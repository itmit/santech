<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use SoftDeletes;
    
    protected $table = 'entities';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
