<?php

namespace App\Http\Controllers\Admin\Accounts;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    /**
     * Role Controller Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:roles-list',['only' => ['index']]);
        $this->middleware('permission:roles-create', ['only' => ['create','store']]);
        $this->middleware('permission:roles-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:roles-delete', ['only' => ['destroy']]);

        $this->permission_avail = [
            'admin'         => ['list', 'create', 'edit', 'delete'],
            'roles'         => ['list', 'create', 'edit', 'delete'],
            'Peta'          => ['list', 'create', 'edit', 'delete'],
            'subscriber'    => ['list', 'create', 'edit', 'delete'],
            'laporan'       => ['list', 'create', 'edit', 'delete'],
            'provinsi'      => ['list', 'create', 'edit', 'delete'],
            'kabupaten/Kota'=> ['list', 'create', 'edit', 'delete'],
            'kecamatan'     => ['list', 'create', 'edit', 'delete'],
            'kecamatan'     => ['list', 'create', 'edit', 'delete']
        ];
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->get();
        return view('admin.accounts.roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $options = $this->permission_avail;
        return view('admin.accounts.roles.create', compact('options'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token','_method');

        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:roles,name']
        ]);
        
        $role = new Role;
        $role->name = $request->name;
        $role->save();

        // Assign Permission
        $role->syncPermissions($data['permissions']);

        return redirect()->route('admin.role.index')->with([
            'status'    => 'success',
            'message'   => 'Role permission successfully added'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.accounts.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $options = $this->permission_avail;
        $old_options = Permission::join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('role_has_permissions.role_id', $id)
            ->get()->pluck('name');
            
        return view('admin.accounts.roles.edit', compact('role', 'options', 'old_options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:roles,name,'.$id]
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        // Validate Request
        $old_permission = array();
        foreach($role->permissions as $permissions){
            array_push($old_permission, $permissions->name);
        }
        $new_permission = $request->permissions;

        // Check Action
        $revoke = array_diff($old_permission, $new_permission);
        foreach($revoke as $r){
            $role->revokePermissionTo($r);
        }
        $assign = array_diff($new_permission, $old_permission);
        foreach($assign as $a){
            $role->givePermissionTo($a);
        }

        return redirect()->route('admin.role.index')->with([
            'status'    => 'success',
            'message'   => 'Role permission for '. $role->name .' successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Check if Role assigned to users
        if($role->users()->exists()){
            return response()->json([
                'status' => 'danger',
                'message' => 'Failed to delete. Please remove role from assigned users'
            ]);
        }

        $role->delete();
        return response()->json([
            'status'    => 'success',
            'message' => 'Role permission successfully deleted'
        ]);
    }
}
