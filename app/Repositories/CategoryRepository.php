<?php
namespace App\Repositories;
use \App\Models\Category;
class CategoryRepository{
    
    public function getAll(){
        $categories= Category::orderBy('id')->get();
        return array_map(fn($categorie) =>$categorie->toCatalogArray(),$categories->all());

    }


    public function findById(int $id){
        return Category::find($id);
    }

    
}