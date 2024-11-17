<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function showStoreForm()
    {
        return view('profile.index');
    }

    public function store(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        // プロフィール画像の保存
        $profileImagePath = null;
        if ($profileRequest->hasFile('profile_image')) {
            $profileImagePath = $profileRequest->file('profile_image')->store('profile_images', 'public');
        }

        $user = User::create([
            'username' => $profileData['username'],
        ]);

        UserAddress::create([
            'user_id' => $user->id,
            'profile_image' => $profileImagePath,
            'postal_code' => $addressData['postal_code'],
            'address' => $addressData['address'],
            'building' => $addressData['building'],
        ]);

        return redirect()->route('home');
    }
}
