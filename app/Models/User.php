<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'must_change_password', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

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
        if (! $this->student) {
            return null;
        }

        return "Uniform@{$this->student->nim}";
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
