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
        return $this->hasMany(NodeItem::class, 'node_id')
        ->join('items', 'node_items.item_id', '=', 'items.id')
        ->select('items.uuid', 'items.name', 'node_items.count', 'node_items.amount', 'node_items.description', 'node_items.node_item_uuid')
        ->orderBy('items.name', 'desc')
        ->get();
    }
}
