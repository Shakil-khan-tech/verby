<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Device;
use Illuminate\Http\Request;
use DB;
use Carbon\CarbonPeriod;

class VacationController extends Controller
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
        $this->authorize('viewAny', Vacation::class);

        $page_title = __('Vacations');
        $page_description = __('All the vacations');
        // $employees = Employee::all()->take(10);
        $employees = Employee::all();
        $pushimi = DB::select('
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id
          ORDER BY fillimi DESC'
        );

        return view('pages.vacation.index', compact('page_title', 'page_description', 'employees'));
    }

    /**
     * Get all vacations in json format.
     *
     * @return json
     */
    public function getall(Request $request)
    {
        $this->authorize('viewAny', Vacation::class);

        //get devices that the user has access to
        $devices_arr = Device::available()->pluck('id')->toArray();

        $sql =
        'SELECT DISTINCT CONCAT_WS("|",pushimi.employee_id,fillimi,mbarimi) as RecordID,
                DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi,
                DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi,
                DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days,
                employees.name,
                employees.surname,
                employees.id as emp_id,
                employees.function
        FROM pushimi
            INNER JOIN employees ON pushimi.employee_id = employees.id
            INNER JOIN device_employee ON employees.id = device_employee.employee_id
        WHERE device_employee.device_id IN (' . implode(',', $devices_arr) . ')';
          
        if ( $request->has('query') ) {
          if ( isset( request('query')['generalSearch'] ) ) {
            $search = strip_tags( request('query')['generalSearch'] );
            $sql .= ' AND CONCAT(employees.name, " ", employees.surname) LIKE "%' . $search . '%"';
          }
        }
        $sql .= ' ORDER BY fillimi DESC';

        $pushimi = DB::select( $sql );

        $meta = [
          "page" => 1,
          "pages" => 1,
          "perpage" => -1,
          "total" => count($pushimi),
          "sort" => "asc",
          "field" => "RecordID",
          // "kerkimi" => request('query')['generalSearch']
        ];

        return response()->json( ['meta' => $meta, 'data' => $pushimi], 200 );
    }
    /**
     * Delete vacations.
     *
     * @return json
     */
    public function delete(Request $request)
    {
        $this->authorize('delete', Vacation::class);

        $request->validate([
          'records' => 'required'
        ]);
        $sql = 'DELETE FROM pushimi WHERE ';
        $conditions = [];
        foreach (request('records') as $key => $record) {
          $conditions[] = sprintf('(employee_id="%s" and fillimi="%s" and mbarimi="%s")', explode("|", $record)[0], explode("|", $record)[1], explode("|", $record)[2]);
        }
        $sql = $sql . implode( ' OR ', $conditions );
        // return response()->json( ['records' => request('records'), 'sql' => $sql], 200 );
        // return request('records');

        try {
          DB::statement( $sql );

          $pushimi = DB::select(
            'SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
            FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id
            ORDER BY fillimi DESC'
          );
          return response()->json( ['success' => __('Vacation deleted successfully'), 'pushimi' => $pushimi], 200 );
        } catch (\Exception $ex) {
          return response()->json(['message' => __('Vacation cannot be deleted'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
        }
    }


}
