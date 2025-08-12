<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueListing;
use App\Models\Device;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\URL;
use App\Jobs\IssueListingMailJob;
use App\Jobs\IssueListingTechnicianMailJob;
use Debugbar;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $issues = Issue::all();
        $page_title = __('Manage Room Issues');
        $page_description = __('Total: ') . $issues->count();

        return view('pages.issues.issues', compact('page_title', 'page_description', 'issues'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Holiday::class);
        $issues = Issue::all();

        $issues_add = [];
        $issues_remove = [];
        $issues_req = new Collection( request('issues') );
        
        $issuesToRemove = $issues->whereNotIn('name', $issues_req->pluck('name'));
        $issuesToAdd = $issues_req->whereNotIn('name', $issues->pluck('name'));
        

        foreach ($issuesToRemove as $key => $issue) {
          $issues_remove[] = $issue->id;
        }

        foreach ($issuesToAdd as $key => $issue) {
          $issues_add[] = [
            'name' => $issue['name'],
          ];
        }

        
        try {
          Issue::destroy($issues_remove);
          Issue::insert($issues_add);            
          return redirect()->back()->with([ 'success' => __('Room List issues updated sucessfully') ]);
        } catch (\Exception $ex) {
          return $ex;
          return redirect()->back()->with([ 'error' => __('Room List issues cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function show(Issue $issue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Issue  $issues
     * @return \Illuminate\Http\Response
     */
    public function edit(Issue $issue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Issue $issue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Issue $issue)
    {
        //
    }

    // LISTINGS
    public function listings(Request $request)
    {
      // $assigned_devices = [];
      // if ( auth()->user()->hasRole(['super_admin', 'admin']) ) {
      //   $assigned_devices = Device::all()->pluck('id'); // get all devices
      // } else {
      //   $assigned_devices = auth()->user()->devices->pluck('id'); //get just assigned devices
      // }
      $devices_arr = Device::available()->pluck('id')->toArray();

      $issues = Issue::all();
      $devices = Device::available()->get();
      $hotel_rooms = $devices;
      $page_title = __('Room Issues');
      $page_description = __('Manage Issues');
      $open_issue_listings = IssueListing::active()
      ->withWhereHas('room', fn($query) =>
        $query->whereIn('rooms.device_id', $devices_arr)
      )->count();
      $closed_issue_listings = IssueListing::fixed()
      ->withWhereHas('room', fn($query) =>
        $query->whereIn('rooms.device_id', $devices_arr)
      )->count();

      return view('pages.issues.listings', compact('page_title', 'page_description', 'issues', 'devices', 'open_issue_listings', 'closed_issue_listings', 'hotel_rooms'));
    }

    public function fix_listings(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'listing' => 'required|exists:issue_listings,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all() ], 500);
        }

        $listing = IssueListing::findOrFail( $request->get('listing') );
        $listing->update([
          'done' => 1,
          'email_fixed' => $request->has('email_fixed') ? $request->get('email_fixed') : auth()->user()->email,
          'comment_fixed' => $request->get('comment_fixed'),
          'date_fixed' => Carbon::now()
        ]);

        $total_requested = IssueListing::active()->count();
        $total_fixed = IssueListing::fixed()->count();

        $url = URL::signedRoute('issues.listings');
        $cc = $listing->room->device->hotel_email != null ? $listing->room->device->hotel_email : null; //if there is a hotel email
        $locale = app()->getLocale();
        IssueListingMailJob::dispatch($url, $listing, $cc, $locale)->delay(now()->addSeconds(1));

        if ( $request->ajax() ) {
          return response()->json( ['success' => $listing, 'total_requested' => $total_requested, 'total_fixed' => $total_fixed], 200 );
        } else {
          return __('Issue resolved successfully');
        }
        

    }

    public function listings_external(IssueListing $listing, Request $request) {
      if (! $request->hasValidSignature()) {
          abort(401);
      }

      $listing->clearMediaCollection();

      if ( $listing->date_fixed != null ) {
        
        return __('Issue is resolved');
      }
      
      $page_title = __('Listing');
      $page_description = __('Issue requested');

      $email = $request->get('email');
      
      return view('pages.external.issue_listing', compact('page_title', 'page_description', 'listing', 'email'));
    }

    public function listings_external_fix(IssueListing $listing, Request $request) {
      $validator = Validator::make($request->all(), [
        'listing' => 'required|exists:issue_listings,id',
      ]);
      if ($validator->fails()) {
          return response()->json(['error' => $validator->errors()->all() ], 500);
      }

      $listing = IssueListing::findOrFail( $request->get('listing') );
      $listing->update([
        'done' => 1,
        'email_fixed' => $request->get('email_fixed'),
        'comment_fixed' => $request->get('comment_fixed'),
        'date_fixed' => Carbon::now()
      ]);

      if ($request->hasFile('photo')) {
          $fileAdders = $listing->addMultipleMediaFromRequest(['photo'])
          ->each(function ($fileAdder) {
              $fileAdder->toMediaCollection('photos');
          });
      }

      $url = URL::signedRoute('issues.listings');
      $cc = $listing->room->device->hotel_email != null ? $listing->room->device->hotel_email : null; //if there is a hotel email
      $locale = app()->getLocale();
      IssueListingMailJob::dispatch($url, $listing, $cc, $locale)->delay(now()->addSeconds(1));

      return back()->with(['message' => 'aaa']); //prevent resubmit

      // return __('Issue resolved successfully');
    }

    public function delete_listings(IssueListing $listing, Request $request)
    {
        $listing->delete();

        $total_requested = IssueListing::active()->count();
        $total_fixed = IssueListing::fixed()->count();

        return response()->json( ['success' => __('Listing deleted successfully'), 'total_requested' => $total_requested, 'total_fixed' => $total_fixed], 200 );
    }

    public function store_listings(Request $request)
    {
        $request->validate([
          'room' => 'required',
          'issue' => 'required',
        ]);

        $listing = new IssueListing;
        $listing->room_id = request('room');
        $listing->issue_id = request('issue');
        $listing->done = 0;
        $listing->user_requested = auth()->user()->id;
        $listing->date_requested = Carbon::now();
        $listing->comment_requested = request('comment');
        $listing->priority = request('priority');
        $listing->save();

        $total_active = IssueListing::active()->count();

        $url = URL::signedRoute('issues.listings');
        $cc = $listing->room->device->hotel_email != null ? $listing->room->device->hotel_email : null; //if there is a hotel email
        $locale = app()->getLocale();
        IssueListingMailJob::dispatch($url, $listing, $cc, $locale)->delay(now()->addSeconds(1));

        if ( $listing->room->device->hotel_technician_email != null ) { //if there is a hotel technician email
          $url = URL::signedRoute('issues.listings_external', ['listing' => $listing->id, 'email' => $listing->room->device->hotel_technician_email]);
          $to = $listing->room->device->hotel_technician_email;
          $locale = app()->getLocale();
          IssueListingTechnicianMailJob::dispatch($url, $listing, $to, $locale)->delay(now()->addSeconds(1));
        }

        return response()->json( ['success' => __('Listing added successfully'), 'total' => $total_active], 200 );
    }

    public function listings_ajax(Request $request)
    {
        // $this->authorize('viewAny', Issues::class);        

        $devices_arr = Device::available()->pluck('id')->toArray();

        // defaults
        $page = 1;
        $perpage = 10;
        $order_by = 'date_requested';
        $sort = 'desc';
        $fixed = $request->get('type') == 1 ? 1 : 0;
        


        if ( $request->has('pagination') ) {
          if ( !empty( $request->get('pagination')['page'] ) ) {
            $page = $request->get('pagination')['page'];
          }
          if ( !empty( $request->get('pagination')['perpage'] ) ) {
            $perpage = $request->get('pagination')['perpage'];
          }
        }

        if ( $request->has('sort') ) {
          if ( is_array( request('sort') ) ) {
            $sort = request('sort')['sort'];
          }
        }

        if ( $request->has('query') ) {

          $search = $request->get('query');
          if ( !is_array($search) ) {
            $listings = IssueListing::with('userRequested')
            // ->with('userFixed')
            ->with('issue:id,name')
            ->with('room:id,name')
            ->with('room.device')
            //AppServiceProvider Macro
            ->withWhereHas('room', fn($query) =>
              $query->whereIn('rooms.device_id', $devices_arr)
            )
            ->whereBetween( 'date_requested', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()] )
            ->with('media')
            ->where('done', $fixed)
            ->orderBy($order_by, $sort)
            ->paginate( $perpage, ['*'], null, $page );
          } else {
            $listings = IssueListing::with('userRequested')
            // ->with('userFixed')
            ->with('issue:id,name')
            ->with('room:id,name')
            //AppServiceProvider Macro
            ->withWhereHas('room', fn($query) =>
              $query->whereIn('rooms.device_id', $devices_arr)
            )
            ->with('room.device')
            ->with('media')
            ->where('done', $fixed)
            ->orderBy($order_by, $sort)
            ->where(function ($q) use ($search){
              foreach( $search as $key => $value ) {
                if( $key == "generalSearch" ){
                  $q->whereHas( 'issue', function($k) use ($value) {
                      $k->where( 'name', 'like', "%{$value}%" );
                  })
                  ->orWhereHas( 'room', function($k) use ($value) {
                    $k->where( 'name', 'like', "%{$value}%" );
                  })
                  ->orWhereHas( 'userRequested', function($k) use ($value) {
                    $k->where( 'name', 'like', "%{$value}%" );
                  });
                }

                if( $key == "Date" ){
                  if ( !empty( $value['start'] || $value['end'] ) ) {
                    $start = Carbon::parse( $value['start'] )->startOfDay();
                    $end = Carbon::parse( $value['end'] )->endOfDay();
                    $q->whereBetween( 'date_requested', [$start, $end] );
                  }
                }

                if( $key == "device" ){
                  $q->whereHas('room', function($q) use ($value){
                    $q->where('device_id', $value);
                  });
                }

              }
            })->paginate( $perpage, ['*'], null, $page );
          }

        } else {
          $listings = IssueListing::with('userRequested')
          // ->with('userFixed')
          ->with('issue:id,name')
          ->with('room:id,name')
          //AppServiceProvider Macro
          ->withWhereHas('room', fn($query) =>
            $query->whereIn('rooms.device_id', $devices_arr)
          )
          ->with('room.device')
          ->with('media')
          ->where('done', $fixed)
          ->orderBy($order_by, $sort)
          ->paginate( $perpage, ['*'], null, $page );
        }

        $meta = [
          "page" => $listings->currentPage(),
          "pages" => intval(count($listings) / $perpage),
          "perpage" => $perpage,
          "total" => $listings->total(),
          "sort" => $sort,
          "field" => $order_by,
        ];

        return response()->json( ['meta' => $meta, 'data' => $listings->items()], 200 );
        // return response()->json( $records, 200 );
    }

    /**
     * Get all rooms in json format.
     *
     * @return json
     */
    public function rooms_ajax(Device $device, Request $request)
    {
        $this->authorize('viewAny', Device::class);

        $page = 1; //default
        $perpage = 30; //default

        if ( $request->has('page') ) {
          $page = $request->get('page');
        }
        if ( $request->has('perpage') ) {
          $perpage = $request->get('perpage');
        }

        if ( $request->has('query') ) {

          $search = $request->get('query');
          $rooms = Room::select('id', 'name as text')
          ->where('device_id', $device->id)
          ->where(function ($q) use ($search){
            $q->where( 'name', 'LIKE', "%{$search}%" );
          })
          ->paginate( $perpage, ['*'], null, $page );

        } else {

          $rooms = Room::select('id', 'name as text')
          ->where('device_id', $device->id)
          ->paginate( $perpage, ['*'], null, $page );
          
        }

        $data = [
          "total_count" => $rooms->total(),
          "items" => $rooms->items(),
        ];

        return response()->json( $data, 200 );
    }
}
