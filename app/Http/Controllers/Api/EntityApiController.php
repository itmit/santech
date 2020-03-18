<?php

namespace App\Http\Controllers\Api;

use App\Models\Entity;
use App\Models\Node;
use App\Models\NodeItem;
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
use PDF;

class EntityApiController extends ApiBaseController
{
    public $successStatus = 200;
    
    private $obj;

    public function index()
    {
        return $this->sendResponse(Entity::select('uuid', 'name')->where('client_id', auth('api')->user()->id)->get()->toArray(), 'Entity list');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|string|min:2|max:191',
            'data' => 'array',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        try {
            DB::transaction(function () use ($request) {
                $this->obj = Entity::create([
                    'uuid' => Str::uuid(),
                    'client_id' => auth('api')->user()->id,
                    'name' => $request->name
                ]);
                if(!empty($request->data))
                {
                    foreach ($request->data as $node) {
                    $nodeObj = Node::create([
                        'entity_id' => $this->obj->id,
                        'uuid' => Str::uuid(),
                        'name' => $node['name']
                    ]);
                        foreach ($node['items'] as $item) {
                            $nodeItm = NodeItem::create([
                                'node_id' => $nodeObj->id,
                                'item_id' => $item['id'],
                                'uuid' => Str::uuid(),
                                'count' => $item['count'],
                                'amount' => $item['amount'],
                                'description' => $item['Description'],
                            ]);
                        }
                    }
                }
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([$this->obj], 'Entity created');
    }

    public function destroy($uuid)
    {
        $obj = Entity::where('uuid', $uuid)->first();
        $node = Node::where('entity_id', $obj->id)->first();

        try {
            DB::transaction(function () use ($obj, $node) {
                if($node != null)
                {
                    $items = NodeItem::where('node_id', $node->id)->delete();
                    $node->delete();
                }
                $obj->delete();
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([], 'Entity deleted');
    }

    public function show($uuid)
    {
        $entity = Entity::where('uuid', $uuid)->first();

        $nodes = Node::where('entity_id', $entity->id)->get();

        $result = [];

        foreach ($nodes as $node) {
            $items = [];
            foreach ($node->getItems() as $item) {
                $items[] = [
                    'name' => $item->name,
                    'count' => $item->count,
                    'amount' => $item->amount,
                ];
            }
            $result[] = [
                'uuid' => $node->uuid,
                'name' => $node->name,
                'items' => $items
            ];
        };

        return $this->sendResponse($result, "Object's nodes");
    }

    public function edit($uuid)
    {
        $entity = Entity::where('uuid', $uuid)->first(['id', 'uuid', 'name']);

        $nodes = Node::where('entity_id', $entity->id)->get();

        $result = [];

        $result[] = [
            'entity' => $entity,
        ];

        foreach ($nodes as $node) {
            $items = [];
            foreach ($node->getItems() as $item) {
                $items[] = [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                    'count' => $item->count,
                    'amount' => $item->amount,
                    'description' => $item->description
                ];
            }
            $result[] = [
                'uuid' => $node->uuid,
                'name' => $node->name,
                'items' => $items
            ];
        };

        return $this->sendResponse($result, "Object's nodes and items for edit");
    }

    public function update(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|string|min:2|max:191',
            'data' => 'array',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        try {
            DB::transaction(function () use ($request, $uuid) {
                $this->obj = Entity::where('uuid', $uuid)->update([
                    'name' => $request->name
                ]);
                if(!empty($request->data))
                {
                    foreach ($request->data as $node) {
                        if(Node::where('uuid', $node['uuid'])->exists())
                        {
                            $nodeObj = Node::where('uuid', $node['uuid'])->update([
                                'name' => $node['name']
                            ]);
                        }
                        else
                        {
                            $nodeObj = Node::create([
                                'entity_id' => $this->obj->id,
                                'uuid' => Str::uuid(),
                                'name' => $node['name']
                            ]);
                        }
                        foreach ($node['items'] as $item) {
                            if(NodeItem::where('uuid', $item['uuid'])->exists())
                            {
                                $nodeItm = NodeItem::where('uuid', $item['uuid'])->update([
                                    'node_id' => $nodeObj->id,
                                    'item_id' => $item['id'],

                                    'count' => $item['count'],
                                    'amount' => $item['amount'],
                                    'description' => $item['Description'],
                                ]);
                            }
                            else
                            {
                                $nodeItm = NodeItem::create([
                                    'node_id' => $nodeObj->id,
                                    'item_id' => $item['id'],
                                    'uuid' => Str::uuid(),
                                    'count' => $item['count'],
                                    'amount' => $item['amount'],
                                    'description' => $item['Description'],
                                ]);
                            }
                        }
                    }
                }
            });
        } catch (\Throwable $th) {
            return $th;
        }

        return $this->sendResponse([$this->obj], 'Entity created');
    }

    public function getEstimatePDF($uuid)
    {
        $entity = Entity::where('uuid', $uuid)
        ->first();

        $nodes = Node::where('entity_id', $entity->id)
        ->get();

        $estimate = [];
        $total = 0;

        foreach ($nodes as $node) {
            $items = [];
            foreach ($node->getItems() as $item) {
                $items[] = [
                    'name' => $item->name,
                    'count' => $item->count,
                    'amount' => $item->amount,
                    'price' => $item->count * $item->amount,
                ];
                $total = $total + $item->count * $item->amount;
            }
            $estimate = $items;
        };

        $pdf = PDF::loadView('pdf.estimate', ['estimate' => $estimate, 'entity' => $entity, 'total' => $total]);
    
        $pdf->save(storage_path().'/app/public/estimate/'.$uuid.'.pdf');
        $link = 'storage/estimate/'.$uuid.'.pdf';

        return $this->sendResponse([$link], "PDF");
    }

    public function getEstimate($uuid)
    {
        $entity = Entity::where('uuid', $uuid)
        ->first();

        $nodes = Node::where('entity_id', $entity->id)
        ->get();

        $estimate = [];
        $total = 0;

        foreach ($nodes as $node) {
            $items = [];
            foreach ($node->getItems() as $item) {
                $items[] = [
                    'name' => $item->name,
                    'count' => $item->count,
                    'amount' => $item->amount,
                    'price' => $item->count * $item->amount,
                ];
                $total = $total + $item->count * $item->amount;
            }
            $estimate = $items;
        };

        return $this->sendResponse($estimate, "PDF");
    }
}
