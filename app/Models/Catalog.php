<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use SoftDeletes;
    
    protected $table = 'catalogs';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
