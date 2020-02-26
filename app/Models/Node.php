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

    public function getItems()
    {
        // $this->hasMany(NodeItem::class, 'node_id')
        // ->join('items', 'node_items.item_id', '=', 'items.id')
        // ->select('items.name', 'node_items.count', 'node_items.amount')
        // ->get()
        // ->toArray();

        $this->hasMany(NodeItem::class, 'id')->get();
    }
}
