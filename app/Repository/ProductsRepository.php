<?php
namespace App\Repository;

use Spatie\QueryBuilder\QueryBuilder;

class ProductsRepository extends BaseRepository {
    //Base repo to get item by id
    public function getProductById($id){
        return $this->table->where('is_deleted', 0)->where('productId', $id)->firstOrFail();
    }

    //Base repo to update item 
    public function updateProduct($id, $values){
        $item = $this->table->where('is_deleted', 0)->where('productId',$id)->firstOrFail();
        $item->update($values);
        return  $item;
    }
}