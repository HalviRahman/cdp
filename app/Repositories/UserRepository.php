<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\PermissionGroup;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRepository extends Repository
{
    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * get prodi options
     *
     * @return array
     */
    public function getProdiOptions()
    {
        return ProgramStudi::all()
            ->mapWithKeys(function ($item) {
                return [$item->jenjang . ' ' . $item->nama_prodi => $item->jenjang . ' ' . $item->nama_prodi];
            })
            ->toArray();
    }

    public function getAnggotaOptions()
    {
        $userProdi = auth()->user()->prodi;
        // $userProdi = json_decode(auth()->user()->prodi, true);

        return User::whereNotNull('prodi')
            // Query untuk menghilangkan nama dosen yang sudah ada di kelompok

            // ini query sebelum diubah (tidak mempertimbangkan status proposal)
            // ->whereNotIn('email', function ($query) {
            //     $query->select('anggota_email')->from('kelompoks')->whereNotNull('anggota_email')->whereYear('created_at', date('Y')); // hanya cek data tahun ini
            // })

            // ini query untuk menghilangkan nama ketika sudah ada di tabel kelompok
            // ->whereNotIn('email', function ($query) {
            //     $query->select('kelompoks.anggota_email')->from('kelompoks')->join('proposals', 'kelompoks.id_kelompok', '=', 'proposals.id_kelompok')->whereNotNull('kelompoks.anggota_email')->whereYear('kelompoks.created_at', date('Y'))->where('proposals.status', '!=', '10'); // cek status di tabel proposals
            // })
            // end
            // ini query untuk menghilangkan nama dosen yang sedang menjadi session auth
            ->where('email', '!=', auth()->user()->email)
            // end
            // Query untuk menampilkan nama mahasiswa yang memiliki prodi yang sama dengan prodi user login
            ->where(function ($query) use ($userProdi) {
                foreach ($userProdi as $prodi) {
                    $query->orWhere('prodi', 'like', '%' . $prodi . '%');
                }
            })
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->email => $item->name . ' - ' . implode('; ', $item->prodi),
                ];
            });
    }

    /**
     * get user id login
     *
     * @return int
     */
    public function getUserIdLogin()
    {
        return auth()->id() ?? auth('api')->id();
    }

    /**
     * set and get user login
     *
     * @return User
     */
    public function login(User $user)
    {
        auth()->login($user, request()->filled('remember'));
        $user->update(['last_login' => now()]);
        logLogin();
        return $user;
    }

    /**
     * find user by email
     *
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * find user by twitter id
     *
     * @param string $twitterId
     * @return User
     */
    public function findByTwitterId(string $twitterId)
    {
        return $this->model->where('twitter_id', $twitterId)->first();
    }

    /**
     * find user by email token
     *
     * @param string $emailToken
     * @return User
     */
    public function findByEmailToken(string $emailToken)
    {
        return $this->model->where('email_token', $emailToken)->first();
    }

    /**
     * update profile by user login
     *
     * @param array $data
     * @return int
     */
    public function updateProfile(array $data)
    {
        $userId = $this->getUserIdLogin();
        $this->model->where('id', $userId)->update($data);
        return $this->find($userId);
    }

    /**
     * get users data
     *
     * @return Collection
     */
    public function getUsers()
    {
        $users = $this->model
            ->with(['roles'])
            ->where('is_mahasiswa', null)
            ->latest()
            ->get();
        return $users;
    }

    /**
     * get user as option dropdown
     *
     * @return array
     */
    public function getUserOptions()
    {
        return $this->getUsers()->pluck('name', 'id')->toArray();
    }

    /**
     * get user data as pagination
     *
     * @param integer $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginateUsers($perPage = 20)
    {
        $users = $this->model
            ->with(['roles'])
            ->latest()
            ->paginate($perPage);
        return $users;
    }

    /**
     * get all role data
     *
     * @return Collection
     */
    public function getRoles()
    {
        $roles = Role::with(['permissions'])
            ->withCount(['permissions'])
            ->latest()
            ->get();
        return $roles;
    }

    /**
     * get role as option dropdown
     *
     * @return array
     */
    public function getRoleOptions()
    {
        return $this->getRoles()
            ->whereNotIn('name', ['superadmin', 'user', 'admin'])
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * find permission
     *
     * @param integer $permissionId
     * @return Permission
     */
    public function findPermission(int $permissionId)
    {
        return Permission::where('id', $permissionId)->first();
    }

    /**
     * find permission group
     *
     * @param integer $groupId
     * @return PermissionGroup
     */
    public function findPermissionGroup(int $groupId)
    {
        return PermissionGroup::where('id', $groupId)->first();
    }

    /**
     * delete permission
     *
     * @param integer $permissionId
     * @return Permission
     */
    public function deletePermission(int $permissionId)
    {
        return Permission::where('id', $permissionId)->delete();
    }

    /**
     * delete permission group by id
     *
     * @param integer $groupId
     * @return Permission
     */
    public function deletePermissionGroup(int $groupId)
    {
        return PermissionGroup::where('id', $groupId)->delete();
    }

    /**
     * update permission data
     *
     * @param integer $permissionId
     * @param array $data
     * @return Permission
     */
    public function updatePermission(int $permissionId, array $data)
    {
        Permission::where('id', $permissionId)->update($data);
        return $this->findPermission($permissionId);
    }

    /**
     * update permission group data
     *
     * @param integer $groupId
     * @param array $data
     * @return PermissionGroup
     */
    public function updatePermissionGroup(int $groupId, array $data)
    {
        PermissionGroup::where('id', $groupId)->update($data);
        return $this->findPermissionGroup($groupId);
    }

    /**
     * get all permission data
     *
     * @return Collection
     */
    public function getPermissions()
    {
        return Permission::all();
    }

    /**
     * get all permission join group data
     *
     * @return Collection
     */
    public function getPermissionJoinGroups()
    {
        $permissions = Permission::select(['permissions.*', 'permission_groups.group_name'])
            ->join('permission_groups', 'permissions.permission_group_id', '=', 'permission_groups.id')
            ->get();
        return $permissions;
    }

    /**
     * get all permission join group data latest
     *
     * @return Collection
     */
    public function getLatestPermissionJoinGroups()
    {
        $permissions = Permission::select(['permissions.*', 'permission_groups.group_name'])
            ->join('permission_groups', 'permissions.permission_group_id', '=', 'permission_groups.id')
            ->latest()
            ->get();
        return $permissions;
    }

    /**
     * get permission group data latest
     *
     * @return Collection
     */
    public function getLatestPermissionGroups()
    {
        return PermissionGroup::latest()->get();
    }

    /**
     * get permission as option dropdown
     *
     * @return array
     */
    public function getPermissionGroupOptions()
    {
        return PermissionGroup::pluck('group_name', 'id')->toArray();
    }

    /**
     * create permission data
     *
     * @param array $data
     * @return Permission
     */
    public function createPermission(array $data)
    {
        return Permission::create($data);
    }

    /**
     * create permission group data
     *
     * @param array $data
     * @return PermissionGroup
     */
    public function createPermissionGroup(array $data)
    {
        return PermissionGroup::create($data);
    }

    /**
     * findRole
     *
     * @param integer $roleId
     * @return Role
     */
    public function findRole(int $roleId)
    {
        return Role::where('id', $roleId)
            ->with(['permissions'])
            ->first();
    }

    /**
     * create role data
     *
     * @param string $roleName
     * @param array $data
     * @return int
     */
    public function createRole(string $roleName, array $data)
    {
        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);
        if (isset($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])->get();
            $role->syncPermissions($permissions);
            return $role;
        }
    }

    /**
     * update role data
     *
     * @param int $roleId
     * @param array $data
     * @return int
     */
    public function updateRole(int $roleId, array $data)
    {
        $role = Role::find($roleId);
        $role->update($data);
        if ($role && isset($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])->get();
            $role->syncPermissions($permissions);
            return $role;
        }
    }

    /**
     * delete role data
     *
     * @param int $roleId
     * @return int
     */
    public function deleteRole(int $roleId)
    {
        return Role::where('id', $roleId)->delete();
    }

    /**
     * get all user where role owner boarding house data
     *
     * @return Collection
     */
    public function getOwnerOptions()
    {
        $owners = $this->model->role('pemilik kos')->get();
        return $owners->pluck('name', 'id')->toArray();
    }

    /**
     * get permission group with child
     *
     * @return Collection
     */
    public function getPermissionGroupWithChild()
    {
        return PermissionGroup::with(['permissions'])->get();
    }

    /**
     * getLogActivitiesPaginate
     *
     * @param integer $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLogActivitiesPaginate($perPage = 20)
    {
        return ActivityLog::query()->where('user_id', $this->getUserIdLogin())->latest()->paginate($perPage);
    }

    /**
     * assign role
     *
     * @param User $user
     * @param string $role
     * @return User
     */
    public function assignRole(User $user, string $role)
    {
        return $user->assignRole($role);
    }

    /**
     * sync roles
     *
     * @param User $user
     * @param array $role
     * @return User
     */
    public function syncRoles(User $user, array $roles)
    {
        return $user->syncRoles($roles);
    }
}
