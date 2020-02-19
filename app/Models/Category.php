<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use SoftDeletes;
    
    protected $table = 'categories';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
