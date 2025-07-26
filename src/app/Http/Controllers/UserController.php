<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\Message;
use App\Models\Order;
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

        // 出品者 or 購入者として関わっている注文
        $tradingOrders = Order::where(function ($q) use ($user) {
                    $q->whereHas('item', fn($q) => $q->where('user_id', $user->id))
                        ->orWhere('user_id', $user->id);
                })
                ->with(['item', 'chatRoom.messages'])
                ->get();

        // 未読チャットメッセージ数（自分が送信したものを除く）を表示
        $unreadMessageCount = Message::whereHas('chatRoom.order', function ($q) use ($user) {
                $q->whereHas('item', fn($q) => $q->where('user_id', $user->id))
                ->orWhere('user_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        // タブ表示設定
        $displayItems = match ($tab) {
            'sell' => $user->items->map(fn($item) => [
                'item' => $item,
                'link' => route('item.detail', ['item_id' => $item->id]),
            ]),

            'buy' => $user->orders()->with('item')->get()->map(fn($order) => [
                'item' => $order->item,
                'link' => route('item.detail', ['item_id' => $order->item->id]),
            ]),

            'trading' => $tradingOrders
                ->filter(fn($order) => $order->item && $order->chatRoom)
                ->sortByDesc(fn($order) => optional($order->chatRoom->messages->last())->created_at)
                ->values()
                ->map(fn($order) => [
                    'item' => $order->item,
                    'link' => route('chat.show', ['chatRoom' => $order->chatRoom->id]),
                ]),

            default => collect(),
        };

        return view('profile.mypage', compact('user', 'profileImage', 'tab', 'unreadMessageCount', 'displayItems'));
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
