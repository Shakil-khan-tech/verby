"use strict";
// Class definition

var EmailsIndex = function() {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: emails_json_url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                    },
                },
            },
            pageSize: 10,
            serverPaging: false,
            serverFiltering: false,
            serverSorting: false,
        },

        // layout definition
        layout: {
            scroll: false, // enable/disable datatable scroll both horizontal and
            footer: false // display/hide footer
        },

        // column sorting
        sortable: true,

        pagination: true,

        // order: [[ 1, 'asc' ]],

        // columns definition
        columns: [{
            field: 'order',
            title: '#',
            sortable: false,
            width: 20,
            textAlign: 'center',
            autoHide: false,
        }, {
            field: 'email',
            title: Lang.get('script.email'),
            sortable: 'asc',
        }, {
            field: 'login',
            title: `${Lang.get('script.quota')}: ${Lang.get('script.used')} / ${Lang.get('script.allocated')} / %`,
            sortable: false,
            autoHide: false,
            template: function(row) {
                let _class = '';
                switch (true) {
                    case row.diskusedpercent < 25:
                        _class = 'success';
                        break;
                    case (row.diskusedpercent >= 25 && row.diskusedpercent < 50):
                        _class = 'primary';
                        break;
                    case (row.diskusedpercent >= 50 && row.diskusedpercent < 80):
                        _class = 'warning';
                        break;
                    case row.diskusedpercent >= 80:
                        _class = 'danger';
                        break;
                    default:
                        break;
                }

                var output = `${row.humandiskused} / ${row.humandiskquota} / ${row.diskusedpercent}%`;
                output +=
                `<div class="progress">
                    <div class="progress-bar bg-${_class}" role="progressbar"
                        style="width: ${row.diskusedpercent_float}%" aria-valuenow="${row.diskusedpercent_float.toFixed(2)}"
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>`;

                return output;

            },
        }, {
            field: 'mtime',
            title: Lang.get('script.mod_time'),
            template: function(row) {
                return moment.unix(row.mtime).format('DD.MM.YYYY HH:mm:ss')
            }
        }, {
            field: 'Actions',
            title: Lang.get('script.Actions'),
            sortable: false,
            width: 80,
            autoHide: false,
            overflow: 'visible',
            template: function(row) {
                return `\
                    <a href="/${Lang.locale}/emails/${row.email}" class="btn btn-icon btn-light btn-hover-primary btn-sm w-100">\
                      <span class="svg-icon svg-icon-md svg-icon-primary">\
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                          <polygon points="0 0 24 0 24 24 0 24"></polygon>\
                          <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000) " x="11" y="5" width="2" height="14" rx="1"></rect>\
                          <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997) "></path>\
                          </g>\
                        </svg>\
                      </span>\
                    </a>\
                `;
            },
        }],

        //translation
        translate: TraceLocales.datatables(),
    };
    var datatable;

    // basic demo
    var _initDatatable = function() {
        options.search = {
            input: $('#kt_datatable_search_query'),
            key: 'generalSearch'
        };

        datatable = $('#kt_datatable').KTDatatable(options);

        datatable.on( 'datatable-on-init datatable-on-goto-page datatable-on-update-perpage datatable-on-sort', function () {
            setTimeout(function() {
                let current_page = datatable.getCurrentPage();
                let page_size = datatable.getPageSize();
                datatable.column(0, { page: 'current' }).nodes().each( function (i, cell) {
                    cell.innerHTML = (current_page-1) * page_size + (i + 1);
                });
            }, 10);
        } );
    };

    return {
        // public functions
        init: function() {
            _initDatatable();
        },
    };
}();

jQuery(document).ready(function() {
    EmailsIndex.init();
});
