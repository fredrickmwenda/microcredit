<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;


class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        // $this->middleware(['permission:user.roles.index'])->only(['index', 'show', 'get_roles']);
        // $this->middleware(['permission:user.roles.create'])->only(['create', 'store']);
        // $this->middleware(['permission:user.roles.edit'])->only(['edit', 'update']);
        // $this->middleware(['permission:user.roles.destroy'])->only(['destroy']);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::get()->groupBy('module');
        $permission_groups = Permission::all()->pluck('module')->unique();
        return theme_view('user::permission.index', compact('permissions', 'permission_groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission_groups = Permission::all()->pluck('module')->unique();
        return theme_view('user::permission.create', compact('permission_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'required|string',
            'module' => 'required|string',
        ]);

        Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'module' => $request->module,
            'guard_name' => $request->guard_name,
        ]);

        return redirect()
            ->route('permission.index')
            ->with('success', 'Permission Created Successfully');
    }
        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return theme_view('user::permission.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::where('id', $id)->first();


        return theme_view('user::permission.edit', compact('permission'));
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
            'name' => ['required', 'regex:/^[a-z]+\.[a-z]+\.[a-z]+$/'],
            'display_name'=> 'required',
            'module' => 'required',
        ]);
        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'module' => $request->module,
        ]);
        return redirect()->route('permission.index')->with('success', 'Permission Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $id = (int) $id;
        // Find the permission by its id
        $permission = Permission::findById($id);
        //dd($permission);

        // Get all roles
        $roles = Role::all();

        // Loop through all roles
        foreach ($roles as $role) {
            // If the role has the permission, remove it
            if ($role->hasPermissionTo($permission)) {
                $role->revokePermissionTo($permission);
            }
        }

        // Delete the permission
        $permission->delete();

        return redirect()->back()->with('message', 'Permission deleted successfully');
    }
}

