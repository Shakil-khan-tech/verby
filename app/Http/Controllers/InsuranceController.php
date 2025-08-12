<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Record;
use App\Models\Plan;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\Storage;
use Codedge\Fpdf\Fpdf\Fpdf;
use FPDM;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeInsurance;

use App\Http\Traits\InsuranceTrait;
use App\Http\Traits\RecordTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class InsuranceController extends Controller
{
    use InsuranceTrait, RecordTrait;

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
    public function show(Employee $employee, $date)
    {
        $this->authorize('viewAny', Employee::class);
        // return $employee;
        $date = new Carbon($date);
        
        $fields = $this->insuranceFields($employee, $date);
  
        // $pdf = new FPDM( Storage::path( "pdfs/arbeitslosenversicherung.pdf" ) );
        $pdf = new FPDM( Storage::path( "pdfs/fixed.pdf" ) ); //pdftk storage/app/pdfs/arbeitslosenversicherung.pdf output storage/app/pdfs/fixed.pdf
        $pdf->useCheckboxParser = true;
        $pdf->Load($fields, true); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
        $pdf->Merge();
        $pdf->Output();
    }

    /**
     * Send an email to employee with pdf for the month.
     *
     * @return json
     */
    public function insuranceEmail(Employee $employee, $date, Request $request)
    {
        $this->authorize('viewAny', Employee::class);

        if ( !$request->ajax() ) {
          return response()->json(['message' => "Just ajax requests!" ], 500);
        }

        $validator = Validator::make(['email' => $employee->email],[
          'email' => 'required|email'
        ]);

        if( $validator->fails() ){
          return response()->json(['message' => __('Employee email is not valid or is empty'), 'errors' => ['exception' => $validator->errors()] ], 500);
        }

        $date = new Carbon($date);

        $fields = $this->insuranceFields($employee, $date);
        // $pdf = new FPDM( Storage::path( "pdfs/arbeitslosenversicherung.pdf" ) );
        $pdf = new FPDM( Storage::path( "pdfs/fixed.pdf" ) );
        $pdf->useCheckboxParser = true;
        $pdf->Load($fields, true); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
        $pdf->Merge();
        File::isDirectory( Storage::path( "insurances") ) or File::makeDirectory(Storage::path( "insurances"), 0777, true, true);
        $path = Storage::path( "insurances/". Str::random(10) .".pdf" );
        $pdfdoc = $pdf->Output("F", $path);

        Mail::to( $employee->email )->send( new EmployeeInsurance( $path, $date->format('Y-m') ) );
        File::delete($path);
        
        return response()->json( ['success' => __('Email sent successfully')], 200 );
    }

    private function insuranceFields(Employee $employee, $date) {
      $newLocale = setlocale(LC_TIME, 'German');
      if ($newLocale === false) {
          // return '"German" locale is not installed on your machine, it may have a different name on your machine or you may need to install it.';
      }
      $from = $date->copy()->firstOfMonth()->startOfDay();
      $to = $date->copy()->lastOfMonth()->endOfDay();
      $address = [];
      array_push( $address, $employee->PLZ, $employee->ORT1, $employee->strasse );
      $emp_perqind1 = $employee->Perqind1 ? $employee->Perqind1 : 0;
      $emp_perqind2 = $employee->Perqind2 ? $employee->Perqind2 : 0;
      $emp_perqind3 = $employee->Perqind3 ? $employee->Perqind3 : 0;
      /*
      // old formula
      $ahv = $employee->EhChf +
      $employee->EhChf*($emp_perqind1/100) +
      $employee->EhChf*($emp_perqind2/100) +
      $employee->EhChf*($emp_perqind3/100);
      */
      /*
      //formula
      px = [(19.2*p1%)+(19.2*p2%)+19.2]*p3%
      ahv = (19.2*p1)+(19.2*p2)+19.2 + px;
      */
      $p1 = $employee->EhChf*($emp_perqind1/100);
      $p2 = $employee->EhChf*($emp_perqind2/100);
      $px = ($p1 + $p2 + $employee->EhChf) * $emp_perqind3/100;
      $ahv = $p1 + $p2 + $employee->EhChf + $px;      
      $ahv = $this->toDecimalNumber( $ahv );
      
      $period = CarbonPeriod::create($from, '1 day', $to);
      $matrix = $this->get_month_employee_matrix($employee, $from, $to);
      $plans = $this->get_employee_plans($employee, $from, $to);

      $hours = $this->hours_by_day($matrix, $plans);
      $ten_a = $this->toDecimalNumber( $hours['grand_total_time'] * $employee->EhChf * ($emp_perqind1/100) );
      $ten_b = $this->toDecimalNumber( $hours['grand_total_time'] * $employee->EhChf * ($emp_perqind2/100) );
      $ten_c = $this->toDecimalNumber( ( $hours['grand_total_time'] * $employee->EhChf + $ten_a + $ten_b ) * ($emp_perqind3/100) );
      
      $fields = array(
        // 'Eingangsdatum'                                       => Carbon::now()->format('d.m.Y'), //Date of receipt
        'Eingangsdatum'                                       => '', //Date of receipt
        'Name_und_Vorname'                                    => $employee->fullname, //Fullname
        'Pers-Nr'                                             => '', //
        'AHV-Nr'                                              => $employee->AHV ? $employee->AHV : ' ', //
        'PLZ_Wohnort_Strasse'                                 => implode (", ", $address), //Address
        'Geburtsdatum'                                        => $employee->DOB, //Birthday
        'Zivilstand'                                          => $employee->maried ? 'Verheiratet' : 'Unverheiratet', // Civil Status
        'Monat'                                               => utf8_encode($date->localeMonth), // Month
        'Jahr'                                                => $date->format("Y"), // Year
        // 'ausgeübte_Tätigkeit'                                 => $employee->function ? config('constants.functions')[$employee->function] : '', //Activity performed | utf8 problem, because of fpdm not compatible with php 8.1
        'ausgeubte_Tatigkeit'                                 => $employee->function ? config('constants.functions')[$employee->function] : '', //Activity performed
        '1_1'                                                 => $hours['01'],
        '1_2'                                                 => $hours['02'],
        '1_3'                                                 => $hours['03'],
        '1_4'                                                 => $hours['04'],
        '1_5'                                                 => $hours['05'],
        '1_6'                                                 => $hours['06'],
        '1_7'                                                 => $hours['07'],
        '1_8'                                                 => $hours['08'],
        '1_9'                                                 => $hours['09'],
        '1_10'                                                => $hours['10'],
        '1_11'                                                => $hours['11'],
        '1_12'                                                => $hours['12'],
        '1_13'                                                => $hours['13'],
        '1_14'                                                => $hours['14'],
        '1_15'                                                => $hours['15'],
        '1_16'                                                => $hours['16'],
        '1_17'                                                => $hours['17'],
        '1_18'                                                => $hours['18'],
        '1_19'                                                => $hours['19'],
        '1_20'                                                => $hours['20'],
        '1_21'                                                => $hours['21'],
        '1_22'                                                => $hours['22'],
        '1_23'                                                => $hours['23'],
        '1_24'                                                => $hours['24'],
        '1_25'                                                => $hours['25'],
        '1_26'                                                => $hours['26'],
        '1_27'                                                => $hours['27'],
        '1_28'                                                => isset($hours['28']) ? $hours['28'] : '',
        '1_29'                                                => isset($hours['29']) ? $hours['29'] : '',
        '1_30'                                                => isset($hours['30']) ? $hours['30'] : '',
        '1_31'                                                => isset($hours['31']) ? $hours['31'] : '',
        '2_schriftlicher_Arbeitsvertrag_ja'                   => '1',  //CHECKBOX
        '2_schriftlicher_Arbeitsvertrag_nein'                 => '',  //CHECKBOX
        // Have weekly working hours been agreed with the insured person?
        // '3_wöchentliche_Arbeitszeit_vereinbart_ja'            => '',  //CHECKBOX | utf8 problem ...
        '3_wochentliche_Arbeitszeit_vereinbart_ja'            => '',  //CHECKBOX
        // '3_wöchentliche_Arbeitszeit_Std'                      => '', //Hours per week | utf8 problem ...
        '3_wochentliche_Arbeitszeit_Std'                      => '', //Hours per week
        // '3_wöchentliche_Arbeitszeit_vereinbart_nein'          => '',  //CHECKBOX | utf8 problem ...
        '3_wochentliche_Arbeitszeit_vereinbart_nein'          => '',  //CHECKBOX
        '4_wochentliche_Arbeitszeit_Betrieb_Std'              => '42', //Weekly normal working hours in the company
        //Is the company subject to a collective labor agreement?
        '5_Gesamtarbeitsvertrag_ja'                           => '1', //CHECKBOX
        '5_GAV'                                               => 'Reinigungsbranche',
        '5_Gesamtarbeitsvertrag_nein'                         => '', //CHECKBOX
        //Were the insured person offered more working hours in the certified month?
        '6_mehr_Arbeitsstunden_angeboten_ja'                  => $employee->insurance_6_1 == 1 ? '1' : '', //CHECKBOX
        '6_Mehr-Std_pro_Tag'                                  => $employee->insurance_6_2, //Hours per day
        '6_Mehr-Std_pro_Woche'                                => $employee->insurance_6_3, //Hours per week
        '6_Mehr-Std_pro_Monat'                                => $employee->insurance_6_4, //Hours per month
        '6_mehr_Arbeitsstunden_angeboten_nein'                => $employee->insurance_6_5 == 1 ? '1' : '', //CHECKBOX
        // '7_Begründung_Ablehung_Arbeitsangebot1'               => count(explode( "\n", $employee->insurance_7_1 )) > 1 ? explode( "\n", $employee->insurance_7_1 )[0] : $employee->insurance_7_1, | utf8 problem ...
        '7_Begrundung_Ablehung_Arbeitsangebot1'               => count(explode( "\n", $employee->insurance_7_1 )) > 1 ? explode( "\n", $employee->insurance_7_1 )[0] : $employee->insurance_7_1,
        // '7_Begründung_Ablehung_Arbeitsangebot2'               => count(explode( "\n", $employee->insurance_7_1 )) > 1 ? explode( "\n", $employee->insurance_7_1 )[1] : '', | utf8 problem ...
        '7_Begrundung_Ablehung_Arbeitsangebot2'               => count(explode( "\n", $employee->insurance_7_1 )) > 1 ? explode( "\n", $employee->insurance_7_1 )[1] : '',
        '8_AHV-pflichtiger_Bruttlohn_Std'                     => $employee->PartTime == 1 ? $ahv : '',
        '8_AHV-pflichtiger_Bruttlohn_Monat'                   => $employee->PartTime == 0 ? $this->toDecimalNumber($employee->rroga + ($employee->rroga*($emp_perqind3/100))) : '',
        '9_AHV-pflichtiger_Bruttolohn_Std'                    => $employee->PartTime == 1 ? $hours['grand_total_time'] : '',
        // '9_AHV-pflichtiger_Bruttolohn_à_CHF'                  => $employee->PartTime == 1 ? $ahv : '', | utf8 problem ...
        '9_AHV-pflichtiger_Bruttolohn_a_CHF'                  => $employee->PartTime == 1 ? $ahv : '',
        '9_AHV-pflichtiger_Bruttolohn_gleich_CHF'             => $employee->PartTime == 1 ? $this->toDecimalNumber( $hours['grand_total_time'] *  $ahv ) : $this->toDecimalNumber($employee->rroga + ($employee->rroga*($emp_perqind3/100))),
        '10_Grundlohn'                                        => '1', //CHECKBOX
        '10_Grundlohn_CHF'                                    => $employee->PartTime == 1 ? $this->toDecimalNumber( $hours['grand_total_time'] * $employee->EhChf ) : $employee->rroga,
        '10_Feiertagsentschadigung'                           => $employee->PartTime == 1 ? '1' : '', //CHECKBOX
        '10_Feiertagsentschadigung_%'                         => $employee->PartTime == 1 ? $emp_perqind1 : '',
        '10_Feiertagsentschadigung_CHF'                       => $employee->PartTime == 1 ? $ten_a : '',
        '10_Ferienentschadigung'                              => $employee->PartTime == 1 ? '1' : '', //CHECKBOX
        '10_Ferienentschadigung_%'                            => $employee->PartTime == 1 ? $emp_perqind2 : '',
        '10_Ferienentschadigung_CHF'                          => $employee->PartTime == 1 ? $ten_b : '',
        '10_13_monatslohn_Gratifikation'                      => '1', //CHECKBOX
        // '10_13_Monatslohn_Gratifikation_%'                    => number_format( $hours['grand_total_time'] * $employee->EhChf * ($emp_perqind2/100), 2, '.', '' ),
        '10_13_Monatslohn_Gratifikation_%'                    => $emp_perqind3,
        '10_13_Monatslohn_Gratifikation_CHF'                  => $employee->PartTime == 1 ? $ten_c : $this->toDecimalNumber($employee->rroga*$emp_perqind3/100),
        '10_andere_Lohnbestandteile'                          => '', //CHECKBOX
        '10_andere_Lohnbestandteile_welche'                   => '',
        '10_andere_Lohnbestandteile_%'                        => '',
        '10_andere_Lohnbestandteile_CHF'                      => '',
        '11_13_Monatslohn_Gratifikation_wird_noch_ausbezahlt' => '1', //CHECKBOX
        '11_13_Monatslohn_Gratifikation_wird_ausbezahlt_am'   => 'Dezember',
        '11_mit_%'                                            => '8.33',
        '11_13_Monatslohn_Gratifikation_nicht_vereinbart'     => '', //CHECKBOX
        '12_BVG-Beitrage_ja'                                  => '1', //CHECKBOX
        '12_BVG-Beitrage_nein'                                => '', //CHECKBOX
        '12_Name_BVG_Versicherers'                            => 'Basler Leben AG',
        '13_AHV-Ausgleichskasse'                              => 'SVA Zürich - LU5.413',
        '14_Kinder_Ausbildungszulagen_ja'                     => '1', //CHECKBOX
        '14_Anzahl_Kinderzulagen'                             => $employee->decki200,
        '14_Anzahl_Ausbildungszulagen'                        => $employee->decki250,
        '14_Kinder_Ausbildungszulagen_nein'                   => '', //CHECKBOX
        '14_Begrundung_Warum_nicht1'                          => '',
        '14_Begrundung_Warum_nicht2'                          => '',
        '14_Begrundung_Warum_nicht3'                          => '',
        //manual from here?
        '15_Weiterbeschaftigung_unbestimmte-Zeit_ja'          => $employee->insurance_15_1 == 1 ? '1' : '', //CHECKBOX
        '15_Weiterbeschaftigung_voraussichtlich_bis_ja'       => $employee->insurance_15_2 == 1 ? '1' : '', //CHECKBOX
        '15_voraussichtlich_bis'                              => $employee->insurance_15_3,
        '15_Weiterbeschaftigung_nein'                         => $employee->insurance_15_4 == 1 ? '1' : '', //CHECKBOX
        '15_wer_hat_gekundigt'                                => $employee->insurance_15_5,
        '15_wann'                                             => $employee->insurance_15_6,
        '15_auf_welchen_Zeitpunkt'                            => $employee->insurance_15_7,
        // '16_Grund_Vertragsauflösung1'                         => explode( "\n", wordwrap( $employee->insurance_16_1, 80))[0],
        '16_Grund_Vertragsauflosung1'                         => count(explode( "\n", $employee->insurance_16_1 )) > 1 ? explode( "\n", $employee->insurance_16_1 )[0] : $employee->insurance_16_1,
        '16_Grund_Vertragsauflosung2'                         => count(explode( "\n", $employee->insurance_16_1 )) > 1 ? explode( "\n", $employee->insurance_16_1 )[1] : '',
        '16_Grund_Vertragsauflosung3'                         => count(explode( "\n", $employee->insurance_16_1 )) > 2 ? explode( "\n", $employee->insurance_16_1 )[2] : '',
        '17_am_Betrieb_beteiligt_ja'                          => '',
        '17_am_Betrieb_beteiligt_nein'                        => '',
        '18_Bruttoeinkommen'                                  => '',
        '18_Material-Warenkosten'                             => '',
        '18_Zwischentotal'                                    => '',
        '18_Pauschalabzug_20_%'                               => '',
        '18_anrechenbarer_Zwischenverdienst'                  => '',
        // 'Ort_Datum'                                           => 'Glattbrugg ' . $date->format("d.m.Y"),
        'Ort_Datum'                                           => 'Glattbrugg ' . Carbon::now()->addMonth()->firstOfMonth()->format("d.m.Y"),
        'Tel_Nr'                                              => '043 557 33 12',
        'BUR-Nr'                                              => '',
        'Branchen_Code'                                       => '',
        'vollstandige_Adresse_Arbeitgeber'                    => "AAAB GmbH\nEuropastrasse 17\n8152 Glattbrugg",  //TEXTAREA
      );

      return $fields;
    }

    private function toDecimalNumber($input) {
      return number_format( $input, 2, '.', '' );
    }

    private function get_employee_plans($employee, $from, $to) {
      $plans = Plan::where('employee_id', $employee->id)
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'dita', [$from, $to] )
            // ->whereIn( 'symbol', ['K','U','F','FR','W'] );
            ->whereIn( 'symbol', ['F','W','S','A','K','KK','O','U','V','FR','SC','MSE','VSE','UN'] );
        })
        ->select('id', 'dita', 'symbol')
        ->orderBy('dita', 'ASC')
        ->get();

      return $plans;
    }

    private function get_month_employee_matrix($employee, $from, $to) {
      $employee_records = Record::where('employee_id', $employee->id)
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'time', [$from, $to] );
        })
        ->select('id', 'action', 'time')
        ->orderBy('time', 'ASC')
        ->get();

      $period = CarbonPeriod::create($from, '1 day', $to);
      return $this->month_employee_matrix($period, $employee_records);
    }
}
