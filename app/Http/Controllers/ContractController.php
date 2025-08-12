<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class ContractController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('contract', User::class);
        $page_title = __('Contracts');
        $page_description = __('All the Contracts');
        $contracts = Contract::get();
        return view('pages.contracts.index', compact('page_title', 'page_description', 'contracts'));
    }

    public function store_files(Request $request)
    {
        $request->validate([
            'files' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('files');
        $originalExtension = $file->getClientOriginalExtension();

        // Ensure directory exists
        $path = 'public/contracts';
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        // Store the original file
        $storedFilePath = $file->store($path);
        $fileName = basename($storedFilePath);
        $originalName = $file->getClientOriginalName();

        // If the uploaded file is a PDF, convert it to DOCX
        if (strtolower($originalExtension) === 'pdf') {
            $pdfPath = storage_path('app/' . $storedFilePath);
            $docxPath = storage_path('app/' . $path . '/' . pathinfo($fileName, PATHINFO_FILENAME) . '.docx');

            // Parse PDF and create DOCX
            $pdfParser = new Parser();
            $pdf = $pdfParser->parseFile($pdfPath);
            $text = $pdf->getText();

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addText($text);

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($docxPath);

            // Update the stored file name to the DOCX version
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.docx';
            $storedFilePath = $path . '/' . $fileName;
        }

        // Save to database
        $contract = new Contract();
        $contract->name = $originalName;
        $contract->file_name = $fileName;
        $contract->mime_type = $file->getClientMimeType();
        $contract->size = $file->getSize();
        $contract->save();

        return response()->json([
            'message' => 'File uploaded successfully.',
            'original_name' => $originalName,
            'file_path' => Storage::url($storedFilePath),
        ]);
    }

    public function get_files(Request $request)
    {
        $page = 1;
        $perpage = 10;
        $order_by = 'created_at';
        $sort = 'asc';
        $search = null;

        if ($request->has('pagination')) {
            if (!empty($request->get('pagination')['page'])) {
                $page = $request->get('pagination')['page'];
            }
            if (!empty($request->get('pagination')['perpage'])) {
                $perpage = $request->get('pagination')['perpage'];
            }
        }

        if ($request->has('sort')) {
            if (is_array(request('sort'))) {
                $sort = request('sort')['sort'];
            }
        }

        if ($request->has('query')) {
            if (is_array($request->get('query'))) {
                $search = $request->get('query');
            }
        }

        $files = Contract::where(function ($q) use ($search) {
            if (!$search) return;
            foreach ($search as $key => $value) {
                if ($key == "generalSearch") {
                    $q->whereRaw("name LIKE ?", ['%' . $value . '%']);
                }

                if ($key == "Date") {
                    if (!empty($value['start'] || $value['end'])) {
                        $start = new Carbon($value['start']);
                        $start = $start->startOfDay();
                        $end = new Carbon($value['end']);
                        $end = $end->endOfDay();
                        $q->whereBetween('updated_at', [$start, $end]);
                    }
                }
            }
        })
            ->orderBy($order_by, $sort)
            ->paginate($perpage, ['*'], null, $page);

        $meta = [
            "page" => $files->currentPage(),
            "pages" => intval(count($files) / $perpage),
            "perpage" => $perpage,
            "total" => $files->total(),
            "sort" => $sort,
            "field" => $order_by,
        ];

        return response()->json(['meta' => $meta, 'data' => $files->items()], 200);
    }

    public function download($id)
    {
        // Find contract by ID
        $contract = Contract::findOrFail($id);
        $filePath = 'public/contracts/' . $contract->file_name;
        // Check if file exists
        if (!Storage::exists($filePath)) {
            return response()->json([
                'message' => 'File not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        // Download file with original name
        return Storage::download($filePath, $contract->name);
    }

    public function destroy(Request $request,$id)
    {
        $locale = $request->input('locale', session('locale', config('app.locale')));
        \App::setLocale($locale);
        // Find contract record
        $contract = Contract::findOrFail($id);

        $filePath = 'public/contracts/' . $contract->file_name;

        // Delete file from storage if it exists
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete database record
        $contract->delete();

        return response()->json([
            'success' => __('script.contract_deleted') // Changed from 'message' to 'success'
        ], Response::HTTP_OK);
    }
}
