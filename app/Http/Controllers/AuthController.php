<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {
            $validated= $this->validate($request,[
                'nama'=>'required|max:255',
                'username'=> 'required|max:255|unique:users,username',
                'password'=> 'required',
                'akses'=>'required|in:superadmin,pegawai'
            ]);
            $user = new User();
            $user->nama = $validated['nama'];
            $user->username = $validated['username'];
            $user->password = Hash::make($validated['password']);
            $user->akses = $validated['akses'];
            $user->save();
            return response()->json($user,201);
    }

    public function login(Request $request)
    {
            $validated= $this->validate($request,[
               
                'username'=> 'required|exists:users,username',
                'password'=> 'required'
                
            ]);
            $user= User::where('username',$validated['username'])->first();
            if(!Hash::check($validated['password'],$user->password))
            {
                return response()->json(['message'=>'data tidak ditemukan'],401);
            }
            $payload=[
                    'iat'=> intval(microtime(true)),
                    'exp'=> intval(microtime(true))+ (60 * 60 * 2000),
                    'uid'=> $user->id,
                    'akses'=>$user->akses
            ];
            $token = JWT::encode($payload, env('JWT_SECRET'),'HS256');
            return response()->json(['access_token'=>$token, 'data'=>$payload]);
            // return response()->json($validated);
          
    }
}
