<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;

use App\Services\ImgServices;
use App\Services\UserServices;
use App\User;

use App\Exceptions\NotExistedTokenException;

class AuthController extends Controller
{
    private $imgServices;
    private $userServices;
    public function __construct(ImgServices $imgServices,UserServices $userServices){
        $this->imgServices = $imgServices;
        $this->userServices = $userServices;
    }

    public function signup(Request $request)
    {
        /*if ($request->user()->role!='admin'){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401); 
        }*/

        $image_name=$request->username.'_'.time();

        $this->imgServices->save_img($request->image,$image_name,"avatar1",80,60);

        $this->imgServices->save_img($request->image,$image_name,"avatar2",80,60);
        
        $this->userServices->create_user($request->all(),$image_name);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
  
    /**
     * Login user and create token
     *
     */
    public function login(LoginRequest $request)
    {
        $message = $this->userServices->login($request);
        return $message;
    }
  
    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     */
    public function user(Request $request)
    {
        /*if (Storage::disk('public')->exists($request->user()->image)){
            $content = Storage::disk('public')->get($request->user()->image);
        }*/
        return response()->json($request->user());
    }
}
