<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $url = str_replace('api/email/verify', 'verify-email', $url);
            return (new MailMessage)
                ->subject('メールアドレスのご確認')
                ->greeting('こんにちは、' . $notifiable->name . 'さん')
                ->line('ご登録いただいたメールアドレスを確認します。')
                ->line('下記へアクセスし、登録を完了してください。')
                ->action('メールアドレスの認証', $url);
        });
    }
}
