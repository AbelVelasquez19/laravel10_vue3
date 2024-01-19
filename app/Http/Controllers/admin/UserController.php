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
        $user = User::create([
            'name'=>request('name'),
            'email'=>request('email'),
            'password'=>bcrypt(request('password')),
        ]);
        return $user;
    }
}
