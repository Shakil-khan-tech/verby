'use strict';
// Class definition

var CalendarReport = function() {
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
            autoHide: true,
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
                    <a href="/${Lang.locale}/records/calendar/${row.id}/print">
                      <div class="text-dark-75 font-weight-bold line-height-sm">${row.name} ${row.surname}</div>
                      <span class="font-size-sm text-dark-50 text-hover-primary">${e_function}</span>
                    </a>
                  </div>
                </div>`;

                return output;

            },
        }, {
            field: 'ORT',
            title: Lang.get('script.canton'),
            template: function(row) {
              var output = `<span class="text-dark-75 font-weight-bolder d-block font-size-lg">${row.ORT}</span>\
              <span class="text-muted font-weight-bold">${row.ORT1}</span>`;

              return output;
            },
        }, {
            field: 'function',
            title: Lang.get('script.function'),
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
            width: 80,
            autoHide: false,
            overflow: 'visible',
            template: function(row) {
                return `
                    <a href="/${Lang.locale}/records/calendar/${row.id}/print" class="btn btn-icon btn-light btn-hover-primary btn-sm w-100">
                      <span class="svg-icon svg-icon-md svg-icon-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <polygon points="0 0 24 0 24 24 0 24"></polygon>
                          <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000) " x="11" y="5" width="2" height="14" rx="1"></rect>
                          <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) "></path>
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

    $('#employee_datatable_search_device').on('change', function() {
      datatable.search($(this).val().toLowerCase(), 'device');
    });

    $('#employee_datatable_search_status').on('change', function() {
        datatable.search($(this).val().toLowerCase(), 'function');
    });

    $('#employee_datatable_search_device, #employee_datatable_search_status').selectpicker();
};

  return {
    // Public functions
    init: function() {
      // init dmeo
      _initDatatable();
    },
  };
}();

jQuery(document).ready(function() {
  CalendarReport.init();
});
