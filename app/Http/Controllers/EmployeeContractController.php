<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Contract;
use App\Models\Country;
use App\Models\EmployeeContract;
use Illuminate\Support\Facades\Storage;
use Auth;
use Debugbar;

class EmployeeContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Employee $employee)
    {
        $this->authorize('viewAny', EmployeeContract::class);
        $page_title = $employee->fullname;
        $page_description = __('Employee');
        $item_active = 'contracts';
        $pushimi = DB::select(
            '
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
                ' ORDER BY fillimi DESC'
        );

        return view('pages.employees.employee_contract', compact('page_title', 'page_description', 'employee', 'item_active', 'pushimi'));
    }

    public function get_contracts(Employee $employee, Request $request)
    {
        $page = $request->input('pagination.page', 1);
        $perpage = $request->input('pagination.perpage', 10);
        $order_by = $request->input('sort.field', 'contracts.created_at');
        $sort = $request->input('sort.sort', 'asc');
        $searchTerm = $request->input('query.generalSearch');

        // Subquery to get latest employee_contract record per contract_id for the current employee
        $latestContracts = DB::table('employee_contracts')
            ->select(DB::raw('MAX(id) as id'))
            ->where('employee_id', $employee->id)
            ->groupBy('contract_id');

        $query = Contract::leftJoin('employee_contracts', function ($join) use ($employee, $latestContracts) {
            $join->on('contracts.id', '=', 'employee_contracts.contract_id')
                ->whereIn('employee_contracts.id', $latestContracts);
        })
            ->select([
                'contracts.id as id',
                'contracts.name as contract_name',
                'contracts.file_name as contract_file',
                'contracts.mime_type as contract_mime',
                'contracts.size as contract_size',
                'employee_contracts.id as employee_contract_id',
                'employee_contracts.name as employee_file_name',
                'employee_contracts.file_name as employee_file',
                'employee_contracts.mime_type as employee_mime',
                'employee_contracts.size as employee_size',
                'employee_contracts.is_sign as is_signed',
                'employee_contracts.updated_at',
            ]);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('contracts.name', 'like', "%{$searchTerm}%");
            });
        }

        $query->orderBy($order_by, $sort);

        $contracts = $query->paginate($perpage, ['*'], 'page', $page);

        $meta = [
            'page' => $contracts->currentPage(),
            'pages' => $contracts->lastPage(),
            'perpage' => $perpage,
            'total' => $contracts->total(),
            'sort' => $sort,
            'field' => $order_by,
        ];

        return response()->json([
            'meta' => $meta,
            'data' => $contracts->items(),
        ]);
    }

    public function store_contract(Employee $employee, Request $request)
    {
        $this->authorize('create', EmployeeContract::class);
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'employee_id' => 'required|exists:employees,id',
            'file' => 'required|file|max:2048',
        ]);

        $file = $request->file('file');
        $path = 'public/contracts/signature';

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        $storedFilePath = $file->store($path); // e.g., public/contracts/signature/abc.pdf
        $fileName = basename($storedFilePath);

        $employeeContract = EmployeeContract::updateOrCreate(
            [
                'contract_id' => $request->contract_id,
                'employee_id' => $employee->id,
            ],
            [
                'name' => $file->getClientOriginalName(),
                'file_name' => $fileName,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]
        );

        return response()->json([
            'message' => $employeeContract->wasRecentlyCreated
                ? __('script.file_uploaded')
                : __('script.file_updated'),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => Storage::url($storedFilePath),
        ]);
    }

    public function show(Employee $employee, $contractID, Request $request)
    {
        $this->authorize('viewAny', EmployeeContract::class);

        // Get all devices for this employee
        $deviceNames = $employee->devices()->pluck('name')->implode(', ');
        $contract = Contract::findOrFail($contractID);
        $filePath = Storage::disk('public')->path('contracts/' . $contract->file_name);
        $countryID = isset($employee->country_id) && !empty($employee->country_id) ? $employee->country_id : NULL;
        $taxCode = isset($employee->TAX) && !empty($employee->TAX) ? 'Ja' : 'Nein';
        $countryName = '';
        if ($countryID !== NULL) {
            $getCountry = Country::findOrFail($countryID);
            $countryName = session('locale') == 'en' ? $getCountry->name_en : $getCountry->name_de;
        }

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found: ' . $contract->file_name);
        }

        // Fixed the typo here (removed underscore after id)
        $outputFileName = $employee->id . '_' . $contract->file_name;
        $outputPath = Storage::disk('public')->path('contracts/' . $outputFileName);

        try {
            $template = new TemplateProcessor($filePath);
            $married = isset($employee->maried) && $employee->maried == 1 ? 'Verheiratet' : 'Einzel';
            $template->setValue('EMPLOYEE_NAME', $employee->name . ' ' . $employee->surname);
            $template->setValue('EMPLOYEE_ID', $employee->id);
            $template->setValue('EMPLOYEE_PIN', $employee->pin);
            $template->setValue('Marital Status', $married);
            $template->setValue('Phone', $employee->phone);
            $template->setValue('AHV number', $employee->AHV);
            $template->setValue('ZIPCode', $employee->PLZ);
            $template->setValue('Birthdate', $employee->DOB);
            $template->setValue('Place', $employee->ORT);
            $template->setValue('Hotel', $deviceNames);
            $template->setValue('Tax', $taxCode);
            if (!empty($countryName)) {
                $template->setValue('Nationality', $countryName);
            }
            $template->setValue('EMPLOYEE_ADDRESS', $employee->strasse);
            $template->setValue('DATE', now()->format('d.m.Y'));
            $template->saveAs($outputPath);

            // Download the file with a more descriptive name
            return Storage::disk('public')->download(
                'contracts/' . $outputFileName,
                'Contract_' . $employee->name . '_' . $contract->name . '.docx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process document: ' . $e->getMessage());
        }
    }

    public function signedDocument($contractID, Request $request)
    {
        $this->authorize('create', EmployeeContract::class);
        $contract = EmployeeContract::findOrFail($contractID);
        $filePath = 'public/contracts/signature/' . $contract->file_name;
        // Check if file exists
        if (!Storage::exists($filePath)) {
            return response()->json([
                'message' => 'File not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        // Download file with original name
        return Storage::download($filePath, $contract->name);
    }
    // In EmployeeContractController.php
    public function sign_status(Request $request, EmployeeContract $contract)
    {
        $this->authorize('create', EmployeeContract::class);

        // ✅ Optionally get locale from request or session
        $locale = $request->input('locale', session('locale', config('app.locale')));
        \App::setLocale($locale);

        // ✅ Validate input
        $validated = $request->validate([
            'is_signed' => 'required|boolean'
        ]);

        // ✅ Update contract status
        $contract->update(['is_sign' => $validated['is_signed']]);

        // ✅ Return translated response
        return response()->json([
            'success' => true,
            'message' => $validated['is_signed']
                ? __('script.signed_success')
                : __('script.unsigned_success'),
        ]);
    }
}
