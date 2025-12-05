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
            $result[$type->value][] = ['recipe' => ['recipe_id' => 0], 'items' => []];
        }
        $index = 0;
        foreach ($items as $item) {
            if ($item->food_id == 0) {
                $index ++;
                $result[$item->meal_type->value][$index]['recipe'] = $item->toArray();
                $result[$item->meal_type->value][$index]['recipe']['recipe_id'] = $item->recipe_id;
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
        $params = $request->post();
        Log::info("store: " . var_export($params, true));
        $user_id = Auth::id();
        $date = date('Y-m-d', strtotime($params['date']));
        $meal_type = $params['meal_type'];
        $recipe = $params['recipe'];
        $recipe_id = $recipe['recipe_id'];
        $items = $params['items'];
        if ($recipe_id) {
            $recipe['food_id'] = 0;
            array_unshift($items, $recipe);
        } else {
            $results['recipe'] = $recipe;
        }

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                if (isset($item['id'])) {
                    $result = $item;
                } else {
                    $item['user_id'] = $user_id;
                    $item['date'] = $date;
                    $item['meal_type'] = $meal_type;
                    $item['recipe_id'] = $recipe_id;
                    $diary = Diary::create($item);
                    $result = $diary->toArray();
                    if (empty($result['food_id'])) {
                        $result['recipe_id'] = $recipe_id;
                    }
                }
                if ($result['food_id']) {
                    $results['items'][] = $result;
                } else {
                    $results['recipe'] = $result;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Exception: $e");
            return $this->responseFailure($e->getMessage());
        }
        return $this->responseSuccess($results);
    }

    /**
     * Display the specified resource.
     */
    public function show(Diary $diary)
    {
        if ($diary->user_id != 0 && Auth::id() != $diary->user_id) {
            return $this->responseFailure('権限エラー');
        }
        $result['recipe'] = $diary->makeHidden('id')->toArray();
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
        if (Auth::id() != $diary->user_id || $diary->id != $params['id']) {
            return $this->responseFailure('権限エラー');
        }
        $diary->update($params);
        return $this->responseSuccess();
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
