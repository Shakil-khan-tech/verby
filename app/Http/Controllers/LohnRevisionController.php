<?php

namespace App\Http\Controllers;

use App\Models\LohnRev;
use App\Models\Lohn;
use App\Models\Employee;
// use Illuminate\Http\Request;
use DB;
use File;
use Illuminate\Support\Facades\Storage;
// use Debugbar;
use Carbon\Carbon;
use App\Http\Traits\EmployeeTrait;

use Illuminate\Http\Request;

class LohnRevisionController extends Controller
{
    use EmployeeTrait;

    public function __construct()
    {
        $this->middleware('auth');
        // $this->authorizeResource('user');
    }

    public function view($id)
    {
        $this->authorize('manage', Payroll::class);
        $this->authorize('viewAny', Employee::class);
        // $cDate = new Carbon($date);
        // $firstDayYMD = $cDate->firstOfMonth()->format("Y-m-d H:i:s");
        //
        // $lDate = new Carbon($cDate);
        // $firstDayLastMonthYMD = $lDate->subMonth(1)->firstOfMonth()->format("Y-m-d H:i:s");
        //
        // $dateYM = $cDate->format("Y-m");
        // $firstDay = $cDate->firstOfMonth()->format("d.m.Y");
        // $lastDay = $cDate->lastOfMonth()->format("d.m.Y");

        $revision = LohnRev::findOrFail( $id );
        $employee = Employee::where('id', '=', $revision->employee_id)->firstOrFail();

        $cDate = new Carbon($revision->date);
        $firstDayYMD = $cDate->firstOfMonth()->format("Y-m-d H:i:s");
        $lDate = new Carbon($cDate);
        $firstDayLastMonthYMD = $lDate->subMonth(1)->firstOfMonth()->format("Y-m-d H:i:s");
        
        $firstDay = $cDate->firstOfMonth()->format("d.m.Y");
        $lastDay = $cDate->lastOfMonth()->format("d.m.Y");

        $konto = Lohn::select('KONTO_Ferie', 'KONTO_13monats')->where('employee_id', '=', $employee->id)->where('date', '=', $firstDayLastMonthYMD)->get()->first();

        $globall = DB::table('globall')->first();

        //predifend vars
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

        if ( $revision->konfirm == 1 ) {
          $employee->decki200 = $revision->decki200;
          $employee->decki250 = $revision->decki250;
          $oret           = $revision->oret;
          $employee->EhChf    = $revision->ehchf;
          $employee->BVG      = $revision->BVG;
          $AHV      = $revision->AHV;
          $ALV      = $revision->ALV;
          $NBUV     = $revision->NBUV;
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
          $baza = ( $revision->konfirm == 1 ) ? $revision->rroga : $employee->rroga ;
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

        $ktg = $revision->B_KTG_1 * $revision->B_KTG_2;
        $unfall = $revision->B_unfall_1 * $revision->B_unfall_2;
        $b_bonnus1 = $revision->B_bonnus1_2;
        $b_bonnus2 = $revision->B_bonnus2_2;
        $A6 = $revision->A_Verplegung_1 * $revision->A_Verplegung_2; $A6 = number_format((float)$A6, 2, '.', '') ;
        $a_bonnus1 = $revision->A_bonnus1_2;
        $a_bonnus2 = $revision->A_bonnus2_2;
        $timestamp = $revision->timestamp;

        $a_total = $A1+$A2+$A3+$A4+$A5+$A6+$A7 + $a_bonnus1 + $a_bonnus2;
        $nettoLohn1 = $total_1+$dck250+$dck200+$unfall+$ktg+$b_bonnus1+$b_bonnus2 - $a_total;
        $tatimiSelekt = 0;

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
                  // echo $line;
                  // Debugbar::info('line: ' . $line);
                  // Debugbar::info('search1: ' . $line);
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
                    // $tatimiSelekt=$tatimi/100;
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

        $page_title = __('Payroll Revision');

        $dateYM = $cDate->format("Y-m");
        $firstDay = $cDate->firstOfMonth()->format("d.m.Y");
        $lastDay = $cDate->lastOfMonth()->format("d.m.Y");
        // $page_description = $cDate;
        $page_description = $revision->timestamp;

        return view('pages.lohnrev.show', compact(
          'revision', 'timestamp',
          'page_title', 'page_description', 'employee', 'dateYM', 'firstDay', 'lastDay', 'firstDayYMD',
          'oret', 'baza', 'p1', 'p2', 'p3', 'total_1',
          'dck200', 'dck250',
          'AHV', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'ALV', 'NBUV', 'b_bonnus1', 'b_bonnus2', 'a_bonnus1', 'a_bonnus2', 'a_total',
          'nettoLohn1', 'tatimiSelekt', 'Quellensteuer', 'NettoLohn2', 'KONTO_13monats'
        ));
    }
}
