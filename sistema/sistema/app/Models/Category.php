<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description'])]

class Category extends Model
{
    public function medicines(): HasMany
    {

     return $this->hasMany(Medicine::class);
     
    }

}
