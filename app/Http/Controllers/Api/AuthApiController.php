<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
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

class AuthApiController extends ApiBaseController
{
    public $successStatus = 200;

    private $user;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|unique:clients|email|max:191',
            'name' => 'required|max:191|min:2',
            'password' => 'required|confirmed|min:6',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);
        }

        try {
            DB::transaction(function () use ($request) {
                $this->user = Client::create([
                    'uuid' => Str::uuid(),
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($this->password),
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json(['errors'=>$th], 401);
        }

        Auth::login($this->user);     

        if (Auth::check()) {
            $tokenResult = $this->user->createToken(config('app.name'));
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return $this->sendResponse([
                'access_token' => $tokenResult->accessToken,
                'client' => $this->user,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ],
                'Authorization is successful');
        }
        
        return response()->json(['error'=>'Не удалось авторизоваться'], 401);     
    }

    /** 
     * login api 
     * 
     * @return Response 
     */ 
    public function login(Request $request) { 

        $validator = Validator::make($request->all(), [ 
            // 'phone' => 'required|max:18|min:18',
            'phone' => 'required',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        if(!Client::where('phone', '=', $request->phone)->exists())
        {
            return response()->json(['error'=>'Такого пользователя не существует'], 401); 
        }       

        $client = Client::where('phone', '=', $request->phone)->first();

        if(Hash::check($request->password, $client->password))
        {
            Auth::login($client);
            if (Auth::check()) {
                $tokenResult = $client->createToken(config('app.name'));
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                if($request->device_token)
                {
                    Client::where('id', '=', $client->id)->update([
                        'device_token' => $request->device_token
                    ]);
                }

                return $this->sendResponse([
                    'access_token' => $tokenResult->accessToken,
                    'client' => $client,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString(),
                ],
                    'Authorization is successful');
            }
        }
        else
        {
            return response()->json(['error'=>'Неверный пароль'], 401); 
        }
        return response()->json(['error'=>'Авторизация не удалась'], 401); 
    }

    public function logout(Request $request)
    {
        $isUser = $request->client()->token()->revoke();
        if($isUser){
            $success['message'] = "Successfully logged out.";
            return $this->sendResponse($success);
        }
        else{
            $error = "Something went wrong.";
            return $this->sendResponse($error);
        }
    }
    
}
