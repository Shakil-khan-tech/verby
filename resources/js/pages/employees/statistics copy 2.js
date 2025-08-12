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

    // Public functions
    var _initDatatable = function() {
        datatable = $(table).DataTable({
            // datasource definition
            // data: {
            //     type: 'remote',
            //     source: {
            //         read: {
            //             url: statistics_json_url,
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
            //             },
            //             params: {
            //                 query: {
            //                     active_status: $('#employee_datatable_search_active').val(),
            //                 },
            //             },
            //             error: function (xhr, ajaxOptions, thrownError) {
            //                 console.log(xhr);
            //                 console.log(ajaxOptions);
            //                 console.log(thrownError);

            //                 var content = {};
			// 				content.title = thrownError;
			// 				content.message = Lang.get('script.narrow_down_your_search');
			// 				var notify = $.notify(content, {
			// 					type: 'danger',
			// 					mouse_over:  true,
			// 					z_index: 1051,
			// 				});
            //             },
            //         },
            //     },
            //     pageSize: 10,
            //     serverPaging: true,
            //     serverFiltering: true,
            //     serverSorting: true,
            // },

            responsive: true,
			buttons: [
				'print',
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				'pdfHtml5',
			],
			processing: true,
			serverSide: true,
			ajax: {
				url: statistics_json_url,
				type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                },
				data: {
					// parameters for custom backend script demo
					columnsDef: [
                        'id', 'name', 'ORT', 'active_status', 'seconds', 'percentage', 'work_percetage', 'function'
                    ],
				},
                params: {
                    query: {
                        active_status: $('#employee_datatable_search_active').val(),
                    },
                },
			},
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'ORT'},
                {data: 'active_status'},
                {data: 'seconds'},
                {data: 'seconds'},
                {data: 'work_percetage'},
                {data: 'function'},
            ],
            columnDefs: [
                {
                    targets: 1,
                    render: function(data, type, full, meta) {
                        let states = ['success', 'info', 'primary', 'warning', 'danger'];
                        let state = (typeof states[full.function] === 'undefined') ? states[4] : states[full.function];
                        let e_function = full.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

                        return `
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40 symbol-${state} flex-shrink-0">
                                <div class="symbol-label">${full.id}</div>
                            </div>
                            <div class="ml-2">
                                <a href="/${Lang.locale}/records/calendar/${full.id}/print?date=${currentMonth}">
                                    <div class="text-dark-75 font-weight-bold line-height-sm">${full.name} ${full.surname}</div>
                                    <span class="font-size-sm text-dark-50 text-hover-primary">${e_function}</span>
                                </a>
                            </div>
                        </div>
                        `;
                    },
                },
            ],

            


            search: {
                input: $('#employee_datatable_search_query'),
                key: 'generalSearch'
            },

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
        // $('[data-kt-ecommerce-export="pdf"]').on('click', function(e) {
		// 	e.preventDefault();
        //     console.log('pdf');
        //     console.log(datatable);
		// 	datatable.button(4).trigger();
		// });
        // return;
        new $.fn.dataTable.Buttons(datatable, {
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

        // $(table).buttons( 0, null ).container().prependTo(
        //     table.table().container()
        // );
        return;

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
