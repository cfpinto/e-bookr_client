<?php

namespace Ebookr\Client\Models;

use Illuminate\Notifications\Notifiable;

/**
 * Ebookr\Client\Models\User
 *
 * @property int $id
 * @property int|null $role_id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property string $password
 * @property string|null $remember_token
 * @property array $settings
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $status
 * @property string|null $deleted_at
 * @property mixed $locale
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \TCG\Voyager\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\TCG\Voyager\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Ebookr\Client\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
