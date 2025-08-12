<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
// use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super_admin|admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('Access Management');
        $page_description = __('All Access Management');
        $roles = Role::orderBy('id','ASC')->get();
        $permissions = Permission::all();

        return view('pages.roles.index', compact('page_title', 'page_description', 'roles', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = __('Access Management');
        $page_description = __('Add new Access Management');

        return view('pages.roles.create', compact('page_title', 'page_description'));
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
          'name' => 'required|unique:roles',
          'permissions' => 'required|array|min:1',
          'permissions.*' => 'required|string|min:1',
        ]);

        $role = new Role();
        $role->name = request('name');
        // $role->guard_name = config('auth.defaults.guard');
        $role->syncPermissions( request('permissions') );
        $role->save();
        return redirect()->route('roles.index')->with([ 'success' => __('controllers.access_management.store.success', ['name' => $role->name]) ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        if ( $role->name == 'super_admin' && !auth()->user()->hasRole('super_admin') ) {
          abort(403, __('This access cannot be shown'));
        }
        $page_title = $role->name;
        $page_description = __('Edit permissions for access');
        $item_active = 'personal';
        $role = Role::where('id', $role->id)->with('permissions')->first();
        // return $role->permissions;

        return view('pages.roles.edit', compact('page_title', 'page_description', 'role', 'item_active'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        if ( $role->name == 'super_admin' && !auth()->user()->hasRole('super_admin') ) {
          abort(403, __('This access cannot be edited'));
        }
        $page_title = $role->name;
        $page_description = __('Edit permissions for access');
        $item_active = 'personal';

        return view('pages.roles.edit', compact('page_title', 'page_description', 'role', 'item_active'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        if ( $role->name == 'super_admin' && !auth()->user()->hasRole('super_admin') ) {
          abort(403, __('This access cannot be edited'));
        }

        $request->validate([
          'name' => 'required',
          'permissions' => 'required|array|min:1',
          'permissions.*' => 'required|string|min:1',
        ],
        [
            'permissions.required' => __('There should be at least one permission!')
        ]);
        $r = Role::findOrFail($role->id);
        $r->name = request('name');
        $r->save();
        try {
          $r->syncPermissions( request('permissions') );
          return redirect()->back()->with([ 'success' => __('Access Management updated successfully')]);
        } catch (\Exception $e) {
          return redirect()->back()->with([ 'error' => __('Access Management permissions cannot be updated'), 'message' => ['exception' => $e->getMessage()] ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $r = Role::findOrFail($role->id);
        if ($r->name == 'super_admin' || $r->name == 'admin') {
          abort(403, __('This access cannot be deleted'));
        }
        try {
          $r->delete();
          return redirect()->route('roles.index')->with( ['success' => __('Access Management deleted successfully') ] );
        } catch (\Exception $e) {
          return redirect()->back()->with([ 'error' => __('Access Management cannot be deleted'), 'message' => ['exception' => $e->getMessage()] ]);
        }

    }
}
