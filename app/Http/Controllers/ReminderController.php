<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class ReminderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('manage_reminder', User::class);
        $page_title = __('Reminders');
        $page_description = __('All the Reminders');
        return view('pages.reminders.index', compact('page_title', 'page_description'));
    }

    public function getEmployees(Request $request)
    {
        $perPage = $request->input('pagination.perpage', 10);
        $page = $request->input('pagination.page', 1);

        // Total number of contracts in the system
        $totalContracts = Contract::count();

        // Get all new employees
        $employees = Employee::where('employee_type', 1)
            ->get()
            ->filter(function ($employee) use ($totalContracts) {
                // Count signed contracts
                $signedCount = \DB::table('employee_contracts')
                    ->where('employee_id', $employee->id)
                    ->where('is_sign', 1)
                    ->count();

                return $signedCount < $totalContracts;
            })
            ->map(function ($employee) use ($totalContracts) {
                // Get signed contract IDs
                $signedContractIds = \DB::table('employee_contracts')
                    ->where('employee_id', $employee->id)
                    ->where('is_sign', 1)
                    ->pluck('contract_id');

                // Get unsigned contracts (is_sign = 0)
                $unsignedContracts = Contract::where(function ($query) use ($employee) {
                    $query->whereIn('id', function ($sub) use ($employee) {
                        $sub->select('contract_id')
                            ->from('employee_contracts')
                            ->where('employee_id', $employee->id)
                            ->where('is_sign', 0);
                    })
                        ->orWhereNotIn('id', function ($sub) use ($employee) {
                            $sub->select('contract_id')
                                ->from('employee_contracts')
                                ->where('employee_id', $employee->id);
                        });
                })->get();


                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'surname' => $employee->surname,
                    'email' => $employee->email,
                    'function' => $employee->function,
                    'gender' => $employee->gender,
                    'signed_count' => $signedContractIds->count(),
                    'total_contracts' => $totalContracts,
                    'unsigned_contracts' => $unsignedContracts,
                    'pending_contract_names' => $unsignedContracts->pluck('name')->toArray(),
                ];
            })->values();

        // Manual pagination
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $employees->forPage($page, $perPage),
            $employees->count(),
            $perPage,
            $page
        );

        return response()->json([
            'meta' => [
                'page' => $page,
                'pages' => ceil($employees->count() / $perPage),
                'perpage' => $perPage,
                'total' => $employees->count(),
            ],
            'data' => $paginatedData->items()
        ]);
    }
}
