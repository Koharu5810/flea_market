<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
// プロフィール画面表示
    public function showMypage(Request $request)
    {
        $user = auth()->user();

        // プロフィール画像表示
        $profileImage = $user->profile_image ? asset('storage/' . $user->profile_image) : null;

        $tab = $request->query('tab', 'sell');

        $items = match ($tab) {
            // 出品した商品タブ
            'sell' => $user->items,

            // 購入した商品タブ
            'buy' => $user->orders()->with('item')->get()->map(fn($order) => $order->item),

            // 取引中の商品（購入者 or 出品者どちらかとして関わっているもの）
            'trading' => \App\Models\Order::whereHas('item', fn($q) =>
                            $q->where('user_id', $user->id)) // 出品者として
                            ->orWhere('user_id', $user->id)     // 購入者として
                            ->with('item')
                            ->get()
                            ->map(fn($order) => $order->item),

            default => collect(),   // デフォルトでは空を返す
        };

        return view('profile.mypage', compact('user', 'profileImage', 'items', 'tab'));
    }

// プロフィール編集画面の表示
    public function showProfileForm()
    {
        // 認証ユーザか確認
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // usersテーブルからデータをデフォルト値付きで取得
        $username = $user->username ?? '';
        $profileImage = $user->profile_image ? asset('storage/' . $user->profile_image) : null;

        // user_addressesテーブルから既存データを取得または空文字を設定（初回登録時）
        $userAddress = $user->user_address ?? (object) [
            'postal_code' => '',
            'address' => '',
            'building' => '',
        ];

        return view('profile.profile_edit', compact('username', 'profileImage', 'userAddress'));
    }

// プロフィール保存（登録または更新）
    public function save(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $profileData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // プロフィール画像の保存
        $profileImagePath = $user->profile_image;  // デフォルトは既存画像
        if ($profileRequest->hasFile('profile_image')) {
            $newImagePath = $profileRequest->file('profile_image')->store('profile_image', 'public');
            // 既存の画像を削除
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // 新しい画像を保存
            $profileImagePath = $newImagePath;
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

        return redirect()->route('profile.mypage');
    }
}
