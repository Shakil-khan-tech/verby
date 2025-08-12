<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Device;
use Illuminate\Http\Request;
// use Auth;
use Illuminate\Support\Facades\Storage;
use Image;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->authorizeResource('user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $page_title = __('Users');
        $page_description = __('All the users');
        $users = User::nodevice()->get();

        return view('pages.users.index', compact('page_title', 'page_description', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $page_title = __('User');
        $page_description = __('Create user');

        return view('pages.users.create', compact('page_title', 'page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $this->validate($request,[
            'avatar' => 'max:8192',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'min:6|confirmed',
        ]);

        $user = new User;
        $user->name = request('name');
        $user->email = request('email');
        $user->password = bcrypt( request('password') );
        $user->avatar = null;

        if($request->hasFile('avatar')){
            $image = $request->file('avatar');
            $avatar = Storage::disk('public')->put('avatars/users', $image);
            Image::make( storage_path() . '/app/public/'.$avatar )
              ->orientate()
              ->fit(300, 300, function ($constraint) {
                  $constraint->upsize();
              })
              ->save( public_path('storage/' . $avatar) );

            $user->avatar = $avatar;
        }

        try {
          $user->save();
          return redirect()->route('users.show', $user->id)->with([ 'success' => __('User created sucessfully') ]);

        } catch (\Exception $ex) {
          return redirect()->back()->with([ 'error' => __('User cannot be created'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('show', $user);

        $can_change_pass = true;
        if ( $user->roles->contains('name', auth()->user()->roles[0]->name) && $user->id != auth()->user()->id ) {
          $can_change_pass = false;
        }

        $page_title = $user->name;
        $page_description = '';
        $item_active = 'personal';
        $user_roles = $user->getRoleNames();

        return view('pages.users.show', compact('page_title', 'page_description', 'user', 'user_roles', 'item_active', 'can_change_pass'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function role(User $user)
    {
        $this->authorize('manage', $user);

        $page_title = $user->name;
        $page_description = '';
        $item_active = 'role';
        $devices = Device::all();
        $all_roles = Role::all();
        foreach ($all_roles as $key => $role) {
          if ( auth()->user()->hasRole('super_admin') ) {
            continue;
          }
          if ( $role->name == 'super_admin' && !auth()->user()->hasRole('super_admin') ) {
            $all_roles->forget($key);
          } elseif ( $role->name == 'admin' && !auth()->user()->hasRole('admin') ) {
            $all_roles->forget($key);
          }
        }
        // $all_roles->forget(0);
        // return $all_roles;
        $user_roles = $user->getRoleNames();

        return view('pages.users.role', compact('page_title', 'page_description', 'user', 'item_active', 'devices', 'user_roles', 'all_roles'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('edit', $user);

        $page_title = $user->name;
        $page_description = '';
        $item_active = 'personal';
        $user_roles = $user->getRoleNames();

        return view('pages.users.show', compact('page_title', 'page_description', 'user', 'user_roles', 'item_active'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('manage', $user);

        $this->validate($request,[
            'avatar' => 'max:8192',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = request('name');
        $user->email = request('email');

        if($request->hasFile('avatar')){
            $image = $request->file('avatar');
            $avatar = Storage::disk('public')->put('avatars/users', $image);
            Image::make( storage_path() . '/app/public/'.$avatar )
              ->orientate()
              ->fit(300, 300, function ($constraint) {
                  $constraint->upsize();
              })
              ->save( public_path('storage/' . $avatar) );

            $user->avatar = $avatar;
        }

        if ( request('profile_avatar_remove') == 1 ) {
          $user->avatar = null;
        }

        if ( request('password') ) {
          $user->password = bcrypt( request('password') );
        }

        try {
          $user->save();
          return redirect()->back()->with([ 'success' => __('User updated sucessfully') ]);

        } catch (\Exception $ex) {
          return redirect()->back()->with([ 'error' => __('User cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function role_update(Request $request, User $user)
    {
        // return $request->all();
        $this->authorize('manage', $user);

        $this->validate($request,[
            'role' => 'exists:roles,id',
            'device_permissions' => 'sometimes|required|array',
        ]);

        $role = Role::findOrFail( request('role') );
        $user->syncRoles( $role );

        $user->devices()->sync( request('device_permissions') );
        
        return redirect()->back()->with([ 'success' => __('User updated sucessfully') ]);

    }

    /**
     * Change language of user and store into DB.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function change_lang(Request $request)
    {
        // $user = auth()->user();
        // $locale = request('lang');
        // \App::setLocale($locale);
        // session()->put('locale', $locale);
        // $user->update(['language' => $locale]);
        // return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('manage', $user);

        try {
          $user->delete();
          return redirect()->route('users.index')->with([ 'success' => __('controllers.user.destroy.success', ['name' => $user->name]) ]);
        } catch (\Exception $e) {
          return redirect()->back()->with([ 'error' => __('User cannot be deleted'), 'message' => ['exception' => $e->getMessage()] ]);
        }
    }

    /**
     * Activate / Disable user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function change_active(User $user, Request $request)
    {
        if ( $user->hasRole('super_admin') ) {
          return back()->with([ 'error' => __('Action not allowed'), 'message' => ['exception' => __('Super admin cannot be disabled')] ]);
        }

        if ( $user->id == auth()->user()->id ) {
          return back()->with([ 'error' => __('Action not allowed'), 'message' => ['exception' => __('You cannot disable yourself')] ]);
        }

        try {

            $user->active = $request->active;
            $user->save();

            if ( $request->wantsJson() ) {
                return response()->json( ['message' => $request->active == 0 ? __('User disabled') : __('User activated')], 200 );
            } else {
                return redirect()->back()->with([ 'success' => __('Status changed') ]);
            }

        } catch (\Exception $e) {
            Log::debug( $e->getMessage() .' at UsersController.change_active:' . __LINE__ );
            return response()->json(['message' => __('User cannot be updated'), 'errors' => ['exception' => $e->getMessage()] ], 500);
        }
        
    }
}
