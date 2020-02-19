<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Object extends Model
{
    use SoftDeletes;
    
    protected $table = 'objects';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
