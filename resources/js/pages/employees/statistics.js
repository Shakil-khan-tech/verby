"use strict";
// Class definition

var EmployeeStatistics = function() {
    // Private functions
    var table;
    var datatable;
    var currentMonth = moment().format('YYYY-MM');
    // var employee_datatable = $('#employee_datatable');

    var statDefinedMonths = $('.statDefinedMonths');
    var date_picker_start = $('#date_picker_start');
    var date_picker_end = $('#date_picker_end');
    var filter_start = moment().format('YYYY-MM');
    var filter_end = moment().format('YYYY-MM');
    var _months = 1;

    // Public functions
    var _initDatatable = function() {
        datatable = $(table).KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: statistics_json_url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                        },
                        params: {
                            query: {
                                active_status: $('#employee_datatable_search_active').val(),
                            },
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr);
                            console.log(ajaxOptions);
                            console.log(thrownError);

                            var content = {};
							content.title = thrownError;
							content.message = Lang.get('script.narrow_down_your_search');
							var notify = $.notify(content, {
								type: 'danger',
								mouse_over:  true,
								z_index: 1051,
							});
                        },
                    },
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },

            ext: {
                errMode: 'throw',
            },

            processing: true,

            // layout definition
            layout: {
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false // display/hide footer
            },

            // column sorting
            sortable: true,

            pagination: true,

            extensions: {
                // boolean or object (extension options)
                checkbox: true,
            },

            search: {
                input: $('#employee_datatable_search_query'),
                key: 'generalSearch'
            },

            // columns definition
            columns: [{
                field: 'id',
                title: '#',
                sortable: false,
                width: 20,
                type: 'number',
                selector: true,
                textAlign: 'center',
                autoHide: false,
            },{
                field: 'name',
                title: Lang.get('script.name'),
                autoHide: false,
                template: function(row) {
                    var states = ['success', 'info', 'primary', 'warning', 'danger'];
                    var state = (typeof states[row.function] === 'undefined') ? states[4] : states[row.function];
                    var e_function = row.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

                    var output = `<div class="d-flex align-items-center">
      								<div class="symbol symbol-40 symbol-${state} flex-shrink-0">
      									<div class="symbol-label">${row.id}</div>
      								</div>
      								<div class="ml-2">
                                        <a href="/${Lang.locale}/records/calendar/${row.id}/print?date=${currentMonth}">
        									<div class="text-dark-75 font-weight-bold line-height-sm">${row.name} ${row.surname}</div>
        									<span class="font-size-sm text-dark-50 text-hover-primary">${e_function}</span>
                                        </a>
      								</div>
      							</div>`;

                    return output;

                },
            }, {
                field: 'ORT',
                title: Lang.get('script.Canton'),
            }, {
                field: 'active_status',
                title: Lang.get('script.Active'),
                template: function(row) {
                    let active = row.active_status == 1 ? Lang.get('script.validations.yes') : Lang.get('script.validations.no');
                    let status = row.active_status == 1 ? ' label-light-success' : ' label-light-danger';
                    return `<span class="label font-weight-bold label-lg${status} label-inline">${active}</span>`;
                },
            }, {
                field: 'seconds',
                title: Lang.get('script.working_hours'),
                template: function(row) {
                    let hours = (row.seconds / 3600).toFixed(2)
                    return `<span class="label font-weight-bold label-lg label-inline">${hours}</span>`;
                },
            }, {
                field: 'percentage',
                title: `${Lang.get('script.percentage')}(182h)`,
                template: function(row) {

                    let seconds = row.seconds;
                    let desired = 182 * 3600;
                    let percentage = (seconds / desired) * 100;
                    percentage = percentage / _months;
                    return `<span class="label font-weight-bold label-lg label-inline">${percentage.toFixed(2)}%</span>`;
                },
            }, {
                field: 'work_percetage',
                title: `${Lang.get('script.assigned_work_percetage')}`,
                template: function(row) {
                    if (row.work_percetage) {
                        return `<span class="label font-weight-bold label-lg label-inline">${row.work_percetage}%</span>`;
                    } else {
                        return `<span class="label font-weight-bold label-lg label-inline">${Lang.get('script.not_set')}</span>`;
                    }
                },
            }, {
                field: 'function',
                title: Lang.get('script.Function'),
                template: function(row) {
                    var status = {
                        0: {
                            'title': Lang.get('script.functions.shift_manager'),
                            'class': ' label-light-success'
                        },
                        1: {
                            'title': Lang.get('script.functions.Cleaners'),
                            'class': ' label-light-info'
                        },
                        2: {
                            'title': Lang.get('script.functions.Maintenance'),
                            'class': ' label-light-primary'
                        },
                        3: {
                            'title': Lang.get('script.functions.Stewarding'),
                            'class': ' label-light-warning'
                        },
                        4: {
                            'title': Lang.get('script.not_set'),
                            'class': ' label-light-danger'
                        },
                    };
                    if (typeof status[row.function] === 'undefined') {
                      return '<span class="label font-weight-bold label-lg' + status[4].class + ' label-inline">' + status[4].title + '</span>';
        						}
                    return '<span class="label font-weight-bold label-lg' + status[row.function].class + ' label-inline">' + status[row.function].title + '</span>';
                },
            }, {
                field: 'Actions',
                title: Lang.get('script.Actions'),
                sortable: false,
                width: 125,
                autoHide: false,
                overflow: 'visible',
                template: function(row) {
                    return `
                        <a href="javascript:;" class="btnSendEmailSingle btn btn-sm btn-clean btn-icon" data-month="${currentMonth}" data-record_id="${row.id}" title="${Lang.get('script.send_email')}">
                            <span class="svg-icon svg-icon-md">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor"/>
                                    <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                    `;
                },
            }],

            //translation
            translate: TraceLocales.datatables(),
        });

        $('#employee_datatable_search_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'function');
        });
        $('#employee_datatable_search_active').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'active_status');
        });

        // $('#date_picker').on('change', function() {
        //     currentMonth = $(this).val();
        //     datatable.search($(this).val().toLowerCase(), 'month');
        // });

        $('#employee_datatable_search_status, #employee_datatable_search_active').selectpicker();

        datatable.on('datatable-on-click-checkbox', function(e) {
            return;
            var ids = datatable.checkbox().getSelectedId();
            var count = ids.length;
            console.log(ids);

            $('#datatable_selected_records').html(count);

            if (count > 0) {
                $('#datatable_group_action_form').collapse('show');
            } else {
                $('#datatable_group_action_form').collapse('hide');
            }
        });

        // datatable on ajax error
        datatable.on('datatable-on-ajax-error', function(e, xhr, textStatus, message) {
            KTApp.unblock(table);
            console.log(xhr);
            console.log(textStatus);
            console.log(message);
        });
    };

    // var initDateRangePicker = function() {
    //     date_picker_start.datetimepicker({
    //         format: 'YYYY-MM',
    //         locale: Lang.locale,
    //         defaultDate: moment().startOf('month'),
    //         viewMode: 'months',
    //         maxDate: moment().endOf('month'),
    //         minDate: moment().subtract(1, 'year').startOf('month'),
    //     });

    //     date_picker_end.datetimepicker({
    //         format: 'YYYY-MM',
    //         locale: Lang.locale,
    //         defaultDate: moment().endOf('month'),
    //         viewMode: 'months',
    //         maxDate: moment().endOf('month'),
    //     });

    //     date_picker_start.on('change.datetimepicker', function (e) {
    //         filter_start = e.date.format('YYYY-MM');
    //         date_picker_end.datetimepicker('minDate', e.date);
    //         // date_picker_end.datetimepicker('date', e.date);
    //         console.log(filter_start);
    //         // datatable.search( {'start': filter_start, 'end': filter_end}, 'Date');
    //     });

    //     date_picker_start.on('hide.datetimepicker', function (e) {
    //         statDefinedMonths.removeClass('bg-gray-200 active');
    //         $('[data-months="custom"]').addClass('bg-gray-200 active');
    //     });

    //     date_picker_end.on('change.datetimepicker', function (e) {
    //         filter_end = e.date.format('YYYY-MM');
    //         date_picker_start.datetimepicker('maxDate', e.date);
    //         console.log(filter_end);
    //         // datatable.search( {'start': filter_start, 'end': filter_end}, 'Date');
    //     });

    //     date_picker_end.on('hide.datetimepicker', function (e) {
    //         statDefinedMonths.removeClass('bg-gray-200 active');
    //         $('[data-months="custom"]').addClass('bg-gray-200 active');
    //     });
    // };

    var _definedMonths = function() {
        statDefinedMonths.on('click', function(e){
            e.preventDefault();
            statDefinedMonths.removeClass('bg-gray-200 active');
            // $('[data-months="custom"]').removeClass('bg-gray-200 active');
            $(this).addClass('bg-gray-200 active');

            let months = $(this).data('months') - 1;
            _months = months + 1;
            filter_start = moment().subtract(months, 'months').format('YYYY-MM');
            filter_end = moment().format('YYYY-MM');

            // date_picker_end.datetimepicker('date', filter_end);
            // date_picker_start.datetimepicker('date', filter_start);

            console.log(filter_start);
            console.log(filter_end);

            $('#dropdown-statistics-toggle').html(`${filter_start} ${Lang.get('script.to')} ${filter_end}`);
            datatable.search( {'start': filter_start, 'end': filter_end}, 'Date');
        });
    };

    // var applyDateRange = function() {
    //     $('[data-dropdown="apply"]').on('click', function(e){
    //         $('#dropdown-statistics-toggle').trigger('click.bs.dropdown');
    //         datatable.search( {'start': filter_start, 'end': filter_end}, 'Date');
    //         $('#dropdown-statistics-toggle').html(`${filter_start} ${Lang.get('script.to')} ${filter_end}`);
    //     });

    //     $('[data-dropdown="cancel"]').on('click', function(e){
    //         $('#dropdown-statistics-toggle').trigger('click.bs.dropdown');
    //     });
    // };

    var exportButtons = () => {
        return;
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: function () { return documentTitle(); },
                    footer: true,
                },
                {
                    extend: 'csvHtml5',
                    title: function () { return documentTitle(); },
                    footer: true,
                },
                {
                    extend: 'pdfHtml5',
                    title: function () { return documentTitle(); },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    footer: true,
                    download: 'open',
                    customize: function (doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    },
                }
            ]
        }).container().appendTo($('#statistics_export'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#kt_ecommerce_report_shipping_export_menu [data-kt-ecommerce-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-ecommerce-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }

    var _sendEmailSingle = function() {
        $(table).on('click', '.btnSendEmailSingle', function(e){
            e.stopPropagation();
            KTApp.block(table, {overlayColor: '#000000', state: 'danger', message: Lang.get('script.please_wait')});

            var content = {};
            content.title = Lang.get('script.sending_email');
            content.message = Lang.get('script.checking_emp_email');
            var notify = $.notify(content, {
              type: 'primary',
              mouse_over:  true,
              showProgressbar: true,
              timer: 10,
              z_index: 1051,
            });

            let employee_id = $(this).data('record_id');
            let month = $(this).data('month');
            
            $.ajax({
                type: 'POST',
                url: `/${Lang.locale}/records/calendar/${employee_id}/email`,
                data: {
                  'date': month
                },
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response, status, xhr) {
                    KTApp.unblock(table);
                    console.log(response);
    
                    setTimeout(function() {
                      notify.update('message', response.success);
                      notify.update('type', 'success');
                      notify.update('progress', 100);
                    }, 5);
                },
                error: function (response) {
                    KTApp.unblock(table);
                    var err = '';
                    for (var err in response.responseJSON.errors) {
                      if (response.responseJSON.errors.hasOwnProperty(err)) {
                        err += response.responseJSON.errors[err] + '<br>';
                      }
                    }
    
                    
                    setTimeout(function() {
                      notify.update('message', String(response.responseJSON.message));
                      notify.update('type', 'danger');
                      notify.update('progress', 60);
                    }, 500);
                }
            });

        });
    };

    var _sendEmailBulk = function() {
        $('#datatable_send_email_bulk').on('click', function() {
            console.log('send email');
            var employees_ids = datatable.checkbox().getSelectedId();
            // var count = ids.length;
            
            $.ajax({
                type: 'POST',
                url: `/${Lang.locale}/records/calendar_report/bulkemail`,
                data: {
                  'employees': employees_ids,
                  'date': currentMonth
                },
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response, status, xhr) {
                    console.log(response);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    };

    return {
        // public functions
        init: function() {
            table = document.querySelector('#employee_datatable');

            _initDatatable();
            // initDateRangePicker();
            _definedMonths();
            // applyDateRange();
            // exportButtons();
            _sendEmailBulk();
            _sendEmailSingle();
        }
    };
}();

jQuery(document).ready(function() {
    EmployeeStatistics.init();
});
