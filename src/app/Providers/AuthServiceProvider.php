<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\ChatRoom::class => \App\Policies\ChatRoomPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $username = $notifiable->username;

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('メールアドレスの確認')
                ->greeting($username . '様')
                ->line('以下のボタンをクリックして、メールアドレスの確認を完了してください。')
                ->action('メールアドレスを認証する', $url)
                ->line('もしアカウントを作成していない場合は、このメールを無視してください。');
        });
    }
}
