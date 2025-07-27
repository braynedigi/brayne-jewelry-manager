<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'logo',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is distributor
     */
    public function isDistributor(): bool
    {
        return $this->role === 'distributor';
    }

    /**
     * Check if user is factory
     */
    public function isFactory(): bool
    {
        return $this->role === 'factory';
    }

    /**
     * Get the distributor profile for this user
     */
    public function distributor()
    {
        return $this->hasOne(Distributor::class);
    }

    /**
     * Get orders created by this user (if distributor)
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'distributor_id');
    }

    /**
     * Get the logo URL for the user
     */
    public function getLogoUrl(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        // Check if the logo exists in storage
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo)) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    /**
     * Check if the user has a valid logo
     */
    public function hasLogo(): bool
    {
        return $this->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo);
    }
}
