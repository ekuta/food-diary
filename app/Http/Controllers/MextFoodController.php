<?php

namespace App\Http\Controllers;

use App\Models\MextFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MextFoodController extends Controller
{
    const MAX_SEARCH_RESULT = 30;
    /**
     * Store a newly created resource in storage.
     */
    public function search(Request $request)
    {
        $searchString = $request->post('searchString');
        $words = preg_split('/[\p{Z}\p{Cc}]++/u', $searchString);
        Log::info("search:", $words);
        $query = MextFood::query();
        foreach ($words as $word) {
            $query->where('name', 'like', "%{$word}%");
        }
        $foods = $query->limit(self::MAX_SEARCH_RESULT)->get(); 
        return $this->responseSuccess($foods);
    }

    /**
     * Display the specified resource.
     */
    public function show(MextFood $mextFood)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MextFood $mextFood)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MextFood $mextFood)
    {
        //
    }
}
