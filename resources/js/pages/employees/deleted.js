"use strict";
// Class definition

var EmployeeDeleted = function() {
    // Private functions
    var datatable;

    // Public functions
    var _initDatatable = function() {
        datatable = $('#employee_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: employee_json_url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                        },
                    },
                },
                pageSize: 10,
                serverFiltering: true,
            },

            // layout definition
            layout: {
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false // display/hide footer
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
                input: $('#employee_datatable_search_query'),
                key: 'generalSearch'
            },

            // columns definition
            columns: [{
                field: 'id',
                title: '#',
                sortable: false,
                width: 25,
                type: 'number',
                selector: false,
                textAlign: 'center',
                autoHide: false,
                visible: false,
            }, {
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
                                        <a href="/${Lang.locale}/employees/${row.id}">
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
                field: 'start',
                title: Lang.get('script.entry_date'),
                // type: 'date',
                // format: 'DD/MM/YYYY',
            }, {
                field: 'end',
                title: Lang.get('script.exit_date'),
            }, {
                field: 'deleted_at',
                title: Lang.get('script.Deleted'),
                template: function(row) {
                    return moment( row.deleted_at ).format('YYYY-MM-DD');
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
                        <a href="javascript:;" class="btnRestoreSingleEmployee btn btn-sm btn-clean btn-icon" data-record_id="${row.id}" title="${Lang.get('script.Restore')}">
                            <span class="svg-icon svg-icon-md">
                                <svg id="glyph" height="24px" viewBox="0 0 64 64" width="24px" xmlns="http://www.w3.org/2000/svg">
                                    <path  opacity="0.6" d="m54.42 34.08a22.42 22.42 0 1 1 -39.25-14.81.46.46 0 0 1 .36-.17.6.6 0 0 1 .37.14l2.74 2.74a.506.506 0 0 1 .02.68 17.558 17.558 0 1 0 13.84-6.15v3.41a.5.5 0 0 1 -.75.43l-10.32-5.96a.495.495 0 0 1 0-.86l10.32-5.96a.5.5 0 0 1 .5 0 .5.5 0 0 1 .25.43v3.66a22.452 22.452 0 0 1 21.92 22.42z"/>
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

        $('#employee_datatable_search_status').selectpicker();
    };

    var singleRowDelete = function() {
      $('#employee_datatable').on('click', '.btnRestoreSingleEmployee', function(e){
        console.log('restore');
        e.stopPropagation();
        if (!confirm( Lang.get('script.are_you_sure') )) {
          return;
        }
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            url: `/employees/restore`,
            type: "POST",
            cache: false,
            datatype: 'JSON',
            data: {
              "employee_id" : theBtn.data('record_id'),
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response, status, xhr, $form) {
              datatable.reload();
            },
            error: function (response)
            {
                theBtn.removeClass('spinner spinner-right spinner-white pr-15');
                var e = '<b>' + String(response.responseJSON.message) + '</b><br>';
                for (var err in response.responseJSON.errors) {
                  if (response.responseJSON.errors.hasOwnProperty(err)) {
                    e += response.responseJSON.errors[err] + '<br>';
                  }
                }
                console.log(e);
            }
        });
      });
    }

    return {
        // public functions
        init: function() {
            _initDatatable();
            singleRowDelete();
        }
    };
}();

jQuery(document).ready(function() {
    EmployeeDeleted.init();
});
