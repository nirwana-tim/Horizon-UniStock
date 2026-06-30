# SMTP Mail

## Apa Itu?

Laravel Mail adalah fitur untuk kirim email via SMTP server. Bisa pake Gmail, Mailtrap (testing), SendGrid, atau SMTP lain.

## Fitur yg Terinstall

| Fitur | Untuk Apa |
|-------|-----------|
| Mail Facade | Kirim email via `Mail::to()->send()` |
| Mailables | Class khusus tiap jenis email (notif, OTP, dll) |
| Mailable Template | Blade template untuk body email |
| Markdown Mail | Template email cantik pake Markdown + komponen |
| Attachment | Lampirkan file (PDF, Excel, gambar) |
| Queue Mail | Kirim email di background (biar halaman gak nunggu) |
| Multiple Mailers | Pilih driver per pengiriman (Gmail, SMTP, log) |
| Local Testing | Testing kirim email via "log" driver (simpan di file) |
| Mailtrap Support | Testing email di lingkungan staging |

## 1. Konfigurasi di .env

**Untuk Gmail SMTP:**
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailanda@gmail.com
MAIL_PASSWORD=app_password_16_karakter
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="emailanda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> **Gmail:** butuh App Password (aktifkan 2FA di akun Gmail → buat App Password).

**Untuk Mailtrap (testing):**
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
```

**Untuk Local Testing (tanpa kirim beneran):**
```
MAIL_MAILER=log
```
Email akan disimpan di `storage/logs/laravel.log`.

## 2. Buat Mailable Class

```bash
php artisan make:mail SendCredentialsMail
```

**`app/Mail/SendCredentialsMail.php`:**
```php
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class SendCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password,
        public ?string $attachmentPath = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun Horizon-UniStock Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.send-credentials',
        );
    }

    public function attachments(): array
    {
        if ($this->attachmentPath) {
            return [
                Attachment::fromPath($this->attachmentPath),
            ];
        }
        return [];
    }
}
```

## 3. Blade Template Email

**`resources/views/emails/send-credentials.blade.php`:**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Akun Horizon-UniStock</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Halo {{ $user->name }}!</h2>

    <p>Akun Anda telah dibuat di <strong>Horizon-UniStock</strong>.</p>

    <p>Berikut kredensial login Anda:</p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Password:</strong> {{ $password }}</li>
    </ul>

    <p>Silakan login di: <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
    <p>Jangan lupa ganti password setelah login pertama.</p>

    <hr>
    <p style="color: gray; font-size: 12px;">Email ini dikirim otomatis, jangan dibalas.</p>
</body>
</html>
```

## 4. Kirim Email di Controller

```php
use App\Mail\SendCredentialsMail;
use Illuminate\Support\Facades\Mail;

// Kirim email kredensial ke mahasiswa baru
Mail::to($student->user->email)->send(
    new SendCredentialsMail($student->user, $plainPassword)
);

// Kirim dengan attachment
Mail::to($student->user->email)->send(
    new SendCredentialsMail(
        $student->user,
        $plainPassword,
        storage_path("app/exports/schedule-{$student->id}.pdf")
    )
);
```

## 5. Queue Mail (Background)

```php
// Langsung di-queue (butuh queue worker)
Mail::to($user->email)->queue(
    new SendCredentialsMail($user, $plainPassword)
);

// Kirim agak lambat (5 detik)
Mail::to($user->email)->later(
    now()->addSeconds(5),
    new SendCredentialsMail($user, $plainPassword)
);
```

Jalankan queue worker: `php artisan queue:work`

## 6. Markdown Mail (Template Cantik)

```bash
php artisan make:mail NotificationMail --markdown=emails.notification
```

**`app/Mail/NotificationMail.php`:**
```php
public function content(): Content
{
    return new Content(
        markdown: 'emails.notification',
        with: [
            'greeting' => 'Halo!',
            'body' => 'Jadwal distribusi telah diperbarui.',
            'actionText' => 'Lihat Jadwal',
            'actionUrl' => url('/schedule'),
        ],
    );
}
```

**`resources/views/vendor/mail/html/`** — override template default:
```bash
php artisan vendor:publish --tag=laravel-mail
```

## 7. Kirim via Mailer Berbeda

```php
// Pake mailer spesifik (didefinisikan di config/mail.php)
Mail::mailer('sendgrid')->to($user->email)->send(new NotificationMail(...));
```

## 8. Struktur File Mail

```
app/
├── Mail/                  # Mailable class
│   ├── SendCredentialsMail.php
│   └── NotificationMail.php
└── ...
resources/views/
├── emails/                # Blade template email
│   ├── send-credentials.blade.php
│   └── notification.blade.php
└── vendor/mail/           # Override template default (publish)
    └── html/themes/default.css
```

## Sumber
- https://laravel.com/docs/13.x/mail
- https://mailtrap.io (testing SMTP gratis)
- https://support.google.com/accounts/answer/185833 (Gmail App Password)

## Analogi
SMTP Mail itu seperti kurir — tinggal kasih alamat (to), amplop (Mailable class), isi surat (Blade template), dan lampiran (attachment). Kurir antar sendiri, kita tinggal tunggu konfirmasi.
