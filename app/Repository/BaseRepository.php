<?php
namespace App\Repository;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseRepository {
    public $table;
    public function __construct(Model $model){
        $this->table = $model;
    }
    
    //Base repo to get all items
    public function getList($skip, $take, $relations, $filter){
        $result = QueryBuilder::for($this->table)
                                ->where('is_deleted', 0)
                                ->allowedFilters($filter);
                                if(!empty($relations)){
                                    $result = $result->with($relations);
                                }
        $totalCount = $result->get()->count();
        $resultData = $result->where('is_deleted', 0)
                        ->take($take)
                        ->skip($skip)
                        ->orderBy('id', 'desc');
        return [
            'totalCount' => $totalCount,
            'items' => $resultData->get()
        ];
    } 
    
    //Base repo to get item by id
    public function getById($id){
        return $this->table->where('is_deleted', 0)->findOrFail($id);
    }

     //Base repo to get item with models by id
    public function getByIdModel($id, $relations){
        return $this->table->where('is_deleted', 0)->with($relations)->findOrFail($id);
    }

    //Base Repo check model if exist
    public function check($id){
        return $this->table->where('is_deleted', 0)->find($id);
    }

    //Base repo to create item
    public function create($data){
        return $this->table->create($data);
    }

    //Base repo to update item 
    public function update($id, $values){
        $item = $this->table->where('is_deleted', 0)->where('id',$id)->firstOrFail();
        $item->update($values);
        return  $item;
    }

    //base repo to soft delete item
    public function softDelete($model){
        return $model->update(['is_deleted' => 1]);
    }
    
    //base repo to delete item
    public function delete($model){
        return $model->delete();
    }
}
