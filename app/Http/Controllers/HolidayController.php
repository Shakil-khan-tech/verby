<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Validator;

class HolidayController extends Controller
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
        $this->authorize('viewAny', Holiday::class);

        $holidays = Holiday::all();
        $page_title = __('Manage Holidays');
        $page_description = __('Total: ') . $holidays->count();

        return view('pages.holidays.index', compact('page_title', 'page_description', 'holidays'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Holiday::class);

        Holiday::truncate();
        if ($request->has('holidays')) {
          try {
            Holiday::upsert(
                request('holidays'),
              ['name', 'month_day'],
              ['name']
            );
          } catch (\Exception $e) {
            return redirect()->back()->with(['error' => __('Cannot update holidays'), 'message' => ['exception' => $e->getMessage()]])->withInput();
          }
        }

        $holidays = Holiday::all();
        $page_title = __('Manage Holidays');
        $page_description = __('Total: ') . $holidays->count();

        return redirect()->route('holidays.index')->with(['success' => __('Holidays updated sucessfully')]);
    }
}
