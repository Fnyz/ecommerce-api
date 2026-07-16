<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Str;
class ProductService
{

    // get all products with filters
    public function getAll(array $filters = [])
    {
        $query = Product::where('is_active', true)->with('category');

        // if category_id exist then filter by category_id
        if(isset($filters['category_id'])){
            $query->where('category_id', $filters['category_id']);
        }

        // if search exist then filter by name
        if(isset($filters['search'])){
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        // if min_price exist then filter by min_price
        if(isset($filters['min_price'])){
            $query->where('price', '>=', $filters['min_price']);
        }

        // if max_price exist then filter by max_price
        if(isset($filters['max_price'])){
            $query->where('price', '<=', $filters['max_price']);
        }

        // if sort_by exist then sort by sort_by
        if(isset($filters['sort_by'])){
            $sortBy = $filters['sort_by'];
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // default sort by created_at desc
            $query->orderBy('created_at', 'desc');
        }

        // return paginated results with per_page filter or default 15
        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Product
    {
        // generate slug from name
        $data['slug'] = Str::slug($data['name']);
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        // if name is updated, update slug as well
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function decreaseStock(Product $product, int $quantity): void
    {
        $product->decrement('stock', $quantity);
    }
}
