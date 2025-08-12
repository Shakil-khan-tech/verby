<?php

namespace App\Http\Controllers;

use App\Models\Lohn;
use App\Models\LohnRev;
use App\Models\Employee;
use Illuminate\Http\Request;
use DB;
use File;
use Illuminate\Support\Facades\Storage;
use Debugbar;
use App\Http\Traits\EmployeeTrait;

use Carbon\Carbon;

class LohnController extends Controller
{
    use EmployeeTrait;

    protected $timestamp;
    protected $oret;
    protected $baza;
    protected $p1;
    protected $p2;
    protected $p3;
    protected $total_1;
    protected $AHV;
    protected $A1;
    protected $A2;
    protected $A3;
    protected $A4;
    protected $A5;
    protected $A6;
    protected $A7;
    protected $dck200;
    protected $dck250;
    protected $ALV;
    protected $NBUV;
    protected $ktg;
    protected $unfall;
    protected $a_bonnus1;
    protected $a_bonnus2;
    protected $b_bonnus1;
    protected $b_bonnus2;
    protected $a_total;
    protected $nettoLohn1;
    protected $tatimiSelekt;
    protected $Quellensteuer;
    protected $KONTO_ferie;
    protected $KONTO_13monats;

    public function __construct()
    {
        $this->middleware('auth');
        //predifend vars
        // $this->timestamp = Carbon::now()->format("d.m.Y");
        $this->timestamp = Carbon::now();
        $this->oret = $this->baza = $this->p1 = $this->p2 = $this->p3 = $this->total_1 = $this->AHV
        = $this->A1 = $this->A2 = $this->A3 = $this->A4 = $this->A5 = $this->A6 = $this->A7
        = $this->dck200 = $this->dck250 = $this->ALV = $this->NBUV = $this->ktg = $this->unfall
        = $this->a_bonnus1 = $this->a_bonnus2 = $this->b_bonnus1 = $this->b_bonnus2
        = $this->a_total = $this->nettoLohn1 = $this->tatimiSelekt = $this->Quellensteuer
        = $this->KONTO_ferie = $this->KONTO_13monats = 0;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('manage', Payroll::class);
        $this->authorize('viewAny', Employee::class);

        $page_title = __('Payroll');
        $page_description = __('Select an employee first');

        return view('pages.lohn.index', compact('page_title', 'page_description'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Employee $employee, $date)
    {
      $this->authorize('manage', Payroll::class);
      $this->authorize('viewAny', Employee::class);

      $cDate = new Carbon($date);
      $firstDayYMD = $cDate->firstOfMonth()->format("Y-m-d H:i:s");

      $lDate = new Carbon($cDate);
      $firstDayLastMonthYMD = $lDate->subMonth(1)->firstOfMonth()->format("Y-m-d H:i:s");

      $dateYM = $cDate->format("Y-m");
      $firstDay = $cDate->firstOfMonth()->format("d.m.Y");
      $lastDay = $cDate->lastOfMonth()->format("d.m.Y");

      // DB::enableQueryLog(); // Enable query log
      $lohn = Lohn::where('employee_id', '=', $employee->id)->where('date', '=', $firstDayYMD)->get()->first();
      // dd(DB::getQueryLog());
      //return $lohn;
      $revisions = LohnRev::where('employee_id', '=', $employee->id)->where('date', '=', $firstDayYMD)->get();

      // return $revisions;

      // DB::enableQueryLog(); // Enable query log
      $konto = Lohn::select('KONTO_Ferie', 'KONTO_13monats')->where('employee_id', '=', $employee->id)->where('date', '=', $firstDayLastMonthYMD)->get()->first();
      // dd(DB::getQueryLog());

      if ( empty($lohn) ) {

        $l = new Lohn;
        $l->employee_id = $employee->id;
        $l->date = $firstDayYMD;
        $l->oret = 0;
        try {
          $l->save();
          $lohn = $l;
        } catch (\Exception $ex) {
          // return response()->json(['message' => "Punëtori nuk mund të shtohet", 'errors' => ['exception' => $ex->getMessage()] ], 500);
          Debugbar::addException($ex);
        }
      }

      $globall = DB::table('globall')->first();

      //predifend vars
      // $timestamp = Carbon::now()->format("d.m.Y");
      $timestamp = Carbon::now();
      $oret = $baza = $p1 = $p2 = $p3 = $total_1 = $AHV = $A1 = $A2 = $A3 = $A4 = $A5 = $A6 = $A7
      = $dck200 = $dck250 = $ALV = $NBUV = $ktg = $unfall = $a_bonnus1 = $a_bonnus2 = $b_bonnus1 = $b_bonnus2
      = $a_total = $nettoLohn1 = $tatimiSelekt = $Quellensteuer
      = $KONTO_ferie = $KONTO_13monats = 0;
      $employee->Perqind1 = $employee->Perqind1 ? $employee->Perqind1 : 0;
      $employee->Perqind2 = $employee->Perqind2 ? $employee->Perqind2 : 0;
      $employee->Perqind3 = $employee->Perqind3 ? $employee->Perqind3 : 0;

      if ( isset( $employee->PartTime ) && $employee->PartTime == 1 ) {
        $oret = $this->calculate_hours($employee, $firstDay, $lastDay);
      }

      if ( isset( $lohn->konfirm ) && $lohn->konfirm == 1 ) {
        $employee->decki200 = $lohn->decki200;
        $employee->decki250 = $lohn->decki250;
        $oret           = $lohn->oret;
        $employee->EhChf    = $lohn->ehchf;
        $employee->BVG      = $lohn->BVG;
        $AHV      = $lohn->AHV;
        $ALV      = $lohn->ALV;
        $NBUV     = $lohn->NBUV;
      } else {
        $AHV      = $globall->AHV;
        $ALV      = $globall->ALV;
        $NBUV     = $globall->NBUV;
      }

      if ( $employee->PartTime == 1 ) {
        $baza = $oret * floatval($employee->EhChf); //xhevat
        // return;
        $p1   = floatval( ($employee->Perqind1/100) * $baza );
        $p1   = number_format( (float)$p1, 2, '.', '' );
        $p2   = floatval( ($employee->Perqind2/100) * $baza );
        $p2   = number_format( (float)$p2, 2, '.', '' );
      } else {
        $baza = ( isset($lohn) && $lohn->konfirm == 1 ) ? $lohn->rroga : $employee->rroga ;
        $baza = floatval( $baza ) ;
        $p1   = 0;
        $p2   = 0;
      }

      $p3 = ( floatval($employee->Perqind3)/100) * (floatval($baza) + floatval($p1) + floatval($p2));
      $p3   = number_format((float)$p3, 2, '.', '');
      $total_1 = $baza + $p1 + $p2 + $p3;
      $total_1 = number_format((float)$total_1, 2, '.', '');
      $dck200 = $employee->decki200 * 200;
      $dck250 = $employee->decki250 * 250;
      $A1 = ($AHV / 100) * $total_1; $A1 = number_format((float)$A1, 2, '.', '') ;
      $A2 = ($ALV / 100) * $total_1; $A2 = number_format((float)$A2, 2, '.', '') ;
      $A3 = $employee->BVG; $A3 = number_format((float)$A3, 2, '.', '') ;
      $A4 = ($NBUV / 100) * $total_1; $A4 = number_format((float)$A4, 2, '.', '') ;
      $A5 = (2.169 / 100) * $total_1 / 2; $A5 = number_format((float)$A5, 2, '.', '') ;
      $A7 = (0.4 / 100) * $total_1; $A7 = number_format((float)$A7, 2, '.', '') ;
      if ( !empty($lohn) ) {
        $ktg = $lohn->B_KTG_1 * $lohn->B_KTG_2;
        $unfall = $lohn->B_unfall_1 * $lohn->B_unfall_2;
        $b_bonnus1 = $lohn->B_bonnus1_2;
        $b_bonnus2 = $lohn->B_bonnus2_2;
        $A6 = $lohn->A_Verplegung_1 * $lohn->A_Verplegung_2; $A6 = number_format((float)$A6, 2, '.', '') ;
        $a_bonnus1 = $lohn->A_bonnus1_2;
        $a_bonnus2 = $lohn->A_bonnus2_2;
        $timestamp = $lohn->timestamp;
      }

      $a_total = $A1+$A2+$A3+$A4+$A5+$A6+$A7 + $a_bonnus1 + $a_bonnus2;
      $nettoLohn1 = $total_1+$dck250+$dck200+$unfall+$ktg+$b_bonnus1+$b_bonnus2 - $a_total;
      $tatimiSelekt = 0;

      //If Lohn doesnt exist
      /*if ( empty($lohn) ) {
        $page_title = 'Mrazet';
        $page_description = $cDate;
        return view('pages.lohn.show', compact(
          'page_title', 'page_description', 'user', 'dateYM', 'firstDay', 'lastDay',
          'oret', 'baza', 'p1', 'p2', 'p3', 'total_1',
          'dck200', 'dck250',
          'lohn', 'AHV', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'ALV', 'NBUV', 'a_bonnus2', 'a_total',
          'nettoLohn1', 'tatimiSelekt', 'Quellensteuer'
        ));
      }*/

      //predifend vars
      // $lohn->B_KTG_1 = isset( $lohn->B_KTG_1 ) ? $lohn->B_KTG_1 : 0;





      // $tarifa = public_path() . "/tarifa/$employee->ORT.txt";
      try {
        $tarifa = Storage::get("/tarifa/$employee->ORT.txt");
      } catch (\Exception $e) {
        $tarifa = '';
      }

      if( File::exists( $tarifa )) {
        $handle = fopen($tarifa, "r");
        if ($handle) {
            $search1 = $employee->TAX;
            $s2 =  $total_1 + $dck250 + $dck200 + $unfall + $ktg + $b_bonnus1 + $b_bonnus2;
            $search2 = "0" . $s2 . "0000";
            while (($line = fgets($handle)) !== false) {
                if ( !empty( $search1 )) {
                  if(stristr( $line, $search1 ) ) {
                  $line2 = $line;
                  $line = substr($line, 24);
                  $rogaChk = explode('000000', $line);
                  $rogaChk = $rogaChk[0];
                  $tatimi = explode('000', $line);
                  $tatimi = end($tatimi);
                  if ($rogaChk > $s2) {
                    $rogaChk;
                    break;
                  }
                  $rogaSelekt = $rogaChk;
                  $tatimi = floatval($tatimi);
                  $tatimiSelekt = ($tatimi != 0) ? $tatimi/100 : 0;
                }
              }
            }

            fclose($handle);
        }
      }

      $Quellensteuer=($tatimiSelekt/100) * ( $total_1 + $dck250 + $dck200 + $unfall + $ktg + $b_bonnus1 + $b_bonnus2 );
      $Quellensteuer=number_format((float)$Quellensteuer, 2, '.', '');
      $NettoLohn2 = $nettoLohn1-($p1+$p3+$Quellensteuer);
      $NettoLohn2=number_format((float)$NettoLohn2, 2, '.', '');
      $KONTO_ferie=$p1;
      $KONTO_13monats=$p3;


      if ( !empty($konto) ) {
        $KONTO_ferie = $p1 + floatval($konto->KONTO_Ferie);
        $KONTO_13monats = floatval($p3) + floatval($konto->KONTO_13monats);
      }
      //calculation variables

      //other variables
      if ( empty($lohn) ) {
        $page_title = __('Empty Payroll');
      } else {
        $page_title = __('Payroll');
      }


      $dateYM = $cDate->format("Y-m");
      $firstDay = $cDate->firstOfMonth()->format("d.m.Y");
      $lastDay = $cDate->lastOfMonth()->format("d.m.Y");
      $page_description = $cDate->format("Y-F");
      // $item_active = 'personal';

      //If Lohn doesnt exist
      if ( empty($lohn) ) {
        /*
        $page_title = 'Mrazet';
        $page_description = $cDate;
        return view('pages.lohn.show', compact(
          'lohn', 'timestamp',
          'page_title', 'page_description', 'user', 'dateYM', 'firstDay', 'lastDay',
          'oret', 'baza', 'p1', 'p2', 'p3', 'total_1',
          'dck200', 'dck250',
          'lohn', 'AHV', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'ALV', 'NBUV', 'b_bonnus1', 'b_bonnus2', 'a_bonnus2', 'a_total',
          'nettoLohn1', 'tatimiSelekt', 'Quellensteuer', 'NettoLohn2', 'KONTO_13monats'
        ));
        */
      }

      $pushimi = DB::select('
        SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
        FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
        ' ORDER BY fillimi DESC'
      );

      return view('pages.lohn.show', compact(
        'lohn', 'revisions', 'timestamp',
        'page_title', 'page_description', 'employee', 'dateYM', 'firstDay', 'lastDay', 'firstDayYMD',
        'oret', 'baza', 'p1', 'p2', 'p3', 'total_1',
        'dck200', 'dck250',
        'lohn', 'AHV', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'ALV', 'NBUV', 'b_bonnus1', 'b_bonnus2', 'a_bonnus1', 'a_bonnus2', 'a_total',
        'nettoLohn1', 'tatimiSelekt', 'Quellensteuer', 'NettoLohn2', 'KONTO_13monats', 'pushimi'
      ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('manage', Payroll::class);
        $this->authorize('viewAny', Employee::class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lohn  $lohn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lohn $lohn)
    {
        $this->authorize('manage', Payroll::class);
        $this->authorize('viewAny', Employee::class);
        // $lohn = Lohn::updateOrCreate(
        //     ['userID' => request('date'), 'destination' => 'San Diego'],
        //     ['price' => 99]
        // );
        if ( $request->has('LohnKONTO_13monats') ) {
          if ( $lohn->exists ) {
            $l= Lohn::findOrFail($lohn->id);
            $l->KONTO_13monats = '0.00';
            $l->KONTO_13monats_PAY = request('LohnKONTO_13monats');
            $l->konfirm = 0;
            try {
              $l->save();
              return redirect()->back()->with(['success' => __('13th Salary paid'), 'id' => $l->id] );
            } catch (\Exception $ex) {
              return redirect()->back()->with(['error' => __('13th Salary cannot be paid'), 'message' => ['exception' => $ex->getMessage()]]);
            }
          } else {
            $l = new Lohn;
            $l->KONTO_13monats = '0.00';
            $l->KONTO_13monats_PAY = request('LohnKONTO_13monats');
            $l->date = request('date');
            $l->employee_id = request('employee_id');
            $l->oret = '0';
            $l->konfirm = 0;
            // $l->ehchf = '0';
            // $l->BVG = '0';
            // $l->KONTO_Ferie = '0';
            // $l->KONTO_Ferie_PAY = '0';
            // $l->KONTO_13monats = '0';
            try {
              $l->save();
              return response()->json( ['success' => __('Payroll created'), 'id' => $l->id], 200 );
            } catch (\Exception $ex) {
              return response()->json(['message' => __('Payroll cannot be created'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
            }
          }
        }
        if ( $request->has('LohnKONTO_Ferie') ) {
          if ( $lohn->exists ) {
            $l= Lohn::findOrFail($lohn->id);
            $l->KONTO_Ferie = '0.00';
            $l->KONTO_Ferie_PAY = request('LohnKONTO_Ferie');
            $l->konfirm = 0;
            try {
              $l->save();
              return redirect()->back()->with(['success' => __('Annual leave paid'), 'id' => $l->id] );
            } catch (\Exception $ex) {
              return redirect()->back()->with(['error' => __('Annual leave cannot be paid'), 'message' => ['exception' => $ex->getMessage()]]);
            }
          } else {
            $l = new Lohn;
            $l->KONTO_Ferie = '0.00';
            $l->KONTO_Ferie_PAY = request('LohnKONTO_Ferie');
            $l->date = request('date');
            $l->employee_id = request('employee_id');
            $l->oret = '0';
            $l->konfirm = 0;
            try {
              $l->save();
              return response()->json( ['success' => __('Payroll created'), 'id' => $l->id], 200 );
            } catch (\Exception $ex) {
              return response()->json(['message' => __('Payroll cannot be created'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
            }
          }
        }
        if ( $request->has('update') ) {
          if ( $lohn->exists ) {
            $l= Lohn::findOrFail($lohn->id);
            $l->konfirm = 0;
            $l->B_KTG_1 = request('B_KTG_1');
            $l->B_KTG_2 = request('B_KTG_2');
            $l->B_unfall_1 = request('B_unfall_1');
            $l->B_unfall_2 = request('B_unfall_2');
            $l->B_bonnus1_1 = request('B_bonnus1_1');
            $l->B_bonnus1_2 = request('B_bonnus1_2');
            $l->B_bonnus2_1 = request('B_bonnus2_1');
            $l->B_bonnus2_2 = request('B_bonnus2_2');
            $l->A_Verplegung_1 = request('A_Verplegung_1');
            $l->A_Verplegung_2 = request('A_Verplegung_2');
            $l->A_bonnus1_1 = request('A_bonnus1_1');
            $l->A_bonnus1_2 = request('A_bonnus1_2');
            $l->A_bonnus2_1 = request('A_bonnus2_1');
            $l->A_bonnus2_2 = request('A_bonnus2_2');
            try {
              $l->save();
              return redirect()->back()->with(['success' => __('Payroll updated'), 'id' => $l->id] );
            } catch (\Exception $ex) {
              return redirect()->back()->with(['error' => __('Payroll cannot be updated'), 'message' => ['exception' => $ex->getMessage()]]);
            }
          } else {
            $l = new Lohn;
            $l->date = request('date');
            $l->employee_id = request('employee_id');
            $l->oret = '0';
            $l->konfirm = 0;

            $l->B_KTG_1 = request('B_KTG_1');
            $l->B_KTG_2 = request('B_KTG_2');
            $l->B_unfall_1 = request('B_unfall_1');
            $l->B_unfall_2 = request('B_unfall_2');
            $l->B_bonnus1_1 = request('B_bonnus1_1');
            $l->B_bonnus1_2 = request('B_bonnus1_2');
            $l->B_bonnus2_1 = request('B_bonnus2_1');
            $l->B_bonnus2_2 = request('B_bonnus2_2');
            $l->A_Verplegung_1 = request('A_Verplegung_1');
            $l->A_Verplegung_2 = request('A_Verplegung_2');
            $l->A_bonnus1_1 = request('A_bonnus1_1');
            $l->A_bonnus1_2 = request('A_bonnus1_2');
            $l->A_bonnus2_1 = request('A_bonnus2_1');
            $l->A_bonnus2_2 = request('A_bonnus2_2');
            try {
              $l->save();
              return response()->json( ['success' => __('Payroll created and updated'), 'id' => $l->id], 200 );
            } catch (\Exception $ex) {
              return response()->json(['message' => __('Payroll cannot be created'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
            }
          }
        }
        if ( $request->has('konfirm') ) {
          if ( $lohn->exists ) {
            $l= Lohn::findOrFail($lohn->id);
            $l->konfirm = 1;
            $l->rroga = request('baza');
            $l->oret = request('oret');
            $l->ehchf = request('ehchf');
            $l->BVG = request('bvg');
            $l->decki200 = request('decki200');
            $l->decki250 = request('decki250');
            $l->AHV = request('ahv');
            $l->ALV = request('alv');
            $l->NBUV = request('nbuv');
            $l->KONTO_Ferie = floatval(request('KONTO_Ferie')) > 0 ? '' : request('KONTO_Ferie');
            $l->KONTO_13monats = floatval(request('KONTO_13monats')) > 0 ? '' : request('KONTO_13monats');
            $l->timestamp = Carbon::now();

            try {
              $l->save();
              $lrev = $l->replicate();
              $lrev = $l->toArray();
              // LohnRev::firstOrCreate($lrev);
              LohnRev::create($lrev);

              return redirect()->back()->with(['success' => __('Payroll confirmed'), 'id' => $l->id] );
            } catch (\Exception $ex) {
              return redirect()->back()->with(['error' => __('Payroll cannot be confirmed'), 'message' => ['exception' => $ex->getMessage()]]);
            }
          } else {
            $l = new Lohn;
            $l->date = request('date');
            $l->employee_id = request('employee_id');
            $l->oret = request('oret');

            $l->konfirm = 1;
            $l->rroga = request('baza');
            $l->ehchf = request('ehchf');
            $l->BVG = request('bvg');
            $l->decki200 = request('decki200');
            $l->decki250 = request('decki250');
            $l->AHV = request('ahv');
            $l->ALV = request('alv');
            $l->NBUV = request('nbuv');
            $l->KONTO_Ferie = request('KONTO_Ferie');
            $l->KONTO_13monats = request('KONTO_13monats');
            $l->timestamp = Carbon::now();
            try {
              $l->save();
              return response()->json( ['success' => __('Payroll created and confirmed'), 'id' => $l->id], 200 );
            } catch (\Exception $ex) {
              return response()->json(['message' => __('Payroll cannot be created'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
            }
          }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lohn  $lohn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lohn $lohn)
    {
        $this->authorize('manage', Payroll::class);
        $this->authorize('viewAny', Employee::class);
    }
}
