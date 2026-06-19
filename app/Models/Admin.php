<?php

namespace App\Models;

use App\Support\AdminPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'      => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function actions()
    {
        return $this->hasMany(AdminAction::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function hasAbility(string $ability): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $allowedRoles = AdminPermissions::MAP[$ability] ?? [];
        return in_array($this->role, $allowedRoles);
    }

    public function hasAnyAbility(string ...$abilities): bool
    {
        foreach ($abilities as $ability) {
            if ($this->hasAbility($ability)) {
                return true;
            }
        }
        return false;
    }

    public function roleLabel(): string
    {
        return AdminPermissions::ROLE_LABELS[$this->role]
            ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    public function log(string $action, ?string $targetType = null, ?int $targetId = null, array $meta = []): void
    {
        $this->actions()->create([
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'meta'        => $meta ?: null,
        ]);
    }
}
