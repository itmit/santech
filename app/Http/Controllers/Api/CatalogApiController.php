<?php

namespace App\Http\Controllers\Api;

use App\Models\Catalog;
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

    public function getCatalog()
    {
        return $this->sendResponse(Catalog::all()->toArray());
    }
    
}
