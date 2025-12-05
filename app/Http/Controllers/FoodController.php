<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $searchString = $request->post('searchString');
        $words = preg_split('/[\p{Z}\p{Cc}]++/u', $searchString);
        Log::info("search:", $words);
        $query = Food::query();
        foreach ($words as $word) {
            $query->where('search_string', 'like', "%{$word}%");
        }                                                                                                                       $foods = $query->limit(config('app.max_search_result'))->get();
        return $this->responseSuccess($foods);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params = $request->post();
        $params['user_id'] = Auth::id();
        $str = trim(implode(' ', [$params['name'], $params['alias_names'], $params['maker']]));
        $params['search_string'] = str_replace("  ", " ", $str);
        Food::create($params);
        return $this->responseSuccess();
    }

    /**
     * Display the specified resource.
     */
    public function show(Food $food)
    {
        if ($food->user_id != 0 && Auth::id() != $food->user_id) {
            return $this->responseFailure('権限エラー');
        }
        return $this->responseSuccess($food);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
        ]);
        $params = $request->post();

        if (Auth::id() != $food->user_id || $food->id != $params['id']) {
            return $this->responseFailure('権限エラー');
        }
        $food->update($params);
        return $this->responseSuccess($food);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        //
    }
}
