<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, HasDatabaseNotifications;
    protected $table = 'users';
    protected $guarded =[];

    /**
     * Check if user has in-app notifications enabled
     */
    public function hasAppNotification(): bool
    {
        return (bool) $this->app_notification;
    }

    /**
     * Scope to get users with app notifications enabled
     */
    public function scopeWhereHasAppNotifications($query)
    {
        return $query->where('app_notification', 1);
    }
}