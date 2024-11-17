<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function store(Request $request)
    {
        $profileData = app(ProfileRequest::class)->validated();
        $addressData = app(AddressRequest::class)->validated();

        $user = User::create([
            'username' => $profileData['username'],
        ]);

        UserAddress::create([
            'user_id' => $user->id,
            'postal_code' => $addressData['postal_code'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
        ]);

        return redirect()->route('home');
    }
}
