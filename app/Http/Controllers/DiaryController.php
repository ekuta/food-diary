<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Enums\MealType;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($date)
    {
        $date = date('Y-m-d', strtotime($date));

        $foods = Diary::where('date', $date)
            ->orderBy('meal_type')->orderBy('recipe_id')->orderBy('id')->get();
        $result = [];
        foreach (MealType::cases() as $type) {
            $result[] = [$type->name => ['calory' => 0, 'items' => []]];
        }
        foreach ($foods as $food) {
            $result[$food->meal_type->name]['calory'] += $food->calory;
            $result[$food->meal_type->name]['items'][] = $food->toArray();
        }
        return $this->responseSuccess($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Diary $diary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diary $diary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Diary $diary)
    {
        //
    }
}
