<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ThesisResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', $this->token, false));

        return (new MailMessage)
            ->subject('Reset Password Thesis App FIKOM UMI')
            ->greeting('Halo,')
            ->line('Kami menerima permintaan reset password untuk akun Thesis App FIKOM UMI.')
            ->line('Klik tombol berikut untuk membuat password baru.')
            ->action('Reset Password', $url)
            ->line('Link reset password berlaku selama 60 menit.')
            ->line('Abaikan email ini jika Anda tidak meminta reset password.');
    }
}
