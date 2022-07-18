<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    //

    public function Rigister(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] =$user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'user register Successfuly'
        ];
        $user->assignRole('teacher');
        return response()->json($response, 200);
    }
    public function Login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =$user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            $success['id'] = $user->id;
    
            $response = [
                'success' => true,
                'data' => $success,
                'message' => 'user Login Successfuly'
            ];
            // $role = Role::create(['name' => 'admin']);
            // $permission = Permission::create(['name' => 'write articles']);

            // $role = Role::findById(1);
            // $permission = Permission::create(['name' => 'edit articles']);
            // $role->givePermissionTo($permission);
            
            return response()->json($response, 200);

        }else{
            $response = [
                'success' => false,
                'message' => 'unathentecated'
            ];
            return response()->json($response, 200);
        }

    }
    public function Alluser(){
        $users = User::all();
        return response()->json(array('users' => $users));
    }
}
