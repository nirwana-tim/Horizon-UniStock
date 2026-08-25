<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'must_change_password', 'is_active', 'last_login_at', 'plain_password'];

    protected $hidden = ['password', 'remember_token', 'plain_password'];

    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function getDecryptedPasswordAttribute(): ?string
    {
        if (! $this->plain_password) {
            return null;
        }

        try {
            return Crypt::decryptString($this->plain_password);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
