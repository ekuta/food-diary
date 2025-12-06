<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Enums\MealType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiaryController extends Controller
{
    const MEMBERS = ['food_id', 'servings', 'name', 'amount', 'unit', 'calory', 'protein', 'fat', 'carbs', 'salt'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $date = date('Y-m-d', strtotime($request->get('date')));

        $items = Diary::where('user_id', $user_id)->where('date', $date)
            ->orderBy('meal_type')->orderBy('recipe_id')->orderBy('id')->get();
        $result = [];
        foreach (MealType::cases() as $type) {
            $result[$type->value][] = ['recipe_id' => 0, 'date' => $date, 'meal_type' => $type->value, 'items' => []];
        }
        $index = 0;
        foreach ($items as $item) {
            if ($item->food_id == 0) {
                $index ++;
                $result[$item->meal_type->value][$index] = $item->makeVisible(Diary::RECIPE_VISIBLE)->toArray();
            } else {
                $result[$item->meal_type->value][$index]['items'][] = $item->toArray();
            }
        }
        return $this->responseSuccess($result);
    }

    public function history()
    {
        $user_id = Auth::id();
         $result['recipes'] = Diary::where('user_id', $user_id)->where('food_id', 0)->latest('updated_at')
            ->limit(config('app.max_history_result'))->get()->makeVisible(['date', 'meal_type'])->toArray();
        $items = Diary::where('user_id', $user_id)->whereNot('food_id', 0)->latest('updated_at')
            ->limit(config('app.max_history_result'))->get()->makeVisible('meal_type')->toArray();
        $exists = [];
        Log::info("history: " . var_export($items, true));
        $result['items'] = [];
        foreach ($items as $item) {
            if (isset($exists[$item['food_id']])) {
                continue;
            }
            $result['items'][] = $item;
            $exists[$item['food_id']] = true;
        }
        return $this->responseSuccess($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $recipe = $request->post();
        Log::info("store: " . var_export($recipe, true));
        $user_id = Auth::id();
        $date = date('Y-m-d', strtotime($recipe['date']));
        $meal_type = $recipe['meal_type'];
        $recipe_id = $recipe['recipe_id'];
        $items = $recipe['items'];
        unset($recipe['items']);

        $exists = Diary::where('user_id', $user_id)->where('date', $date)->where('meal_type', $meal_type)
            ->where('recipe_id', $recipe_id)->exists();
        if ($exists) {
            return $this->responseFailure("登録済みです");
        }

        DB::beginTransaction();
        try {
            if ($recipe_id) {
                $recipe['user_id'] = $user_id;
                $result = Diary::create($recipe)->toArray();
            } else {
                $recipe['food_id'] = 0;
                $result = $recipe;
            }
            $result['items'] = [];
            foreach ($items as $item) {
                $item['user_id'] = $user_id;
                $item['date'] = $date;
                $item['meal_type'] = $meal_type;
                $item['recipe_id'] = $recipe_id;
                $result['items'][] = Diary::create($item)->toArray();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception: $e");
            return $this->responseFailure($e->getMessage());
        }
        return $this->responseSuccess($result);
    }

    /**
     * Used to add new recipe from history of diary
     */
    public function show(Diary $diary)
    {
        if ($diary->user_id != 0 && Auth::id() != $diary->user_id) {
            return $this->responseFailure('権限エラー');
        }
        $result = $diary->makeHidden('id')->toArray();
        $items = Diary::where('user_id', $diary->user_id)->where('date', $diary->date)
            ->where('meal_type', $diary->meal_type) ->where('recipe_id', $diary->recipe_id)->whereNot('food_id', 0)
            ->get();
        $result['items'] = $items->makeHidden('id')->toArray();
        return $this->responseSuccess($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diary $diary)
    {
        $params = $request->post();
        Log::info("update: " . var_export($params, true));
        if (Auth::id() != $diary->user_id || $diary->id != $params['id']) {
            return $this->responseFailure('権限エラー');
        }

        if ($diary->food_id) {
            $diary->update($params);
            return $this->responseSuccess();
        }

        $items = $params['items'];
        unset($params['items']);
        $targets = Diary::where('user_id', $diary->user_id)->where('date', $diary->date)
            ->where('meal_type', $diary->meal_type)->where('recipe_id', $diary->recipe_id)
            ->whereNot('food_id', 0)->get();
        Log::info("update: current ids: ", $targets->pluck('id')->toArray());

        DB::beginTransaction();
        $result = $params;
        try {
            $diary->update($params);
            foreach ($items as $item) {
                $item['user_id'] = $diary->user_id;
                $item['date'] = $diary->date;
                $item['meal_type'] = $diary->meal_type;
                $item['recipe_id'] = $diary->recipe_id;
                if (empty($item['id'])) {
                    $model = Diary::create($item);
                    $result['items'][] = $model->toArray();
                } else {
                    $target = $targets->first(function ($value) use($item) {
                        return $value->id == $item['id'];
                    });
                    if (is_null($target)) {
                        throw new \Exception("不整合を検出しました。");
                    }
                    $target->update($item);
                    $result['items'][] = $item;
                    $targets = $targets->filter(function ($value) use ($item) {
                        return $value->id != $item['id'];
                    });
                }
            }
            $targets->each(function ($value) {
                $value->delete();
            });
            Log::info("update: deleted ids: ", $targets->pluck('id')->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception: $e");
            return $this->responseFailure($e->getMessage());
        }
        return $this->responseSuccess($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Diary $diary)
    {
        if ($diary->user_id != Auth::id()) {
            return $this->responseFailure('権限エラー');
        }
        if ($diary->food_id) {
            $diary->delete();
        } else {
            Diary::where('user_id', $diary->user_id)->where('date', $diary->date)
                ->where('meal_type', $diary->meal_type)->where('recipe_id', $diary->recipe_id)
                ->delete();
        }
        return $this->responseSuccess();
    }
}
