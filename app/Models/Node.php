<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use SoftDeletes;
    
    protected $table = 'nodes';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
