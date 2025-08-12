"use strict";
// Class definition

var EmployeeDatatable = function() {
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
                pageSize: 10, //revert to 10 as default
                serverFiltering: true,
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
                autoHide: false,
            }, {
                field: 'start',
                title: Lang.get('script.entry_date'),
                autoHide: false,
                // type: 'date',
                // format: 'DD/MM/YYYY',
            }, {
                field: 'end',
                title: Lang.get('script.exit_date'),
                autoHide: false,
            }, {
                field: 'active_status',
                title: Lang.get('script.Active'),
                autoHide: false,
                template: function(row) {
                    let active = row.active_status == 1 ? Lang.get('script.validations.yes') : Lang.get('script.validations.no');
                    let status = row.active_status == 1 ? ' label-light-success' : ' label-light-danger';
                    return `<span class="label font-weight-bold label-lg${status} label-inline">${active}</span>`;
                },
            }, {
                field: 'entries_status',
                title: Lang.get('script.active_again'),
                autoHide: false,
                template: function(row) {
                    let active = row.entries_status == 1 ? Lang.get('script.validations.yes') : Lang.get('script.validations.no');
                    let status = row.entries_status == 1 ? ' label-light-success' : ' label-light-danger';
                    return `<span class="label font-weight-bold label-lg${status} label-inline">${active}</span>`;
                },
            }, {
                field: 'gender',
                title: Lang.get('script.gender'),
                autoHide: false,
                template: function(row) {
                    let gender = row.gender == 0 ? Lang.get('script.male') : Lang.get('script.female');
                    let status = row.gender == 0 ? ' label-light-dark' : ' label-light-info';
                    return `<span class="label font-weight-bold label-lg${status} label-inline">${gender}</span>`;
                },
            }, {
                field: 'updated_at',
                title: Lang.get('script.Updated'),
                autoHide: false,
                template: function(row) {
                    return moment( row.updated_at ).format('YYYY-MM-DD');
                },
            },
            {
                field: 'function',
                title: Lang.get('script.Function'),
                autoHide: false,
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
                        6: {
                            'title': Lang.get('script.canceled'),
                            'class': ' label-light-danger'
                        },
                    };
                    if (typeof status[row.function] === 'undefined') {
                      return '<span class="label font-weight-bold label-lg' + status[4].class + ' label-inline">' + status[4].title + '</span>';
        						}
                    return '<span class="label font-weight-bold label-lg' + status[row.function].class + ' label-inline">' + status[row.function].title + '</span>';
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
                    return `
                        <a href="/${Lang.locale}/employees/${row.id}" class="btn btn-sm btn-clean btn-icon mr-2" title="${Lang.get('script.edit_details')}">
                            <span class="svg-icon svg-icon-md">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero"\ transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                    </g>
                                </svg>
                            </span>
                        </a>
                        <a href="javascript:;" class="btnDeleteSingleEmployee btn btn-sm btn-clean btn-icon" data-record_id="${row.id}" title="${Lang.get('script.Delete')}">
                            <span class="svg-icon svg-icon-md">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
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

        datatable.on('datatable-on-init', function (e, settings) {
            // KTApp.unblock('.datatable');
        });

        datatable.on('datatable-on-layout-updated', function (e, settings) {
            // count genders
            let males = 0;
            let females = 0;
            datatable.getDataSet().forEach(employee => {
                employee.gender == 0 ? males++ : females++;
            });

            let _info = $('.datatable-pager-detail');

            // remove old (everything after the first <br>)
            _info.html(_info.html().split('<br>')[0]);

            _info.html(`${_info.html()} <br> ${Lang.get('script.of_which_males_females', {m: males, f:females})}`);
        });

        $('#employee_datatable_search_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'function');
        });
        $('#employee_datatable_search_active').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'active_status');
        });
        $('#employee_datatable_search_gender').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'gender');
        });
        $('#employee_datatable_search_hotel').on('change', function() {
            datatable.search($(this).val(), 'device');
        });

        $('#employee_datatable_search_status, #employee_datatable_search_active, #employee_datatable_search_gender,#employee_datatable_search_hotel').selectpicker();
    };

    var singleRowDelete = function() {
      $('#employee_datatable').on('click', '.btnDeleteSingleEmployee', function(e){
        e.stopPropagation();
        if (!confirm( Lang.get('script.are_you_sure') )) {
          return;
        }
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            // url: formEl.attr('action'),
            url: "/employees/" + theBtn.data('record_id'),
            type: "DELETE",
            cache: false,
            datatype: 'JSON',
            data: {
              "id" : theBtn.data('record_id'),
              "_method": "DELETE"
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
    EmployeeDatatable.init();
});
