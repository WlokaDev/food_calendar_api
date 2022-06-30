<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read int $id
 * @property string $email
 * @property ?string $password
 * @property ?string $name
 * @property ?Carbon $email_verified_at
 * @property-read Carbon $created_at;
 * @property-read Carbon $updated_at;
 * @property-read ?UserPreference $preferences
 * @method static find(string|array $id)
 * @method static where(string $string, int|string $email)
 */

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function preferences() : HasOne
    {
        return $this->hasOne(UserPreference::class, 'user_id');
    }

    public function excludedProducts() : BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'users_excluded_products');
    }

    public function recipeCalendar() : HasMany
    {
        return $this->hasMany(RecipeCalendar::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function scopeWherePasswordResetToken(Builder $query, string $email, string $token): Builder|null
    {
        if (DB::table('password_resets')->where('email', $email)->where('token', $token)->where('password_reset_token_expires_at', '>=', now())->exists())
            return $query->where('email', $email);
        return null;

    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions()->toArray(), true);
    }

    public function hasPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;

    }

    public function shoppingLists() : HasMany
    {
        return $this->hasMany(ShoppingList::class);
    }
}
