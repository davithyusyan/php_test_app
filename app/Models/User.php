<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static paginate(int $count)
 * @method static where(string $string, int $id)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuids;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_admin',
        'name',
        'login',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'user_id', 'id');
    }

    public function findForPassport($username) {
        return $this->where('login', $username)->first();
    }
}
