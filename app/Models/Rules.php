<?php

namespace App\Models;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Rules extends Model
{
    protected $guarded =[];
    
    protected $hidden =[
        'is_deleted', 'created_at', 'updated_at'
    ];
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
