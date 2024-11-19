<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\User;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function showStoreForm()
    {
        $userId = Auth::user();
        // if (!$userId) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $username = $user->username ?? '';

        return view('profile.index', compact('username'));
    }

    public function store(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        $user = Auth::user();
        if (!$user || !$user instanceof User) {
            abort(403, 'Unauthorized action.');
        }

        // プロフィール画像の保存
        if ($profileRequest->hasFile('profile_image')) {
            // 既存の画像を削除
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // 新しい画像を保存
            $profileImagePath = $profileRequest->file('profile_image')->store('profile_images', 'public');
        } else {
            $profileImagePath = $user->profile_image;
        }

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
