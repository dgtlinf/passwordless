<?php

namespace Dgtlinf\Passwordless\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PasswordlessLoginNotification extends Notification
{
    use Queueable;

    /**
     * @var mixed Token model instance defined by user config.
     */
    public $token;

    /**
     * @var string The raw (unhashed) OTP code.
     */
    public string $otp;

    public function __construct($token, string $otp)
    {
        $this->token = $token;
        $this->otp = $otp;
    }

    /**
     * Notification channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $expiresIn = config('passwordless.link.expires_in', 30);
        $routeName = config('passwordless.notification.route');

        $magicLink = $this->buildMagicLink($notifiable, $routeName);

        return (new MailMessage)
            ->subject(__('passwordless::passwordless.subject'))
            ->greeting(__('passwordless::passwordless.greeting', ['name' => $notifiable->name ?? '']))
            ->line(__('passwordless::passwordless.line_1'))
            ->action(__('passwordless::passwordless.action_text'), $magicLink)
            ->line(__('passwordless::passwordless.line_2', ['otp' => $this->otp]))
            ->line(__('passwordless::passwordless.line_3', ['minutes' => $expiresIn]))
            ->line(__('passwordless::passwordless.line_4'));
    }

    /**
     * Build a signed or plain magic link.
     */
    protected function buildMagicLink($notifiable, ?string $routeName = null): string
    {
        $route = $routeName ?? 'passwordless.magic';
        $tokenValue = $this->token->token;

        if (Str::startsWith($route, ['http://', 'https://'])) {
            return Str::of($route)
                ->replace(':token', $tokenValue)
                ->replace(':email', $notifiable->getEmailForPasswordless())
                ->value();
        }

        return URL::temporarySignedRoute(
            $route,
            now()->addMinutes(config('passwordless.link.expires_in', 30)),
            [
                'token' => $tokenValue,
                'email' => $notifiable->getEmailForPasswordless(),
            ]
        );
    }
}
