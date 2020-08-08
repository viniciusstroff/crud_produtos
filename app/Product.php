<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Product extends Model
{
    use Userstamps;

    const CREATED_BY = 'created_by';
    protected $fillable = ['name', 'price', 'created_by'];


    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

}
