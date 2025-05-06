<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request) {}


    public function store(Request $request) {
        $validated = $request->validate([
            "name" => "required|string|max:255|unique:categories,name",
            "slug" => "required|string|max:255|unique:categories,slug",
            "description" => "nullable|string|max:255",
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);

    }
    public function show(Request $request) {}
    public function update(Request $request) {}
    public function delete(Request $request) {}
}
