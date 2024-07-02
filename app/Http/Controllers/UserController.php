<?php

namespace App\Http\Controllers;

use App\Helper\ApiResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'school_id' => 'required|exists:school,id',
            'phone' => 'required|min:10|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return ApiResult::Response(422, "Validation error", $validator->errors());
        }
        $profileImage = null;
        if ($request->hasFile('profile')) {
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
            return ApiResult::Response(200, "Data berhasil disimpan", $user);
        } else {
            return ApiResult::Response(500, "Terjadi kesalahan saat menyimpan data", null);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return ApiResult::Response(422, "Validation error", $validator->errors());
        }
        $credentials = $request->only('phone', 'password');
        if (Auth::attempt($credentials)) {
            if ($request->filled('device_token')) {
                auth()->user()->update(['device_token' => $request->device_token]);
            }
            $user = User::where('id', Auth::user()->id)->first();
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                "status" => "200",
                "message" => "Login berhasil",
                'data' => $user,
                'token' => $token
            ], 200);
        } else {
            return ApiResult::Response(500, "Phone atau Password salah", null);
        }
    }


    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return ApiResult::Response(200, 'Berhasil Logout', null);
    }


    public function edit(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|min:3',
            'school_id' => 'nullable|exists:school,id',
            'phone' => 'nullable|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:5',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return ApiResult::Response(422, "Validation error", $validator->errors());
        }

        try {
            if ($request->hasFile('profile')) {
                if ($user->profile != null) {
                    Storage::delete($user->profile);
                }

                $image = $request->file('profile');
                $profileImage = $image->storeAs('public/profile', $image->hashName());
                $user->profile = $profileImage;
            }

            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->filled('school_id')) {
                $user->school_id = $request->school_id;
            }

            if ($request->filled('phone')) {
                $user->phone = $request->phone;
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return ApiResult::Response(200, "Profil berhasil diperbarui", $user);
        } catch (Exception $e) {
            return ApiResult::Response(500, "Terjadi kesalahan saat memperbarui profil", null);
        }
    }

}
