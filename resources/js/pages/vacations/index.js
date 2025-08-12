"use strict";
// Class definition

var KTDatatableRecordSelectionDemo = function() {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    // url: 'https://preview.keenthemes.com/metronic/theme/html/tools/preview/api/datatables/demos/default.php',
                    url: vacation_json_url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                    },
                },
            },
            pageSize: 10,
            // serverPaging: true,
            // serverFiltering: true,
            // serverSorting: true,
        },

        // layout definition
        layout: {
            scroll: false, // enable/disable datatable scroll both horizontal and
            footer: false // display/hide footer
        },

        // column sorting
        sortable: true,

        pagination: true,

        // columns definition
        columns: [{
            field: 'RecordID',
            title: '#',
            sortable: false,
            width: 20,
            selector: true,
            textAlign: 'center',
            autoHide: false,
        }, {
            field: 'name',
            title: Lang.get('script.Employee'),
            autoHide: false,
            template: function(row) {
                var states = ['success', 'info', 'primary', 'warning', 'danger'];
                var state = (typeof states[row.function] === 'undefined') ? states[4] : states[row.function];
                var e_function = row.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

                var output = `<div class="d-flex align-items-center">\
                  <div class="symbol symbol-40 symbol-${state} flex-shrink-0">\
                    <div class="symbol-label">${row.emp_id}</div>\
                  </div>\
                  <div class="ml-2">\
                    <a href="/${Lang.locale}/employees/${row.emp_id}">\
                      <div class="text-dark-75 font-weight-bold line-height-sm">${row.name} ${row.surname}</div>\
                      <span class="font-size-sm text-dark-50 text-hover-primary">${e_function}</span>\
                    </a>\
                  </div>\
                </div>`;

                return output;

            },
        }, {
            field: 'fillimi',
            title: Lang.get('script.start'),
            template: '{{fillimi}}',
        }, {
            field: 'mbarimi',
            title: Lang.get('script.end'),
            template: '{{mbarimi}}',
        }, {
            field: 'days',
            title: Lang.get('script.days'),
            template: '{{days}}',
        }, {
            field: 'Status',
            title: Lang.get('script.status'),
            // callback function support for column rendering
            template: function(row) {
                var status = {
                    1: {'title': Lang.get('script.in_vacation'), 'class': ' label-light-primary'},
                    2: {'title': Lang.get('script.finished'), 'class': ' label-light-info'},
                    3: {'title': Lang.get('script.to_happen'), 'class': ' label-light-success'},
                    4: {'title': Lang.get('script.other'), 'class': ' label-light-danger'},
                };
                var today = moment().format('YYYY-MM-DD');
                var type = 4;
                if( today >= row.fillimi && today <= row.mbarimi ) {
                  type = 1;
                } else if (today > row.mbarimi) {
                  //pushimi ka kalu
                  type = 2;
                } else if ( today < row.fillimi) {
                  //pushimi do te jete
                  type = 3;
                } else {
                  //tjetersend
                  type = 4;
                }
                return '<span class="label label-lg font-weight-bold' + status[type].class + ' label-inline">' + status[type].title + '</span>';
            },
        }, {
            field: 'Actions',
            title: Lang.get('script.Actions'),
            sortable: false,
            width: 125,
            overflow: 'visible',
            textAlign: 'left',
	          autoHide: false,
            template: function(row) {
                return `
                    <a href="javascript:;" class="btnDeleteSingleVacation btn btn-sm btn-clean btn-icon" data-record_id="${row.RecordID}" title="${Lang.get('script.Delete')}">
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
    };
    var datatable;

    // basic demo
    var localSelectorDemo = function() {
        // enable extension
        options.extensions = {
            // boolean or object (extension options)
            checkbox: true,
        };

        options.search = {
            input: $('#kt_datatable_search_query'),
            key: 'generalSearch'
        };

        datatable = $('#kt_datatable').KTDatatable(options);

        $('#kt_datatable_search_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        $('#kt_datatable_search_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Type');
        });

        $('#kt_datatable_search_status, #kt_datatable_search_type').selectpicker();

        datatable.on(
            'datatable-on-check datatable-on-uncheck',
            function(e) {
                var checkedNodes = datatable.rows('.datatable-row-active').nodes();
                var count = checkedNodes.length;
                $('#kt_datatable_selected_records').html(count);
                if (count > 0) {
                    $('#kt_datatable_group_action_form').collapse('show');
                } else {
                    $('#kt_datatable_group_action_form').collapse('hide');
                }
            });

        $('#kt_datatable_delete_all').on('click', function(e) {
          e.stopPropagation();
          if (!confirm(Lang.get('script.are_you_sure'))) {
            return;
          }
          var theBtn = $(this);
          theBtn.addClass('spinner spinner-right spinner-white pr-15');
          var ids = datatable.rows('.datatable-row-active').
          nodes().
          find('.checkbox > [type="checkbox"]').
          map(function(i, chk) {
              return $(chk).val();
          });

          $.ajax({
              // url: formEl.attr('action'),
              url: "/vacations/delete",
              type: "POST",
              cache: false,
              datatype: 'JSON',
              data: {
                "records" : ids.toArray(),
              },
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response, status, xhr, $form) {
                datatable.reload();
                theBtn.removeClass('spinner spinner-right spinner-white pr-15');
                $('#kt_datatable_group_action_form').collapse('hide');
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
    };

    var singleRowDelete = function() {
      $('#kt_datatable').on('click', '.btnDeleteSingleVacation', function(e){
        e.stopPropagation();
        if (!confirm(Lang.get('script.are_you_sure'))) {
          return;
        }
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            // url: formEl.attr('action'),
            url: "/vacations/delete",
            type: "POST",
            cache: false,
            datatype: 'JSON',
            data: {
              "records" : [theBtn.data('record_id')],
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response, status, xhr, $form) {
              datatable.reload();
              $('#kt_datatable_group_action_form').collapse('hide');
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
            localSelectorDemo();
            singleRowDelete();
            // serverSelectorDemo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRecordSelectionDemo.init();
});
