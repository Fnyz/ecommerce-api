<?php

namespace App\Services;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAll()
    {
        // return Category::all();
        return Category::withCount('products')->get();
    }

    public function create(array $data): Category
    {
        // generate slug from name
        $data['slug'] = Str::slug($data['name']);
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        // if name is updated, update slug as well
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
