<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //Get All Users
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    //User Profile
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    //Store New user and Edit the user detail
    public function store(Request $request)
    {
        try {
            $user_id = $request->id;
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => ['required', Rule::unique('users')->ignore($user_id)],
                'mobile_number' => 'required|digits_between:10,10'
            ]);

            if ($validator->fails()) :
                return response()->json($validator->errors(), 200);
            endif;

            if ($user_id) :
                $user = User::findOrFail($user_id);
            else :
                $user = new User();
            endif;
            $data = $request->all();
            $data['userId'] = "user" . $user->id;
            $user->fill($data);
            $user->save();
            $message = ['succes' => "User Saved Successfuly"];
            return response()->json($message, 200);
        } catch (\Exception $e) {
            $message = ['error' => $e->getMessage()];
            return response()->json($message, 500);
        }
    }

    //Delete User Profile
    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user) :
            $message = ['error' => "User is not exist"];
            return response()->json($message, 200);
        endif;
        $user->delete();
        $message = ['success' => "User deleted successfully"];
        return response()->json($message, 200);
    }
}
