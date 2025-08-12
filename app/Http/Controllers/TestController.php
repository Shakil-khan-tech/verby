<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Record;
use App\Models\DeviceUser;
use App\Models\Calendar;
use App\Models\CalendarRoom;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Auth;
use App\Http\Traits\EmployeeTrait;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Storage;
use Codedge\Fpdf\Fpdf\Fpdf;
use FPDM;
use App;

// use Keensoen\CPanelApi\CPanel;
use WebReinvent\CPanel\CPanel;
use App\Http\Traits\EmailTrait;
use Illuminate\Support\Facades\Schema;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;


class TestController extends Controller
{
    use EmployeeTrait;
    use EmailTrait;

    public function totalhours(Employee $employee, $date)
    {
        $cDate = new Carbon($date);
        $firstDay = $cDate->firstOfMonth()->format("d.m.Y");
        $lastDay = $cDate->lastOfMonth()->format("d.m.Y");

        if ( isset( $employee->PartTime ) && $employee->PartTime == 1 ) {
          $oret = $this->calculate_hours($employee, $firstDay, $lastDay);
          return $oret;
        }

        return 'not PartTime';

    }

    public function test(Request $request)
    {

      if ( request()->has('delete') ) {
        
        $query = DB::table('records')
        ->select('time', 'employee_id', 'device_id', 'action', 'perform', 'identity', 'time', 'calendar_id', 'user_id', DB::raw('COUNT(*) AS count'))
        ->groupBy('time', 'employee_id')
        ->having('count', '>', 1)
        ->get();


        foreach ($query as $key => $record) {
          Record::where('time', $record->time)
          ->where('employee_id', $record->employee_id)
          ->delete();
        }

        foreach ($query as $key => $record) {
          Record::create([
            'employee_id' => $record->employee_id,
            'device_id' => $record->device_id,
            'action' => $record->action,
            'perform' => $record->perform,
            'identity' => $record->identity,
            'time' => $record->time,
            'calendar_id' => $record->calendar_id,
            'user_id' => $record->user_id,
          ]);
        }

        return 'deleted duplicates';
        
      } else {
        
        $types = ['api single', 'api bilk', 'systme'];

        $data = DB::table('records')
        ->select('*', DB::raw('COUNT(*) AS count'))
        ->whereNotNull('test_type')
        ->orderBy('created_at', 'DESC')
        ->groupBy('time', 'employee_id')
        ->having('count', '>', 1)
        // ->limit(1)
        ->get();

        $table = collect();
        foreach ($data as $key => $r) {
          $temp = Record::where('time', $r->time)->where('employee_id', $r->employee_id)->whereNotNull('test_type')->get();
          $table->push($temp);
        }

        return view('pages.test.test', compact('table', 'data', 'types'));

      }
      
    }

    public function test5(Request $request)
    {
      // $cl = CalendarRoom::all();
      $calendars = Calendar::with('rooms')->get();
      foreach ($calendars[0]->rooms as $key => $room) {
        echo $room;
      }
      return;

      return;
      $cal = Calendar::with('rooms')
      ->where('date', '>=', '2022-07-01')
      ->where('date', '<=', '2022-07-31')
      // ->where('rooms.status', 2)
      ->get();
      // return $cal;
      $i = 0;
      foreach ($cal as $key => $c) {
        foreach ($c->rooms as $key => $room) {
          if ( $room->pivot->status == 2 && $room->pivot->record_id != null ) {
            $i += 1;
          }
        }
        // $filtered_performs = $c->filter(function ($record) {
        //   return $c->rooms == $day;
        // });
        // echo $c->rooms;
      }
      return $i;


      $device = Device::find(1)->employees->take(10);
      $device = Device::find(1)->employees
      ->where('start', '<=', Carbon::now())
      ->where('end', '>=', Carbon::now())->take(10);
      return $device->pluck('fullname');
      
      foreach ($device->employees as $employee) {
        echo $employee->fullname;
      }
      return;

      return \App\Models\Employee::where('id', 3)
      ->with('devices')
      ->where('devices.id', 1)
      ->get()->pluck('devices');

      // return \App\Models\Employee::with(['devices' => function ($q) {
      //   $q->where('devices.id', 1);
      // }])->take(10)->get()->pluck('fullname');


      return \App\Models\Device::where('id', 1)
      ->with(['employees' => function ($q) {
        $q->take(10);
      }])
      ->get();

      // return auth()->user()->devices->pluck('id');
      $o = new \stdClass();
      $o->records = [];
      
      $employees = Employee::take(20)->get();
      $from = Carbon::now()->startOfMonth(); $to = $from->copy()->endOfMonth();
      $period = CarbonPeriod::create($from, '1 day', $to);

      foreach ($period as $tkey => $day) {
        // echo $day->format('d.m.Y') . '<br>';
        foreach ($employees as $key => $employee) {
          for ($ii=0; $ii < 4; $ii++) {
            $r = new \stdClass();
            $r->employee = $employee->id;
            $r->device = 1;
            $r->action = $ii;
            $r->perform = 1;
            $r->identity = 1;
            $r->time = $day->addHours(8)->addMinutes($key)->toDateTimeString();
            array_push($o->records, $r);
          }
          // array_push($o->records, $r);
          // $o->records = $r;
        }
      }
      return $o;

      
    }

