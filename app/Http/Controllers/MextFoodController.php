<?php

namespace App\Http\Controllers;

use App\Models\MextFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MextFoodController extends Controller
{
    const MAX_SEARCH_RESULT = 100;

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
}
