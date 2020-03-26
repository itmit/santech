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
                    'password' => Hash::make($request->password),
                ]);
            });
        } catch (\Throwable $th) {
            return $th;
        }

        Auth::login($this->user);     

        if (Auth::check()) {
            $tokenResult = $this->user->createToken(config('app.name'));
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return $this->sendResponse([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
                'client' => $this->user
            ],
                'Authorization is successful');
        }
        
        return response()->json(['error'=>'Не удалось авторизоваться'], 401);     
    }

    public function test()
    {
        return 'success';
    }

    /** 
     * login api 
     * 
     * @return Response 
     */ 
    public function login(Request $request) { 

        $validator = Validator::make($request->all(), [ 
            'email' => 'required|exists:clients',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }    

        $client = Client::where('email', '=', $request->email)->first();

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
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString(),
                    'client' => $client
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

    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email|exists:clients',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        $code = random_int(1000, 9999);
        $message = "Ваш код для сброса пароля: " . $code;

        Client::where('email', $request->email)->update([
            'code' => $code
        ]);

        $message = wordwrap($message, 70, "\r\n");

        mail($request->email, 'СанТех. Сброс пароля', $message);
    }
    
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email|exists:clients',
            'code' => 'required',
            'password' => 'required|confirmed'
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        $client = Client::where('email', $request->email)->first();

        if($request->code == $client->code)
        {
            $client->update([
                'password' => Hash::make($request->password),
                'code' => null
            ]);
        }
        else return response()->json(['error'=>'Wrong code'], 400);     

        return $this->sendResponse([], 'Пароль успешно сменен');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'password' => 'required|confirmed'
        ]);

        Client::where('id', Auth::id())->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->sendResponse([], 'Пароль успешно сменен');
    }
    
}
