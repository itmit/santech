<?php

namespace App\Http\Controllers\Api;

use App\Models\Catalog;
use App\Models\Category;
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

class CatalogApiController extends ApiBaseController
{
    public $successStatus = 200;

    public function index()
    {
        return $this->sendResponse(Catalog::select('uuid', 'name', 'photo')->orderBy('name')->get()->toArray(), 'Catalog list');
    }

    public function getCategoriesByCatalog(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|uuid|exists:catalogs',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        $catalog = Catalog::where('uuid', $request->uuid)->first('id')->id;
        return $this->sendResponse(Category::select('uuid', 'name', 'photo')->where('catalog_id', $catalog)->get()->toArray(), 'Category list');
    }

    public function getItemsByCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|uuid|exists:categories',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        $category = Category::where('uuid', $request->uuid)->first('id')->id;
        return $this->sendResponse(Item::select('uuid', 'name', 'photo')->where('category_id', $category)->get()->toArray(), 'Items list');
    }

    public function getItem($uuid)
    {        
        if(!Item::where('uuid', $uuid)->exists()) return response()->json(['error'=>'Такой позиции не существует'], 400);            
        
        return $this->sendResponse(Item::select('id', 'uuid', 'name', 'photo')->where('uuid', $uuid)->first()->toArray(), 'Item');
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        $categories = Category::where('name', 'LIKE', "%$request->name%") // 
            ->get(['uuid', 'name', 'photo'])->toArray();

        return $this->sendResponse($categories, 'Categories');
    }
}
