<?php

namespace App\Http\Controllers\Api;

use App\Models\Node;
use App\Models\NodeItem;
use App\Models\Entity;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NodeApiController extends ApiBaseController
{
    public $successStatus = 200;

    public function index()
    {
        return $this->sendResponse(
            Entity::where('client_id', auth('api')->user()->id)
            ->join('nodes', 'entities.id', '=', 'nodes.entity_id')
            ->select(['nodes.uuid AS uuid', 'nodes.name AS name', 'entities.name AS entity_name'])
            ->get()->toArray(),
            'Entity + node list');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|uuid|exists:entities',
            'name' => 'required|string|min:2|max:191',
            'data' => 'array'
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        $entity = Entity::where('uuid', $request->uuid)->first();

        try {
            DB::transaction(function () use ($request, $entity) {
                $nodeObj = Node::create([
                    'entity_id' => $entity->id,
                    'uuid' => Str::uuid(),
                    'name' => $request->name
                ]);
                foreach ($request->data as $item) {
                    $nodeItm = NodeItem::create([
                        'node_id' => $nodeObj->id,
                        'item_id' => $item['id'],
                        'uuid' => Str::uuid(),
                        'count' => $item['count'],
                        'amount' => $item['amount'],
                        'description' => $item['description'],
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([], 'Node created');
    }

    public function destroy($uuid)
    {
        $node = Node::where('uuid', $uuid)->first();

        try {
            DB::transaction(function () use ($node) {
                $items = NodeItem::where('node_id', $node->id)->delete();
                $node->delete();
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([], 'Node deleted');
    }

    public function destroyItemFromNode($uuid)
    {
        $item = NodeItem::where('uuid', $uuid)->first();

        try {
            DB::transaction(function () use ($uuid) {
                $item = NodeItem::where('uuid', $uuid)->delete();
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([], 'Item deleted from node');
    }

    public function addItemToNode(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid_item' => 'required|uuid|exists:items,uuid',
            'uuid_node' => 'required|uuid|exists:nodes,uuid',
            'count' => 'required',
            'amount' => 'required',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        $node = Node::where('uuid', $request->uuid_node)->first();
        $item = Item::where('uuid', $request->uuid_item)->first();

        NodeItem::create([
            'node_id' => $node->id,
            'item_id' => $item->id,
            'uuid' => Str::uuid(),
            'count' => $request->count,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return $this->sendResponse([], 'Item added');
    }

    public function copyNode(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|uuid|exists:nodes',
            'uuid_to' => 'required|uuid|exists:entities,uuid',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        $entity = Entity::where('uuid', $request->uuid_to)->first();

        $node = Node::where('uuid', $request->uuid)->first();

        $items = NodeItem::where('node_id', $node->id)->get();

        try {
            DB::transaction(function () use ($request, $entity, $node, $items) {
                $nodeObj = Node::create([
                    'entity_id' => $entity->id,
                    'uuid' => Str::uuid(),
                    'name' => $node->name
                ]);
                foreach ($items as $item) {
                    $nodeItm = NodeItem::create([
                        'node_id' => $nodeObj->id,
                        'item_id' => $item->getItem(),
                        'uuid' => Str::uuid(),
                        'count' => $item->count,
                        'amount' => $item->amount,
                        'description' => $item->description,
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([], 'Node copied');
    }
}
