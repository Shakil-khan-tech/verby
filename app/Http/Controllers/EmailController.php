<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Email;
use WebReinvent\CPanel\CPanel;
use App\Http\Traits\EmailTrait;
use Auth;
use Validator;
use Carbon\Carbon;

class EmailController extends Controller
{
    use EmailTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $page_title = __('Domain Emails');
        $page_description = __('All the domain emails');

        return view('pages.emails.index', compact('page_title', 'page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $page_title = __('Domain Emails');
        $page_description = __('Create New');

        return view('pages.emails.create', compact('page_title', 'page_description'));
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

        $validator = Validator::make($request->all(),[
            // 'email' => 'required|email',
            'email' => 'required|email|max:255|regex:/^[A-Za-z0-9\.]*@(aaab)[.](ch)$/',
            'quota' => 'required|numeric|min:1|max:'.config('cpanel.max_quota'),
            'password' => 'min:6|confirmed',
        ]);
        if( $validator->fails() ){
            return redirect()->back()->withInput()->with([ 'error' => __('Error occured!'), 'message' => ['exception' => $validator->errors()] ]);
        }

        try {
            $uapi = $this->create_email(request('email'), request('password'), request('quota'));
            if ( $uapi->status == "failed" ) {
                return redirect()->back()->with([ 'error' => __('Error occured while creating email!'), 'message' => ['exception' => implode("|",$uapi->errors)] ]);
            }
            return redirect()->back()->with(['success' => __('Domain email created successfully')]);
        } catch (\Exception $ex) {
            return redirect()->back()->with([ 'error' => __('Domain email cannot be created'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $email
     * @return \Illuminate\Http\Response
     */
    public function show($email)
    {
        $this->authorize('create', User::class);

        $validator = Validator::make(['email' => $email],[
            'email' => 'required|email'
        ]);
        if( $validator->fails() ){
            return response()->json(['message' => __('Email is not valid or is empty'), 'errors' => ['exception' => $validator->errors()] ], 500);
        }

        $page_title = __('Domain Email');
        $page_description = $email;

        $uapi = $this->list_emails($email);
        if ( $uapi->status == "failed" ) {
            return response()->json(['message' => __('Error occured while getting email'), 'errors' => [$uapi->errors] ], 500);
        }
        $data = collect($uapi->data)->first();
        if ( Email::where('email', $email)->exists() ) {
            $data->mtime = Carbon::parse( Email::find($email)->mtime )->timestamp;
        }
        $can_manage = Auth::user()->hasRole(['super_admin', 'admin']) ? true : false;

        return view('pages.emails.show', compact('page_title', 'page_description', 'email', 'data', 'can_manage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('create', User::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $email
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $email)
    {
        $this->authorize('create', User::class);

        $request->merge(['email_param' => $email]);
        // return $request->all();

        $validator = Validator::make($request->all(),[
            'email_param' => 'required|email',
            'email' => 'required|email',
            'old_quota' => 'required|numeric',
            'quota' => 'required|numeric|min:1|max:'.config('cpanel.max_quota'),
            'password' => 'sometimes|nullable|min:6|confirmed',
        ]);
        if( $validator->fails() ){
            return redirect()->back()->with([ 'error' => __('Error occured!'), 'message' => ['exception' => $validator->errors()] ]);
        }

        $quota = request('quota');
        $old_quota = request('old_quota');
        $old_quota = $old_quota / 1024 / 1024;

        try {
            if ( $request->has('password') && !empty( request('password') ) ) {
                $uapi = $this->change_password($email, request('password'));
                // return $uapi;
                if ( $uapi->status == "failed" ) {
                    return redirect()->back()->with([ 'error' => __('Error occured while changing password!'), 'message' => ['exception' => implode("|",$uapi->errors)] ]);
                }
            }
            
            if ($old_quota != request('quota')) {
                $uapi = $this->change_quota($email, request('quota'));
                if ( $uapi->status == "failed" ) {
                    return redirect()->back()->with([ 'error' => __('Error occured while changing quota!'), 'message' => ['exception' => implode("|",$uapi->errors)] ]);
                }
            }

            if ( Email::where('email', $email)->exists() ) {
                $email_db = Email::where('email', $email)->first();
                $email_db->mtime = Carbon::now();
                $email_db->save();
            }

            return redirect()->back()->with(['success' => __('Domain email updated successfully')]);
        } catch (\Exception $ex) {
            return redirect()->back()->with([ 'error' => __('Domain email cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $email)
    {
        $this->authorize('create', User::class);

        $request->merge(['email_param' => $email]);
        $validator = Validator::make($request->all(),[
            'email_param' => 'required|email|max:255|regex:/^[A-Za-z0-9\.]*@(aaab)[.](ch)$/',
        ]);
        if( $validator->fails() ){
            return redirect()->back()->with([ 'error' => __('Error occured!'), 'message' => ['exception' => $validator->errors()] ]);
        }

        try {
            $uapi = $this->delete_email($email);
            if ( $uapi->status == "failed" ) {
                return redirect()->back()->with([ 'error' => __('Error occured while deleting email!'), 'message' => ['exception' => implode("|",$uapi->errors)] ]);
            }
            return redirect()->route('emails.index')->with(['success' => __('controllers.email.destroy.success', ['name' => $email]) ]);
        } catch (\Exception $ex) {
            return 'sasa';
            return redirect()->back()->with([ 'error' => __('Domain email cannot be deleted'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Get all emails in json format.
     *
     * @return json
     */
    public function getall(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $uapi = $this->list_emails();
        if ( $uapi->status == "failed" ) {
            return response()->json(['message' => __('Error occured while getting emails'), 'errors' => [$uapi->errors] ], 500);
        }
        $data = collect($uapi->data)->sortBy('email')->values()->all();

        foreach ($data as $key => $user) {
            $email = Email::firstOrCreate(
                [ 'email' => $user->email ],
                [ 'mtime' => Carbon::createFromTimestamp($user->mtime) ],
            );
            $data[$key]->mtime = Carbon::parse($email->mtime)->timestamp;
        }

        $meta = [
          "page" => 1,
          "pages" => 1,
          "perpage" => -1,
          "total" => count( $uapi->data ),
        //   "sort" => "asc",
          "field" => "RecordID",
        ];

        return response()->json( ['meta' => $meta, 'data' => $data], 200 );
    }
}
