<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as OrchidUser;
use Orchid\Screen\AsSource;
use Orchid\Support\Facades\Dashboard;

class User extends OrchidUser
{
    use HasFactory, Notifiable, AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'email' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

    /**
     * Check if user has specific role by name
     * Helper method with explicit string type
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->inRole($roleName);
    }

    /**
     * Get user's role names
     *
     * @return array
     */
    public function getRoleNames(): array
    {
        return collect($this->permissions ?? [])->keys()->toArray();
    }

    /**
     * Check if user can perform specific permission
     * Maintains compatibility with Spatie Permission syntax
     *
     * @param string $permission
     * @return bool
     */
    public function can($permission, $arguments = []): bool
    {
        return $this->hasAccess($permission);
    }

    /**
     * Assign role to user (compatible with Orchid)
     * 
     * @param \Orchid\Access\RoleInterface|string $role
     * @return \Orchid\Platform\Models\User
     */
    public function assignRole($role): \Orchid\Platform\Models\User
    {
        $roleName = is_string($role) ? $role : $role->getSlug();
        $rolePermissions = $this->getRolePermissions($roleName);
        
        $currentPermissions = $this->permissions ?? [];
        $currentPermissions[$roleName] = 1;
        
        // Merge role permissions
        foreach ($rolePermissions as $permission) {
            $currentPermissions[$permission] = 1;
        }
        
        $this->permissions = $currentPermissions;
        $this->save();
        
        return $this;
    }

    /**
     * Assign role by name (helper method)
     *
     * @param string $roleName
     * @return void
     */
    public function assignRoleByName(string $roleName): void
    {
        $this->assignRole($roleName);
    }

    /**
     * Remove role from user (compatible with Orchid)
     * 
     * @param \Orchid\Access\RoleInterface|string $role
     * @return int
     */
    public function removeRole($role): int
    {
        $roleName = is_string($role) ? $role : $role->getSlug();
        $permissions = $this->permissions ?? [];
        
        if (!isset($permissions[$roleName])) {
            return 0; // Role not found
        }
        
        unset($permissions[$roleName]);
        
        // Remove role-specific permissions
        $rolePermissions = $this->getRolePermissions($roleName);
        foreach ($rolePermissions as $permission) {
            unset($permissions[$permission]);
        }
        
        $this->permissions = $permissions;
        $this->save();
        
        return 1; // Success
    }

    /**
     * Remove role by name (helper method)
     *
     * @param string $roleName
     * @return void
     */
    public function removeRoleByName(string $roleName): void
    {
        $this->removeRole($roleName);
    }

    /**
     * Get permissions for a specific role
     *
     * @param string $roleName
     * @return array
     */
    private function getRolePermissions(string $roleName): array
    {
        $rolePermissions = [
            'super-admin' => [
                // Platform Access (مطلوب للدخول للداشبورد)
                'platform.index',
                'platform.systems.index',
                // Stories
                'list stories',
                'show stories', 
                'create stories',
                'update stories',
                'delete stories',
                'publish stories',
                'unpublish stories',
                // Categories
                'list categories',
                'show categories',
                'create categories',
                'update categories', 
                'delete categories',
                // Tags
                'list tags',
                'show tags',
                'create tags',
                'update tags',
                'delete tags',
                // Users
                'list users',
                'show users',
                'create users',
                'update users',
                'delete users',
                // Roles
                'list roles',
                'show roles',
                'create roles',
                'update roles',
                'delete roles',
                // Members
                'list members',
                'show members',
                'create members',
                'update members',
                'delete members',
                'activate members',
                'suspend members',
                // Roles & Permissions
                'list roles',
                'show roles',
                'create roles', 
                'update roles',
                'delete roles',
                'list permissions',
                'show permissions',
                // Analytics
                'view analytics',
                'view member analytics',
                'view story analytics',
                'export analytics',
                // System
                'view logs',
                'manage settings',
                'backup system',
                'restore system',
            ],
            'admin' => [
                // Platform Access
                'platform.index',
                'platform.systems.index',
                // Stories
                'list stories',
                'show stories',
                'create stories',
                'update stories',
                'delete stories',
                'publish stories',
                'unpublish stories',
                // Categories
                'list categories',
                'show categories',
                'create categories',
                'update categories',
                'delete categories',
                // Tags
                'list tags',
                'show tags',
                'create tags',
                'update tags',
                'delete tags',
                // Users
                'list users',
                'show users',
                'create users',
                'update users',
                // Members
                'list members',
                'show members',
                'activate members',
                'suspend members',
                // Analytics
                'view analytics',
                'view member analytics',
                'view story analytics',
            ],
            'editor' => [
                'platform.index',
                'list stories',
                'show stories',
                'create stories',
                'update stories',
                'publish stories',
                'unpublish stories',
                'list categories',
                'show categories',
                'list tags',
                'show tags',
                'create tags',
                'update tags',
                'view story analytics',
            ],
            'author' => [
                'platform.index',
                'list stories',
                'show stories',
                'create stories',
                'update stories',
                'list categories',
                'show categories',
                'list tags',
                'show tags',
            ],
            'viewer' => [
                'platform.index',
                'list stories',
                'show stories',
                'list categories',
                'show categories',
                'list tags',
                'show tags',
                'view analytics',
            ],
        ];

        return $rolePermissions[$roleName] ?? [];
    }

    /**
     * Check if user has specific role
     * Compatible with Orchid's inRole method signature
     *
     * @param mixed $role
     * @return bool
     */
    public function inRole($role): bool
    {
        $roleName = is_string($role) ? $role : (string) $role;
        $permissions = $this->permissions ?? [];
        return isset($permissions[$roleName]);
    }

    /**
     * Get the user's primary role
     *
     * @return string|null
     */
    public function getPrimaryRole(): ?string
    {
        $permissions = $this->permissions ?? [];
        $roles = ['super-admin', 'admin', 'editor', 'author', 'viewer'];
        
        foreach ($roles as $role) {
            if (isset($permissions[$role])) {
                return $role;
            }
        }
        
        return null;
    }
}