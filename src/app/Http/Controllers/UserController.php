<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\User;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function showStoreForm()
    {
        $user = Auth::user();

        $username = $user->username ?? '';

        // $username = session('username', '');

        return view('profile.index', compact('username'));
    }

    public function store(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        // 認証ユーザを取得
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // プロフィール画像の保存
        $profileImagePath = $profileRequest->hasFile('profile_image')
            ? $profileRequest->file('profile_image')->store('profile_images','public')
            : $user->profile_image;

        // ユーザ情報の更新
        $user->update([
            'username' => $addressData['username'],
            'profile_image' => $profileImagePath,
        ]);

        // ユーザ住所情報の更新または作成
        UserAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $addressData['postal_code'],
                'address' => $addressData['address'],
                'building' => $addressData['building'],
            ]
        );

        return redirect()->route('home');
    }
}
