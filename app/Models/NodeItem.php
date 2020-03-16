<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class NodeItem extends Model
{
    use SoftDeletes;
    
    protected $table = 'node_items';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function getItem()
    {
        return $this->belongsTo(Item::class, 'item_id')->first('id')->id;
    }
}
