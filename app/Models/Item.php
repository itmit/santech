<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use SoftDeletes;
    
    protected $table = 'items';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
