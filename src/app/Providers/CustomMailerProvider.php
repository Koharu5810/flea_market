<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class CustomMailerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend('mailer', function ($app) {
            $dsn = sprintf(
                'smtp://%s:%s@%s:%d',
                config('mail.username'),
                config('mail.password'),
                config('mail.host'),
                config('mail.port')
            );

            $transport = Transport::fromDsn($dsn);

            return new Mailer($transport);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
