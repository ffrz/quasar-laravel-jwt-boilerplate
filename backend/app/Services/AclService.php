<?php

namespace App\Services;

use App\Models\User;

class AclService
{
    protected array $acl;

    public function __construct()
    {
        $this->acl = config('acl.roles');
    }

    public function getUserPermissions(User $user): array
    {
        return $this->acl[$user->role] ?? [];
    }

    public function hasAccess(string $role, string $controller, string $action): bool
    {
        return in_array($action, $this->acl[$role][$controller] ?? []);
    }
}