    public function test2(Request $request)
    {
      echo Crypt::encrypt(Auth::user()->id) . '<br>';
      echo Crypt::decrypt( Crypt::encrypt(Auth::user()->id) ) . '<br>';
      return URL::signedRoute('external.records_report', ['employee' => 1]);
      return URL::signedRoute('users.show', ['user' => Auth::user()->id]);
      // $devices = Device::available()->get();
      // $current_user = Auth::user();
      //   $devices = DB::table('device_user')
      //   ->select('device_id')
      //   ->where('user_id', $current_user->id)
      //   ->get()
      //   ->pluck('device_id');
        // $devices = Device::view()->get();
        return $devices;
    }

    public function test3(Request $request)
    {
        // $cpanel = new CPanel();
        // $response = $cpanel->getEmailAccounts('regex=@batlab.mk');
        // $cpanel = new CPanel();
        // $response = $cpanel->listDatabases();

        // try {

        //   $cpanel = new CPanel();
        //   $Module = 'Email';
        //   $function = 'add_pop';
        //   $parameters_array = [
        //     'email'           => 'test2',
        //     'password'        => '12345luggage',
        //     'quota'           => '0',
        //     'domain'          => 'batlab.mk',
        //     'skip_update_db'  => '1',
        //   ];
        //   $response = $cpanel->callUAPI($Module, $function, $parameters_array);
        //   return $response['status'];

        // } catch (Exception $e) {
        //         return 'Exception: ' .$e->getMessage();
        // }

        $uapi = $this->create_email('test@batlab.mk', '12345luggage', 0);
        if ( $uapi === true ) {
          return 'OK';
        } else {
          // return $uapi;
          foreach ($uapi as $errors) {
            foreach ($errors as $error) {
              echo $error .  "<br>";
            }
          }
        }


    }

