<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'school_id' => 'required|exists:school,id',
            'phone' => 'required|unique:users,phone|',
            'password' => 'required|string|min:8|confirmed',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if($validator->fails()) {
            return response()->json([
                "msg" => "Validation error",
                "error" => $validator->errors()
            ], 422);
        }
        $profileImage = null;
        if($request->hasFile('profile')) {
            $image = $request->file('profile');
            $profileImage = $image->storeAs('public/profile', $image->hashName());

        }
        $user = new User([
            'name' => $request->name,
            'school_id' => $request->school_id,
            'phone' => $request->phone,
            'profile' => $profileImage,
            'password' => Hash::make($request->password)
        ]);
        if ($user->save()) {
            return response()->json([
                "msg" => "Data berhasil disimpan",
                "data" => $user
            ], 200);
        } else {
            return response()->json([
                "msg" => "Terjadi kesalahan saat menyimpan data"
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:5',
            'password' => 'required|string|min:8',
        ]);
        if($validator->fails()) {
            return response()->json([
                "msg" => "Validation error",
                "error" => $validator->errors()
            ], 422);
        }
        $credentials = $request->only('phone', 'password');
        if (Auth::attempt($credentials)) {
            if($request->filled('device_token')) {
                auth()->user()->update(['device_token' => $request->device_token]);
            }
            $user = User::where('id', Auth::user()->id)->first();
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                "msg" => "Login berhasil",
                'data' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                "msg" => "Phone atau Password salah"
            ], 500);
        }
    }



    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return response()->json([
            'msg' => 'Berhasil Logout'
        ]);
    }


    public function edit(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'school_id' => 'required|exists:school,id',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:5',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "msg" => "Validation error",
                "error" => $validator->errors()
            ], 422);
        }

        $profileImage = $user->profile;

        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $profileImage = $image->storeAs('public/profile', $image->hashName());
        }

        $user->name = $request->name;
        $user->school_id = $request->school_id;
        $user->phone = $request->phone;
        $user->profile = $profileImage;

        if ($user->save()) {
            return response()->json([
                "msg" => "Profil berhasil diperbarui",
                "user" => $user
            ], 200);
        } else {
            return response()->json([
                "msg" => "Terjadi kesalahan saat memperbarui profil"
            ], 500);
        }
    }

}
