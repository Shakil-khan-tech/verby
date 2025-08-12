"use strict";
// Class definition

var ReportDevice = function() {
    // Private functions
    var datatable;
    var total_report_time, date_report_from, date_report_to;
    var total_report_time_elem = $('#total_report_time');
    var date_report_from_elem = $('#date_report_time_from');
    var date_report_to_elem = $('#date_report_time_to');

    // record datatable
    var init_device_report = function() {

        $('body').tooltip({
        selector: '.tooltiped'
        });

        datatable = $('#device_report_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: device_report_json_url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                        },
                        map: function(raw) {
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            total_report_time = raw.meta.total_hours;
                            date_report_from = raw.meta.date_from;
                            date_report_to = raw.meta.date_to;
                            return dataSet;
                        },
                    },
                },
                pageSize: 5,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },

            // layout definition
            layout: {
                scroll: false,
                footer: false,
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
                input: $('#device_report_datatable_search_query'),
                key: 'generalSearch'
            },

            // columns definition
            columns: [{
                field: 'id',
                title: '#',
                sortable: 'asc',
                width: 40,
                type: 'number',
                selector: false,
                textAlign: 'center',
                visible: false,
            },
            {
                field: 'name',
                title: Lang.get('script.name'),
                textAlign: 'left',
                template: function(row) {
                    var function_title = Lang.get('script.not_set');
                    var function_color = 'danger';
                    if (typeof constants.performs[row.function] !== 'undefined') {
                      function_title = constants.performs[row.function];
                      function_color = constants.colors[row.function];
                    }

                    let records_sorted = row.records.sort(function(a,b){
                        return new Date(a.time) - new Date(b.time);
                    });
                    let time = records_sorted.length > 0 ? records_sorted[0].time : null;

                    var output = 
                    `<div class="d-flex align-items-center">
      					<div class="symbol symbol-40 symbol-${function_color} flex-shrink-0">
      						<div class="symbol-label">${row.id}</div>
      					</div>
      					<div class="ml-2">
                            <a href="/${Lang.locale}/records/calendar/${row.id}?time=${time}">
                                <div class="text-dark-75 font-weight-bold line-height-sm">${row.name} ${row.surname}</div>
                                <span class="font-size-sm text-dark-50 text-hover-primary">${function_title}</span>
                            </a>
      					</div>
      				</div>`;

                    return output;

                },
            },
            {
                field: 'work_time',
                title: Lang.get('script.working_hours'),
            },
            {
                field: 'work_time_decimal',
                title: Lang.get('script.hours_in_decimal'),
                template: function(row) {
                    return row.work_time_decimal.toFixed(2);
                }
            },
            {
                field: 'function',
                title: Lang.get('script.Function'),
                template: function(row) {
                  if (typeof constants.performs[row.function] === 'undefined') {
                    return '<span class="label font-weight-bold label-lg label-light-danger label-inline">'+ row.function +'</span>';
                  }
                  return '<span class="label font-weight-bold label-lg label-light-' + constants.colors[row.function] + ' label-inline">' + constants.performs[row.function] + '</span>';
                },
            },
            {
                field: 'Actions',
                title: Lang.get('script.Actions'),
                sortable: false,
                width: 125,
                autoHide: false,
                overflow: 'visible',
                template: function(row) {
                    let records_sorted = row.records.sort(function(a,b){
                        return new Date(a.time) - new Date(b.time);
                    });
                    let time = records_sorted.length > 0 ? records_sorted[0].time : null;

                    return `
                        <a href="/${Lang.locale}/records/calendar/${row.id}?time=${time}" class="btn btn-sm btn-clean btn-icon mr-2" title="${Lang.view_calendar}">
                            <span class="svg-icon svg-icon-md">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                  <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                  <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                </g>
                              </svg>
                            </span>
                        </a>
                    `;
                },
            }],

            //translation
            translate: TraceLocales.datatables(),

        });

        datatable.on('datatable-on-ajax-fail', function(event, response) {
            console.log('Error');
            var err = '';
            for (var err in response.responseJSON.errors) {
                if (response.responseJSON.errors.hasOwnProperty(err)) {
                err += response.responseJSON.errors[err] + '<br>';
                }
            }

            var content = {};
            content.title = String(response.responseJSON.message);
            content.message = err;

            var notify = $.notify(content, {
                type: 'danger',
                mouse_over:  true,
                z_index: 1051,
            });
        });

        datatable.on('datatable-on-ajax-done', function(a,b) {
            total_report_time_elem.html(total_report_time).removeClass('spinner');
            date_report_from_elem.html(date_report_from).removeClass('spinner');
            date_report_to_elem.html(date_report_to).removeClass('spinner');
        });

        var daterangepicker = $('#recordsDaterangepicker').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',

            // autoUpdateInput: false,

            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY h:mm A'
            },
            ranges: {
                [Lang.get('script.today')]: [moment(), moment()],
                [Lang.get('script.yesterday')]: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                [Lang.get('script.last7days')]: [moment().subtract(6, 'days'), moment()],
                [Lang.get('script.last30days')]: [moment().subtract(29, 'days'), moment()],
                [Lang.get('script.this_month')]: [moment().startOf('month'), moment().endOf('month')],
                [Lang.get('script.last_month')]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function(start, end, label) {
            $('#kt_daterangepicker_4 .form-control').val( start.format('MM/DD/YYYY h:mm A') + ' / ' + end.format('MM/DD/YYYY h:mm A'));
            datatable.search( {'start': start.format('YYYY-MM-DD HH:mm:00'), 'end': end.format('YYYY-MM-DD HH:mm:00')}, 'Date');
        });

        daterangepicker.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            datatable.search( {'start': null, 'end': null}, 'Date');
        });

        $('#clearRecordsDaterangepicker').click(function(){
            $('#recordsDaterangepicker').val('');
            datatable.search( {'start': null, 'end': null}, 'Date');
        });
    };

    return {
        // public functions
        init: function() {
            init_device_report();
        },
    };
}();

jQuery(document).ready(function() {
    ReportDevice.init();
});
