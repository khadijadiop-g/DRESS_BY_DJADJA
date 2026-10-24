<?php
namespace App\Repositories;
use \App\Models\Product;
class ProductRepository{
        // damay dieul product yeup;

    public function all(){
        // $products=Product::orderBy('id')->get();
        // return array_map(fn($product) => $product->toCatalogArray(),$products);
        return $products = Product::orderBy('id')->get()->map(fn($product) => $product->toCatalogArray());

    }
    // damay dieyl produts par id yi
    public function findById(int $id){
        return Product::find($id)->get();
    }
    public function getActive(){
        return Product::where('active',true)->get();
        
    }
}