    public function test4(Request $request)
    {
        // echo date('m/d/Y h:i:s a', time()) . '---';
        // echo Carbon::now() . '---';
        // echo DB::select( 'select NOW() as the_time' )[0]->the_time . '---';
        // return Record::where('id', 13385)->select('time')->first();
      $fields = array(
        'Eingangsdatum'                                       => 'xxx', //Date of receipt
        'Name_und_Vorname'                                    => 'xxx', //Fullname
        'Pers-Nr'                                             => 'xxx', //
        'AHV-Nr'                                              => 'xxx', //
        'PLZ_Wohnort_Strasse'                                 => 'xxx', //Address
        'Geburtsdatum'                                        => 'xxx', //Birthday
        'Zivilstand'                                          => 'xxx', // Civil Status
        'Monat'                                               => 'xxx', // Month
        'Jahr'                                                => 'xxx', // Year
        'ausgeübte_Tätigkeit'                                 => 'xxx', //Activity performed
        '1_1'                                                 => '8',
        '1_2'                                                 => '8',
        '1_3'                                                 => '8',
        '1_4'                                                 => '8',
        '1_5'                                                 => '8',
        '1_6'                                                 => '8',
        '1_7'                                                 => '8',
        '1_8'                                                 => '8',
        '1_9'                                                 => '8',
        '1_10'                                                => '8',
        '1_11'                                                => '8',
        '1_12'                                                => '8',
        '1_13'                                                => '8',
        '1_14'                                                => '8',
        '1_15'                                                => '8',
        '1_16'                                                => '8',
        '1_17'                                                => '8',
        '1_18'                                                => '8',
        '1_19'                                                => '8',
        '1_20'                                                => '8',
        '1_21'                                                => '8',
        '1_22'                                                => '8',
        '1_23'                                                => '8',
        '1_24'                                                => '8',
        '1_25'                                                => '8',
        '1_26'                                                => '8',
        '1_27'                                                => '8',
        '1_28'                                                => '8',
        '1_29'                                                => '8',
        '1_30'                                                => '8',
        '1_31'                                                => '8',
        '2_schriftlicher_Arbeitsvertrag_ja'                   => '1',  //CHECKBOX
        '2_schriftlicher_Arbeitsvertrag_nein'                 => '',  //CHECKBOX
        // Have weekly working hours been agreed with the insured person?
        '3_wöchentliche_Arbeitszeit_vereinbart_ja'            => '1',  //CHECKBOX
        '3_wöchentliche_Arbeitszeit_vereinbart_nein'          => '1',  //CHECKBOX
        '3_wöchentliche_Arbeitszeit_Std'                      => 'num', //Hours per week
        '4_wöchentliche_Arbeitszeit_Betrieb_Std'              => 'num', //Weekly normal working hours in the company
        //Is the company subject to a collective labor agreement?
        '5_GAV'                                               => 'xxx',
        '5_Gesamtarbeitsvertrag_ja'                           => '',
        '5_Gesamtarbeitsvertrag_nein'                         => '1',
        //Were the insured person offered more working hours in the certified month?
        '6_Mehr-Std_pro_Tag'                                  => 'xxx', //Hours per day
        '6_Mehr-Std_pro_Woche'                                => 'xxx', //Hours per week
        '6_Mehr-Std_pro_Monat'                                => 'xxx', //Hours per month
        '6_mehr_Arbeitsstunden_angeboten_nein'                => 'xxx',
        '6_mehr_Arbeitsstunden_angeboten_ja'                  => 'xxx',
        '8_AHV-pflichtiger_Bruttlohn_Std'                     => 'xxx',
        '8_AHV-pflichtiger_Bruttlohn_Monat'                   => 'xxx',
        '9_AHV-pflichtiger_Bruttolohn_Std'                    => 'xxx',
        '9_AHV-pflichtiger_Bruttolohn_gleich_CHF'             => 'xxx',
        '10_andere_Lohnbestandteile_welche'                   => 'xxx',
        '10_13_Monatslohn_Gratifikation_%'                    => 'xxx',
        '10_andere_Lohnbestandteile_%'                        => 'xxx',
        '10_Grundlohn_CHF'                                    => 'xxx',
        '10_13_Monatslohn_Gratifikation_CHF'                  => 'xxx',
        '10_andere_Lohnbestandteile_CHF'                      => 'xxx',
        '11_13_Monatslohn_Gratifikation_wird_ausbezahlt_am'   => 'xxx',
        '11_mit_%'                                            => 'xxx',
        '12_Name_BVG_Versicherers'                            => 'xxx',
        '13_AHV-Ausgleichskasse'                              => 'xxx',
        '14_Anzahl_Kinderzulagen'                             => 'xxx',
        '14_Anzahl_Ausbildungszulagen'                        => 'xxx',
        '15_voraussichtlich_bis'                              => 'xxx',
        '15_wann'                                             => 'xxx',
        '15_auf_welchen_Zeitpunkt'                            => 'xxx',
        '18_Bruttoeinkommen'                                  => 'xxx',
        '18_Material-Warenkosten'                             => 'xxx',
        '18_Zwischentotal'                                    => 'xxx',
        '18_Pauschalabzug_20_%'                               => 'xxx',
        '18_anrechenbarer_Zwischenverdienst'                  => 'xxx',
        'Ort_Datum'                                           => 'xxx',
        'Tel_Nr'                                              => 'xxx',
        'BUR-Nr'                                              => 'xxx',
        'Branchen_Code'                                       => 'xxx',
        '10_13_monatslohn_Gratifikation'                      => 'xxx',
        '10_andere_Lohnbestandteile'                          => 'xxx',
        '10_Grundlohn'                                        => '', //CHECKBOX
        '11_13_Monatslohn_Gratifikation_nicht_vereinbart'     => 'xxx',
        '11_13_Monatslohn_Gratifikation_wird_noch_ausbezahlt' => 'xxx',
        '14_Kinder_Ausbildungszulagen_ja'                     => 'xxx',
        '14_Kinder_Ausbildungszulagen_nein'                   => 'xxx',
        '17_am_Betrieb_beteiligt_ja'                          => 'xxx',
        '17_am_Betrieb_beteiligt_nein'                        => '',
        'vollständige_Adresse_Arbeitgeber'                    => 'Textarea',  //TEXTAREA
      );

      $pdf = new FPDM( Storage::path( "pdfs/arbeitslosenversicherung.pdf" ) );
      $pdf->useCheckboxParser = true;
      $pdf->Load($fields, false); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
      $pdf->Merge();
      $pdf->Output();
    }
}
