<?php

namespace App\Http\Services\Role;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Arr;

class PermissionService
{
    /**
     * @var Permission
     */
    private Permission $permission;

    /**
     * @param Permission|null $permission
     */
    public function __construct(Permission $permission = null)
    {
        $this->permission = $permission ?: new Permission();
    }

    /**
     * @param array $data
     * @return Permission
     */

    public function assignData(array $data): Permission
    {
        $this->permission->name = $data['name'];
        $this->permission->description = Arr::get($data, 'description');
        $this->permission->save();

        return $this->permission;
    }

    /**
     * @param Permission $permissions
     * @param Role $role
     * @return void
     */

    public function assignPermissions(Permission $permissions, Role $role): void
    {
        $role->permissions()->sync($permissions);
    }

    /**
     * @param Role $role
     * @param $permission
     * @return Role
     */

    public function syncPermissionWithRole(Role $role,  $permission): Role
    {
        $role->permissions()->syncWithoutDetaching($permission);

        return $role;
    }

    /**
     * @param Role $role
     * @param Permission $permission
     * @return Role
     */

    public function detachPermissionFromRole(Role $role, Permission $permission): Role
    {
        $role->permissions()->detach($permission);
        return $role;
    }


}
