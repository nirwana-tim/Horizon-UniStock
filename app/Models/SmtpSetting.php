<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class SmtpSetting extends Model
{
    protected $fillable = [
        'mailer',
        'scheme',
        'host',
        'port',
        'username',
        'password',
        'verify_peer',
        'api_key',
        'from_address',
        'from_name',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'is_active' => 'boolean',
            'verify_peer' => 'boolean',
        ];
    }

    // Password: encrypt on set, decrypt on get
    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPasswordAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return null;
        }
    }

    // API Key: encrypt on set, decrypt on get
    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiKeyAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Whether an active mailer configuration has been saved by the super admin.
     * Used to decide whether email features are available across the app.
     */
    public static function isActiveConfigured(): bool
    {
        return static::getActive() !== null;
    }

    public static function current(): ?self
    {
        return static::find(1);
    }

    /**
     * Persist the given attributes as the single active mailer config.
     */
    public static function write(array $attrs, ?int $createdBy = null): self
    {
        static::query()->update(['is_active' => false]);

        $s = static::firstOrCreate(['id' => 1]);
        $s->fill($attrs);
        $s->is_active  = true;
        $s->created_by = $createdBy ?? $s->created_by ?? auth()->id();
        $s->save();

        return $s;
    }
}
