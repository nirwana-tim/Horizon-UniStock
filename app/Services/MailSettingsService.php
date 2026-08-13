<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class MailSettingsService
{
    private const MAILERS = [
        'smtp' => 'SMTP',
        'sendmail' => 'Sendmail',
        'ses' => 'Amazon SES',
        'postmark' => 'Postmark',
        'resend' => 'Resend',
        'log' => 'Log (Debug)',
    ];

    private const API_DRIVERS = ['ses', 'postmark', 'resend'];

    private const VERIFIABLE_MAILERS = ['smtp', 'ses', 'postmark', 'resend'];

    public static function mailers(): array
    {
        return self::MAILERS;
    }

    public static function apiDrivers(): array
    {
        return self::API_DRIVERS;
    }

    /**
     * Whether the given mailer actually delivers to recipients, so an OTP
     * verification is meaningful. "log" and "sendmail" don't send real emails.
     */
    public static function isVerifiable(string $mailer): bool
    {
        return in_array($mailer, self::VERIFIABLE_MAILERS, true);
    }

    /**
     * Compute all mail config from an attribute array.
     */
    public static function resolve(array $attr): array
    {
        $mailer   = $attr['mailer'] ?? 'log';
        $fromAddr = $attr['from_address'] ?? config('mail.from.address');
        $fromName = $attr['from_name'] ?? config('mail.from.name', config('app.name'));

        // --- Mailer transport config ---
        $mailerConfig = match ($mailer) {
            'smtp' => [
                'transport'    => 'smtp',
                'scheme'       => match ($attr['scheme'] ?? null) {
                    'ssl'  => 'smtps',
                    'tls'  => 'smtp',
                    default => null,
                },
                'host'         => $attr['host'] ?? '127.0.0.1',
                'port'         => $attr['port'] ?? 587,
                'username'     => $attr['username'] ?? null,
                'password'     => $attr['password'] ?? null,
                'timeout'      => null,
                'local_domain' => parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST),
            ],
            'ses' => [
                'transport' => 'ses',
            ],
            'postmark' => [
                'transport' => 'postmark',
            ],
            'resend' => [
                'transport' => 'resend',
            ],
            'sendmail' => [
                'transport' => 'sendmail',
                'path'      => $attr['host'] ?? '/usr/sbin/sendmail -bs -i',
            ],
            'log' => [
                'transport' => 'log',
                'channel'   => $attr['username'] ?? 'mail',
            ],
            default => [
                'transport' => $mailer,
            ],
        };

        // --- Services config (for API drivers) ---
        $servicesConfig = match ($mailer) {
            'ses' => [
                'key'    => $attr['api_key'] ?? null,
                'secret' => $attr['api_secret'] ?? null,
                'region' => $attr['api_region'] ?? 'us-east-1',
            ],
            'postmark' => [
                'key' => $attr['api_key'] ?? null,
            ],
            'resend' => [
                'key' => $attr['api_key'] ?? null,
            ],
            default => [],
        };

        $from = [
            'address' => $fromAddr,
            'name'    => $fromName,
        ];

        return compact('mailer', 'mailerConfig', 'servicesConfig', 'from');
    }

    /**
     * Write config into the running Laravel application.
     */
    public static function apply(array $attr): void
    {
        $resolved = self::resolve($attr);

        Config::set('mail.default', $resolved['mailer']);
        Config::set("mail.mailers.{$resolved['mailer']}", $resolved['mailerConfig']);
        Config::set('mail.from', $resolved['from']);

        // API driver credentials live in config/services.php
        foreach ($resolved['servicesConfig'] as $option => $value) {
            Config::set("services.{$resolved['mailer']}.{$option}", $value);
        }
    }

    /**
     * Build config attributes from a request, falling back to existing DB secrets.
     * Used by test() to build config from unsaved form values.
     */
    public static function buildFromRequest(Request $request, ?object $existing): array
    {
        $data = self::validateConfig($request);

        // Fall back: if password/api_key empty, use DB secret
        if (empty($data['password']) && $existing && !empty($existing->password)) {
            $data['password'] = $existing->password;
        }
        if (empty($data['api_key']) && $existing && !empty($existing->api_key)) {
            $data['api_key'] = $existing->api_key;
        }

        return $data;
    }

    /**
     * Generate a random 4-digit verification code.
     */
    public static function generateOtp(): string
    {
        return (string) random_int(1000, 9999);
    }

    /**
     * One-way hash of the OTP for safe session storage.
     */
    public static function otpHashed(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    /**
     * Whether resend is allowed given the last send timestamp (seconds ago).
     */
    public static function canResend(int $sentAt, int $cooldown = 30): bool
    {
        return (now()->getTimestamp() - $sentAt) >= $cooldown;
    }

    /**
     * Deterministic hash of a config array for session verification.
     * Includes secrets so changed credentials invalidate prior test.
     */
    public static function configHash(array $attr): string
    {
        // Sort keys for determinism
        ksort($attr);
        return md5(json_encode($attr, JSON_THROW_ON_ERROR));
    }

    /**
     * Validate form input from the settings page and map to DB-ready attributes.
     * Unique per-driver field names → canonical DB columns.
     */
    public static function validateConfig(Request $request): array
    {
        $validKeys = implode(',', array_keys(self::MAILERS));

        $validated = $request->validate([
            'mailer'        => ['required', 'string', "in:{$validKeys}"],
            'smtp_scheme'   => ['nullable', 'in:tls,ssl'],
            'smtp_port'     => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_host'     => ['nullable', 'string'],
            'smtp_username' => ['nullable', 'string'],
            'smtp_password' => ['nullable', 'string'],
            'sendmail_path' => ['nullable', 'string'],
            'log_channel'   => ['nullable', 'string'],
            'api_key'       => ['nullable', 'string'],
            'from_address'  => ['required', 'email'],
            'from_name'     => ['nullable', 'string'],
        ]);

        $mailer = $validated['mailer'];

        // Per-driver extra rules
        $extra = match ($mailer) {
            'smtp' => [
                'smtp_host' => ['required', 'string'],
                'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            ],
            'ses', 'postmark', 'resend' => [
                'api_key' => ['required', 'string'],
            ],
            default => [],
        };

        if ($extra) {
            $request->validate($extra);
        }

        // Map unique form names → DB column names
        $mapped = match ($mailer) {
            'smtp' => [
                'scheme'   => $validated['smtp_scheme'] ?? null,
                'host'     => $validated['smtp_host'] ?? null,
                'port'     => $validated['smtp_port'] ?? null,
                'username' => $validated['smtp_username'] ?? null,
                'password' => $validated['smtp_password'] ?? null,
            ],
            'sendmail' => [
                'host' => $validated['sendmail_path'] ?? null,
            ],
            'log' => [
                'username' => $validated['log_channel'] ?? null,
            ],
            default => [],
        };

        $data = [
            'mailer'       => $mailer,
            'scheme'       => $mapped['scheme'] ?? null,
            'host'         => $mapped['host'] ?? null,
            'port'         => $mapped['port'] ?? null,
            'username'     => $mapped['username'] ?? null,
            'password'     => $mapped['password'] ?? null,
            'api_key'      => $validated['api_key'] ?? null,
            'from_address' => $validated['from_address'],
            'from_name'    => $validated['from_name'],
        ];

        // Preserve secrets when empty (don't overwrite with blank)
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if (empty($data['api_key'])) {
            unset($data['api_key']);
        }

        return $data;
    }
}
