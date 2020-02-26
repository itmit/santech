<?php

namespace App\Http\Controllers\Api;

use App\Models\Node;
use App\Models\Entity;
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
            ->select(['nodes.uuid AS uuid', 'nodes.name AS node_name', 'entities.name AS entity_name'])
            ->get()->toArray(),
            'Entity + node list');
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [ 
    //         'name' => 'required|string|min:2|max:191',
    //     ]);
        
    //     if ($validator->fails()) { 
    //         return response()->json(['errors'=>$validator->errors()], 401);            
    //     }

    //     try {
    //         DB::transaction(function () use ($request) {
    //             $this->obj = Entity::create([
    //                 'uuid' => Str::uuid(),
    //                 'client_id' => auth('api')->user()->id,
    //                 'name' => $request->name
    //             ]);
    //         });
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }

    //     return $this->sendResponse([$this->obj], 'Entity created');
    // }

    // public function destroy(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [ 
    //         'uuid' => 'required|uuid|exists:entities',
    //     ]);
        
    //     if ($validator->fails()) { 
    //         return response()->json(['errors'=>$validator->errors()], 401);            
    //     }

    //     $obj = Entity::where('uuid', $request->uuid)->delete();

    //     return $this->sendResponse([$obj], 'Entity deleted');
    // }

    // public function addItemToNode(Request $request)
    // {
    //     $validator = Validator::make($uuid, [ 
    //         'uuid_item' => 'required|uuid|exists:items,uuid',
    //         'uuid_node' => 'required|uuid|exists:nodes,uuid',
    //     ]);
        
    //     if ($validator->fails()) { 
    //         return response()->json(['errors'=>$validator->errors()], 401);            
    //     }

    //     $node = Node::where('')

    //     return $this->sendResponse(Item::select('id', 'uuid', 'name', 'photo')->where('uuid', $uuid)->first(), 'Item');
    // }
}
