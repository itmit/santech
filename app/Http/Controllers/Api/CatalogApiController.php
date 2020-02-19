<?php

namespace App\Http\Controllers\Api;

use App\Models\Catalog;
use App\Models\Category;
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
        return $this->sendResponse(Catalog::all()->toArray(), 'Catalog list');
    }

    public function getCategoriesByCatalog(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|exists:catalogs',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        $catalog = Catalog::where('uuid', $request->uuid)->first('id')->id;
        return $this->sendResponse(Category::where('catalog_id', $catalog)->get()->toArray(), 'Category list');
    }
    
    
}
