<?php

namespace App\Http\Services\Role;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Arr;

class RoleService
{
    /**
     * @var Role
     */
    private Role $role;

    /**
     * @param Role|null $role
     */

    public function __construct(Role $role = null)
    {
        $this->role = $role ?: new Role();
    }

    /**
     * @param array $data
     * @return Role
     */

    public function assignData(array $data): Role
    {
        $this->role->name = $data['name'];
        $this->role->save();
        return $this->role;
    }


    public function assignPermissions(Permission $permissions, Role $role = null): Role
    {
        $role = $role ?: $this->role;
        $role->permissions()->sync($permissions);
        return $this->role;
    }
}
