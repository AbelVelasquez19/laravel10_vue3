<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function index(){
        $users = User::latest()->get();
        return $users;
    }
    public function store(Request $request){
        request()->validate([
            'email'=>'required|unique:users,email',
        ]);
        $user = User::create([
            'name'=>request('name'),
            'email'=>request('email'),
            'password'=>bcrypt(request('password')),
        ]);
        return $user;
    }
    public function update(Request $request, User $user){
        $user->update([
            'name'=>request('name'),
            'email'=>request('email'),
            'password'=>request('password') ? bcrypt(request('password')) : $user->password,
        ]);
        return $user;
    }
}
