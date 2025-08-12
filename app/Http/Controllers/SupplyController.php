<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\SupplyListing;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\URL;
use App\Jobs\SupplyListingMailJob;

class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplies = Supply::all();
        $page_title = __('Manage Device Inventory');
        $page_description = __('Total: ') . $supplies->count();

        return view('pages.supplies.supplies', compact('page_title', 'page_description', 'supplies'));
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
        $supplies = Supply::all();

        $supplies_add = [];
        $supplies_remove = [];
        $supplies_req = new Collection( request('supplies') );

        $suppliesToRemove = $supplies->whereNotIn('name', $supplies_req->pluck('name'));
        $suppliesToAdd = $supplies_req->whereNotIn('name', $supplies->pluck('name'));

        foreach ($suppliesToRemove as $key => $supply) {
          $supplies_remove[] = $supply->id;
        }

        foreach ($suppliesToAdd as $key => $supply) {
          $supplies_add[] = [
            'name' => $supply['name'],
          ];
        }

        try {
          Supply::destroy($supplies_remove);
          Supply::insert($supplies_add);            
          return redirect()->back()->with([ 'success' => __('Device List Inventory updated sucessfully') ]);
        } catch (\Exception $ex) {
          return $ex;
          return redirect()->back()->with([ 'error' => __('Device List Inventory cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function show(Supply $supply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function edit(Supply $supply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supply $supply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supply $supply)
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

      $supplies = Supply::all();
      $devices = Device::available()->get();
      $page_title = __('Hotel Inventory');
      $page_description = __('Manage Inventory');
      $open_supply_listings = SupplyListing::active()
      ->whereIn('device_id', $devices_arr)
      ->count();
      $closed_supply_listings = SupplyListing::fixed()
      ->whereIn('device_id', $devices_arr)
      ->count();

      return view('pages.supplies.listings', compact('page_title', 'page_description', 'supplies', 'open_supply_listings', 'closed_supply_listings', 'devices'));
    }

    public function fix_listings(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'listing' => 'required|exists:supply_listings,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all() ], 500);
        }

        $listing = SupplyListing::findOrFail( $request->get('listing') );
        $listing->update([
          'done' => 1,
          'user_fixed' => auth()->user()->id,
          'date_fixed' => Carbon::now()
        ]);

        $total_requested = SupplyListing::active()->count();
        $total_fixed = SupplyListing::fixed()->count();

        $url = URL::signedRoute('supplies.listings');
        $cc = $listing->device->hotel_email != null ? $listing->device->hotel_email : null; //if there is a hotel email
        $locale = app()->getLocale();
        SupplyListingMailJob::dispatch($url, $listing, $cc, $locale)->delay(now()->addSeconds(1));

        return response()->json( ['success' => $listing, 'total_requested' => $total_requested, 'total_fixed' => $total_fixed], 200 );
    }

    public function delete_listings(SupplyListing $listing, Request $request)
    {
        $listing->delete();

        $total_requested = SupplyListing::active()->count();
        $total_fixed = SupplyListing::fixed()->count();

        return response()->json( ['success' => __('Listing deleted successfully'), 'total_requested' => $total_requested, 'total_fixed' => $total_fixed], 200 );
    }

    public function store_listings(Request $request)
    {
        $request->validate([
          'device' => 'required',
          'supply' => 'required',
        ]);

        $listing = new SupplyListing;
        $listing->device_id = request('device');
        $listing->supply_id = request('supply');
        $listing->done = 0;
        $listing->user_requested = auth()->user()->id;
        $listing->date_requested = Carbon::now();
        $listing->comment = request('comment');
        $listing->save();

        $total_active = SupplyListing::active()->count();

        $url = URL::signedRoute('supplies.listings');
        $cc = $listing->device->hotel_email != null ? $listing->device->hotel_email : null; //if there is a hotel email
        $locale = app()->getLocale();
        SupplyListingMailJob::dispatch($url, $listing, $cc, $locale)->delay(now()->addSeconds(1));

        return response()->json( ['success' => __('Listing added successfully'), 'total' => $total_active], 200 );
    }

    public function listings_ajax(Request $request)
    {
        // $this->authorize('viewAny', Supply::class);

        $devices_arr = Device::available()->pluck('id')->toArray();

        // defaults
        $page = 1;
        $perpage = 10;
        $order_by = 'date_requested';
        $sort = 'asc';
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
            $listings = SupplyListing::with('userRequested')
            ->with('userFixed')
            ->with('supply:id,name')
            ->with('device:id,name')
            ->whereIn('device_id', $devices_arr)
            ->where('done', $fixed)
            ->orderBy($order_by, $sort)
            ->paginate( $perpage, ['*'], null, $page );
          } else {
            $listings = SupplyListing::with('userRequested')
            ->with('userFixed')
            ->with('supply:id,name')
            ->with('device:id,name')
            ->whereIn('device_id', $devices_arr)
            ->where('done', $fixed)
            ->orderBy($order_by, $sort)
            ->where(function ($q) use ($search){
              foreach( $search as $key => $value ) {
                if( $key == "generalSearch" ){
                    $q->whereHas( 'supply', function($k) use ($value) {
                        $k->where( 'name', 'like', "%{$value}%" );
                    })
                    ->orWhereHas( 'device', function($k) use ($value) {
                        $k->where( 'name', 'like', "%{$value}%" );
                    })
                    ->orWhereHas( 'userRequested', function($k) use ($value) {
                        $k->where( 'name', 'like', "%{$value}%" );
                    })
                    ->orWhereHas( 'userFixed', function($k) use ($value) {
                        $k->where( 'name', 'like', "%{$value}%" );
                    });
                }

                if( $key == "Date" ){
                  if ( !empty( $value['start'] || $value['end'] ) ) {
                    $start = new Carbon( $value['start'] );
                    $end = new Carbon( $value['end'] );
                    $end = $end->endOfDay();
                    $q->whereBetween( 'date_requested', [$start, $end] );
                  }
                }

                if( $key == "device" ){
                    $q->where('device_id', $value);
                }

              }
            })->paginate( $perpage, ['*'], null, $page );
          }

        } else {
          $listings = SupplyListing::with('userRequested')
          ->with('userFixed')
          ->with('supply:id,name')
          ->with('device:id,name')
          ->whereIn('device_id', $devices_arr)
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
}
