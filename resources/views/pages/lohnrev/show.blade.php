{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

  <!--begin::Profile Overview-->
  <div class="d-flex flex-row">
    {{-- @include('pages.widgets._widget-lohn_aside', ['item_active' => '']) --}}
    <!--begin::Content-->
    <div class="flex-row-fluid ml-lg-8">
      <!--begin::Card-->
      <div class="card card-custom overflow-hidden">
        <div class="card-body p-0">
          <!-- begin: Invoice-->
          <!-- begin: Invoice header-->
          <div class="row justify-content-center pt-8 px-8 px-md-0">
            <div class="col-md-9">
              <div class="d-table w-100 pb-10 pb-md-20">
                <div class="d-table-cell text-left px-0">
                  <!--begin::Logo-->
                  <a href="#" class="mb-5">
                    <img src=" {{ asset('media/logos/aaab.png') }}" width="40px" alt="" />
                  </a>
                  <!--end::Logo-->
                  <span class="d-flex flex-column opacity-70">
                    <span class="font-weight-bolder mb-2">AAAB GmbH</span>
                    <span>Europastrasse 17,</span>
                    <span>8152 Glattbrugg</span>
                    <span>Tel 043 557 33 12</span>
                  </span>
                </div>
                <div class="d-table-cell text-right px-0">
                  <span class="d-flex flex-column align-items-md-end opacity-70">
                    <span class="font-weight-bolder mb-2">{{ $employee->fullname }}</span>
                    <span>{{ $employee->strasse }}, {{ $employee->PLZ }}</span>
                    <span>{{ $employee->ORT1 }}, {{ $employee->ORT }}</span>
                    <span>Tel: {{ $employee->phone }}</span>
                  </span>
                </div>
              </div>
              <h1 class="display-4 font-weight-boldest mb-10">{{ __('Payroll') }}
              <span class="font-weight-bolder small">{{ $dateYM }}</span></h1>
              <div class="border-bottom w-100"></div>
              <div class="d-flex justify-content-between pt-6">
                <div class="d-flex flex-column flex-root">
                  <span class="font-weight-bolder mb-2">{{ __('Exit') }}</span>
                  <span class="opacity-70">{{ date("d.m.Y") }}</span>
                </div>
                <div class="d-flex flex-column flex-root">
                  <span class="font-weight-bolder mb-2">{{ __('Period') }}</span>
                  <span class="opacity-70">{{ $firstDay }} - {{ $lastDay }}</span>
                </div>
                <div class="d-flex flex-column flex-root">
                  <span class="font-weight-bolder mb-2">{{ __('Employee no.') }}</span>
                  <span class="opacity-70">{{ $employee->id }}</span>
                </div>
              </div>
            </div>
          </div>
          <!-- end: Invoice header-->
          <!-- begin: Invoice body-->
          <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-9">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="pl-0 font-weight-bold text-muted text-uppercase">{{ __('Nr') }}</th>
                      <th class="pl-0 font-weight-bold text-muted text-uppercase">{{ __('Designation wage type') }}</th>
                      <th class="text-right font-weight-bold text-muted text-uppercase">%</th>
                      <th class="text-right font-weight-bold text-muted text-uppercase">{{ __('Approach') }}</th>
                      <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">{{ __('Base') }}</th>
                      <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">{{ __('Amount<br>month') }}</th>
                      <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">{{ __('Total') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2">100</td>
                      <td class="pl-0 py-2" colspan="6">{{ __('Gross wage') }}</td>
                    </tr>
                    {{-- if/else here --}}
                    @if ( $employee->PartTime == 1 )
                      <tr class="">
                        <td class="pl-0 py-2">101</td>
                        <td class="pl-0 py-2">{{ __('Hourly wage') }}</td>
                        <td class="text-right py-2"> {{ number_format($oret, 2, '.', '') }} ha</td>
                        <td class="text-right py-2">Fr. {{ $employee->EhChf }}</td>
                        <td class="text-right py-2">Fr.</td>
                        <td class="text-right py-2">{{ $baza }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                      <tr class="">
                        <td class="pl-0 py-2">102</td>
                        <td class="pl-0 py-2">{{ __('Holiday compensation basis') }} <br> {{ __('4 weeks/year') }}</td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2">{{ $employee->Perqind1 }} %</td>
                        <td class="text-right py-2">Fr.</td>
                        <td class="text-right py-2">{{ $p1 }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                      <tr class="">
                        <td class="pl-0 py-2">103</td>
                        <td class="pl-0 py-2">{{ __('Holiday Compensation') }}</td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2">{{ $employee->Perqind2 }} %</td>
                        <td class="text-right py-2">Fr.</td>
                        <td class="text-right py-2">{{ $p2 }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                    @else
                      <tr class="">
                        <td class="pl-0 py-2">104</td>
                        <td class="pl-0 py-2">{{ __('Monthly wage') }}</td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2">{{ $baza }}</td>
                        <td class="text-right py-2">Fr.</td>
                        <td class="text-right py-2">{{ $baza }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                    @endif
                    <tr class="">
                      <td class="pl-0 py-2">105</td>
                      <td class="pl-0 py-2">{{ __('13th monthly salary') }}</td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2">{{ $employee->Perqind3 }}%</td>
                      <td class="text-right py-2">Fr.</td>
                      <td class="text-right py-2">{{ $p3 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2">106</td>
                      <td class="pl-0 py-2">{{ __('Total') }}</td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2">Fr.</td>
                      <td class="text-right py-2">{{ $total_1 }}</td>
                      <td class="text-danger pr-0 py-2 text-right">Fr. {{ $total_1 }}</td>
                    </tr>
                    @isset( $revision )
                      @if( $revision->B_KTG_1 != 0 && $revision->B_KTG_2 != 0)
                        <tr class="">
                          <td class="pl-0 py-2">107</td>
                          <td class="pl-0 py-2">{{ __('KTG') }}</td>
                          <td class="text-right py-2">{{ $revision->B_KTG_1 }}x</td>
                          <td class="text-right py-2">Fr. {{ $revision->B_KTG_2 }}%</td>
                          <td class="text-right py-2">Fr.</td>
                          <td class="text-right py-2">{{ $KTG ?? '' }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                      @if( $revision->B_unfall_1 != 0 && $revision->B_unfall_2 != 0)
                        <tr class="">
                          <td class="pl-0 py-2">108</td>
                          <td class="pl-0 py-2">{{ __('UVG') }}</td>
                          <td class="text-right py-2">{{ $revision->B_unfall_1 }}x</td>
                          <td class="text-right py-2">Fr. {{ $revision->B_unfall_2 }}%</td>
                          <td class="text-right py-2">Fr.</td>
                          <td class="text-right py-2">{{ $Unfall ?? '' }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                    @endisset

                    @if ($employee->decki250 > 0)
                      <tr class="">
                        <td class="pl-0 py-2">109</td>
                        <td class="pl-0 py-2">{{ __('Education allowance 250') }}</td>
                        <td class="text-right py-2">{{ $employee->decki250 }}x</td>
                        <td class="text-right py-2">Fr. 250</td>
                        <td class="text-right py-2">Fr.</td>
                        <td class="text-right py-2">{{ $dck250 }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                    @endif
                    @if ($employee->decki200 > 0)
                      <tr class="">
                        <td class="pl-0 py-2">109</td>
                        <td class="pl-0 py-2">{{ __('Child allowance 200') }}</td>
                        <td class="text-right py-2">{{ $employee->decki200 }}x</td>
                        <td class="text-right py-2">Fr. 200</td>
                        <td class="text-right py-2">Fr.</td>
                        <td class="text-right py-2">{{ $dck200 }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                    @endif

                    @isset($revision)
                      @if ($revision->B_bonnus1_2 != 0)
                        <tr class="">
                          <td class="pl-0 py-2">110</td>
                          <td class="pl-0 py-2">{{ $revision->B_bonnus1_1 }}</td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2">Fr.</td>
                          <td class="text-right py-2">{{ $b_bonnus1 }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                      @if ($revision->B_bonnus2_2 != 0)
                        <tr class="">
                          <td class="pl-0 py-2">110</td>
                          <td class="pl-0 py-2">{{ $revision->B_bonnus2_1 }}</td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2">Fr.</td>
                          <td class="text-right py-2">{{ $b_bonnus2 }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                    @endisset

                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2" colspan="7"></td>
                    </tr>
                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2">200</td>
                      <td class="pl-0 py-2" colspan="6">{{ __('Deductions') }}</td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">201</td>
                      <td class="pl-0 py-2">{{ __('AHV/IV/EO-Contribution') }}</td>
                      <td class="text-right py-2">{{ $AHV }} %</td>
                      <td class="text-right py-2">von</td>
                      <td class="text-right py-2">Fr. {{ $total_1 }}</td>
                      <td class="text-right py-2">{{ $A1 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">202</td>
                      <td class="pl-0 py-2">{{ __('ALV contribution') }}</td>
                      <td class="text-right py-2">{{ $ALV }} %</td>
                      <td class="text-right py-2">von</td>
                      <td class="text-right py-2">Fr. {{ $total_1 }}</td>
                      <td class="text-right py-2">{{ $A2 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">203</td>
                      <td class="pl-0 py-2">{{ __('BVG contribution') }}</td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2">{{ $A3 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">204</td>
                      <td class="pl-0 py-2">{{ __('NBUV contribution') }}  {{ __('100% share of') }}</td>
                      <td class="text-right py-2">{{ $NBUV }} %</td>
                      <td class="text-right py-2">{{ __('out of') }}</td>
                      <td class="text-right py-2">Fr. {{ $total_1 }}</td>
                      <td class="text-right py-2">{{ $A4 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">205</td>
                      <td class="pl-0 py-2">{{ __('KTG') }}  {{ __('50% share of') }}</td>
                      <td class="text-right py-2">2.169 %</td>
                      <td class="text-right py-2">{{ __('out of') }}</td>
                      <td class="text-right py-2">Fr. {{ $total_1 }}</td>
                      <td class="text-right py-2">{{ $A5 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">206</td>
                      <td class="pl-0 py-2">{{ __('Enforcement costs PK') }}</td>
                      <td class="text-right py-2">0.4 %</td>
                      <td class="text-right py-2">von</td>
                      <td class="text-right py-2">{{ __('out of') }}</td>
                      <td class="text-right py-2">{{ $A7 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    @isset( $revision )
                      @if ( $revision->A_Verplegung_1 != 0 && $revision->A_Verplegung_2 != 0 )
                        <tr class="">
                          <td class="pl-0 py-2">207</td>
                          <td class="pl-0 py-2">{{ __('Meals') }} <span class="float-right">{{ $revision->A_Verplegung_1 }} {{ __('Days') }}</span></td>
                          <td class="text-right py-2">Fr. {{ $revision->A_Verplegung_2 }}</td>
                          <td class="text-right py-2">{{ __('out of') }}</td>
                          <td class="text-right py-2">Fr. {{ $A6 }}</td>
                          <td class="text-right py-2">{{ $A6 }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                      @if ( $revision->A_bonnus1_2 != 0 )
                        <tr class="">
                          <td class="pl-0 py-2">208</td>
                          <td class="pl-0 py-2">{{ $revision->A_bonnus1_1 }}</td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2">Fr.</td>
                          <td class="text-right py-2">{{ $a_bonnus1 }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                      @if ( $revision->A_bonnus2_2 != 0 )
                        <tr class="">
                          <td class="pl-0 py-2">209</td>
                          <td class="pl-0 py-2">{{ $revision->A_bonnus2_1 }}</td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2">Fr.</td>
                          <td class="text-right py-2">{{ $a_bonnus2 }}</td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                    @endisset
                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2">210</td>
                      <td class="pl-0 py-2">{{ __('Total') }}</td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2">Fr.</td>
                      <td class="text-right py-2">{{ $a_total }}</td>
                      <td class="text-danger pr-0 py-2 text-right">Fr. {{ $a_total }}</td>
                    </tr>

                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2" colspan="7"></td>
                    </tr>
                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2">300</td>
                      <td class="pl-0 py-2" colspan="5">{{ __('Net wage') }}</td>
                      <td class="text-danger pr-0 py-2 text-right">Fr. {{ $nettoLohn1 }}</td>
                    </tr>
                    @if ( $employee->PartTime == 1 )
                      <tr class="">
                        <td class="pl-0 py-2">301</td>
                        <td class="pl-0 py-2">{{ __('Provision holiday compensation') }}</td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2">Fr. {{ $p1 }}</td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                    @endif
                    <tr class="">
                      <td class="pl-0 py-2">302</td>
                      <td class="pl-0 py-2">{{ __('Provision for 13th month salary') }}</td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2"></td>
                      <td class="text-right py-2">Fr. {{ $p3 }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                    <tr class="">
                      <td class="pl-0 py-2">303</td>
                      <td class="pl-0 py-2">{{ __('Withholding Tax') }}</td>
                      <td class="text-right py-2">{{ __('Tariff') }}: {{ $employee->ORT }}</td>
                      <td class="text-right py-2">{{ $employee->TAX }}</td>
                      <td class="text-right py-2">{{ $tatimiSelekt }} %</td>
                      <td class="text-right py-2">Fr. {{ $Quellensteuer }}</td>
                      <td class="text-danger pr-0 py-2 text-right"></td>
                    </tr>
                  </tbody>

                </table>
              </div>
            </div>
          </div>
          <!-- end: Invoice body-->
          <!-- begin: Invoice footer-->
          <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-9">
              <div class="table-responsive">
                <table class="table">
                  {{-- <thead>
                    <tr>
                      <th class="font-weight-bold text-muted text-uppercase">BANK</th>
                      <th class="font-weight-bold text-muted text-uppercase">ACC.NO.</th>
                      <th class="font-weight-bold text-muted text-uppercase">DUE DATE</th>
                      <th class="font-weight-bold text-muted text-uppercase">TOTAL AMOUNT</th>
                    </tr>
                  </thead> --}}

                  <tbody>
                    <tr class="font-weight-boldest">
                      <td class="pl-0 py-2">400</td>
                      <td class="pl-0 py-2" colspan="5">{{ __('Net wage') }}</td>
                      <td class="text-danger font-size-h3 font-weight-boldest text-right pr-0">Fr. {{ $NettoLohn2 }}</td>
                    </tr>
                    @if ( $employee->PartTime == 1 )
                      @if ( isset($revision) && $revision->KONTO_Ferie_PAY > 0 )
                        <tr class="">
                          <td class="pl-0 py-2">401</td>
                          <td class="pl-0 py-2">{{ __('Holiday compensation balance') }}</td>
                          <td class="text-right py-2">Fr. 0.00</td>
                          <td class="text-right py-2">{{ __('Payment of holiday compensation') }}</td>
                          <td class="text-right py-2">{{ $KONTO_ferie ?? 0 }}</td>
                          <td class="text-right py-2"></td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @else
                        <tr class="">
                          <td class="pl-0 py-2">401</td>
                          <td class="pl-0 py-2">{{ __('Holiday compensation balance') }}</td>
                          <td class="text-right py-2">Fr. {{ $KONTO_ferie ?? 0 }}</td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2"></td>
                          <td class="text-right py-2"></td>
                          <td class="text-danger pr-0 py-2 text-right"></td>
                        </tr>
                      @endif
                    @endif

                    @if ( isset($revision) && floatval($revision->KONTO_13monats_PAY) > 0 )
                      <tr class="">
                        <td class="pl-0 py-2">402</td>
                        <td class="pl-0 py-2">{{ __('Balance of 13th monthly salary') }}</td>
                        <td class="text-right py-2">Fr. 0.00</td>
                        <td class="text-right py-2" colspan="3">{{ __('Payout 13th month salary') }}</td>
                        <td class="text-danger pr-0 py-2 text-right">{{ $KONTO_13monats ?? 0 }}</td>
                      </tr>
                    @else
                      <tr class="">
                        <td class="pl-0 py-2">402</td>
                        <td class="pl-0 py-2">{{ __('Balance of 13th monthly salary') }}</td>
                        <td class="text-right py-2">Fr. {{ $KONTO_13monats ?? 0 }}</td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2"></td>
                        <td class="text-right py-2"></td>
                        <td class="text-danger pr-0 py-2 text-right"></td>
                      </tr>
                    @endif

                    @isset( $revision )
                      @if ( $revision->KONTO_Ferie_PAY > 0 || $revision->KONTO_13monats_PAY > 0)
                        <tr class="font-weight-boldest">
                          <td class="pl-0 py-2">403</td>
                          <td class="pl-0 py-2" colspan="5">{{ __('TOTAL') }}</td>
                          <td class="text-danger font-size-h3 font-weight-boldest text-right pr-0">
                            Fr. {{ $NettoLohn2 + floatval($revision->KONTO_Ferie_PAY) + floatval($revision->KONTO_13monats_PAY) }}
                          </td>
                        </tr>
                      @endif
                    @endisset
                  </tbody>
                </table>
              </div>

              <div class="d-flex  justify-content-between pt-12">
                <span class="font-weight-boldest text-center">{{ __('Payout:am') }}<br> {{ $timestamp }}</span>
                <span class="font-weight-boldest text-center">{{ __('C/Account') }}
                  {{ $employee->bankname }} {{ $employee->IBAN }} <br> {{ $employee->surname }} {{ $employee->name }}
                </span>
              </div>
            </div>
          </div>
          <!-- end: Invoice footer-->
          <!-- begin: Invoice action-->
          <div class="row invoice-footer justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-9">
              <div class="">
                <button type="button" class="btn btn-primary font-weight-bold float-right" onclick="window.print();">{{ __('Print Payroll') }}</button>
              </div>
            </div>
          </div>
          <!-- end: Invoice action-->
          <!-- end: Invoice-->
        </div>
      </div>
    </div>
    <!--end::Content-->
  </div>
  <!--end::Profile Overview-->

@endsection

{{-- Styles Section --}}
@section('styles')
    {{--  --}}
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    {{-- page scripts --}}
    <script src="{{ mix('js/pages/_misc/aside.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/aside_vacation.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/pages/_misc/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>

    {{-- page script --}}


@endsection
