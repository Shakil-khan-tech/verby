"use strict";

var Items = function () {
    var datatable;

    var initDatatable = function () {
        datatable = $('#files_datatable').KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: reminder_get_url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                        },
                        map: function (raw) {
                            return typeof raw.data !== 'undefined' ? raw.data : raw;
                        }
                    }
                },
                pageSize: 10,
                serverPaging: false,
                serverFiltering: false,
                serverSorting: false,
            },

            layout: {
                scroll: false,
                footer: false,
            },

            sortable: false,
            pagination: true,

            search: {
                input: $('#files_datatable_search_query'),
                key: 'generalSearch'
            },

            columns: [
                {
                    field: 'name',
                    title: Lang.get('script.name'),
                    autoHide: false,
                    template: function (row) {
                        var states = ['success', 'info', 'primary', 'warning', 'danger'];
                        var state = (typeof states[row.function] === 'undefined') ? states[4] : states[row.function];
                        var e_function = row.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

                        return `
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-${state}">
                                    <div class="symbol-label">${row.id}</div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-dark-75 font-weight-bold">${row.name} ${row.surname}</div>
                                    <small class="text-muted">${e_function}</small>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    field: 'signed_count',
                    title: Lang.get('script.signed_contract'),
                    template: function (row) {
                        const progress = Math.round((row.signed_count / row.total_contracts) * 100);
                        return `
                            <div class="d-flex align-items-center">
                                <div class="progress progress-xs w-100px mr-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: ${progress}%" 
                                        aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="font-weight-bold text-dark">${row.signed_count}/${row.total_contracts}</span>
                            </div>
                        `;
                    }
                },
                {
                    field: 'pending_contracts',
                    title: Lang.get('script.pending_contracts'),
                    autoHide: false,
                    template: function (row) {
                        if (row.pending_contract_names && row.pending_contract_names.length > 0) {
                            return `
                <div class="pending-contracts-container">
                    <button class="btn btn-sm btn-light-danger btn-smm  dropdown-toggle w-100 text-left" type="button" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="font-weight-bold">${row.pending_contract_names.length} ${Lang.get('script.pending')}</span>
                    </button>
                    <div class="dropdown-menu drop-2 dropdown-menu-right p-0" style="min-width: 250px;max-height:250px;overflow:auto;">
                        <div class="p-3">
                            <h6 class="font-weight-bolder text-dark mb-3">Pending Contracts</h6>
                            <div class="contract-list">
                                ${row.pending_contract_names.map(name => `
                                    <div class="contract-item d-flex align-items-center p-2 mb-2 bg-light-danger rounded">
                                        <i class="flaticon2-file text-danger mr-3"></i>
                                        <div class="contract-name text-dark-75 font-weight-semibold text-truncate" 
                                            title="${name}">${name}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
                        } else {
                            return `
                <div class="d-flex align-items-center p-2 bg-light-success rounded">
                    <i class="flaticon2-check-mark text-success mr-2"></i>
                    <span class="text-success font-weight-bold">All signed</span>
                </div>
            `;
                        }
                    }
                },
                {
                    field: 'gender',
                    title: Lang.get('script.gender'),
                    autoHide: false,
                    template: function (row) {
                        let gender = row.gender == 0 ? Lang.get('script.male') : Lang.get('script.female');
                        let status = row.gender == 0 ? ' label-light-dark' : ' label-light-info';
                        return `<span class="label font-weight-bold label-lg${status} label-inline">${gender}</span>`;
                    },
                },
                {
                    field: 'function',
                    title: Lang.get('script.Function'),
                    autoHide: false,
                    template: function (row) {
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
                    width: 80,
                    template: function (row) {
                        return `
                            <a href="/employees/${row.id}" class="btn btn-sm btn-clean btn-icon" title="View Profile">
                                <i class="flaticon-eye"></i>
                            </a>`;
                    }
                }
            ],

            translate: TraceLocales.datatables(),
        });

        var daterangepicker = $('#requestedDaterangepicker').daterangepicker({
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
        }, function (start, end, label) {
            $('#kt_daterangepicker_4 .form-control').val(start.format('MM/DD/YYYY h:mm A') + ' / ' + end.format('MM/DD/YYYY h:mm A'));
            datatable.search({ 'start': start.format('YYYY-MM-DD HH:mm:00'), 'end': end.format('YYYY-MM-DD HH:mm:00') }, 'Date');
        });

        daterangepicker.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            datatable.search({ 'start': null, 'end': null }, 'Date');
        });

        $('#clearRequestedDaterangepicker').click(function () {
            $('#requestedDaterangepicker').val('');
            datatable.search({ 'start': null, 'end': null }, 'Date');
        });
    };

    return {
        init: function () {
            initDatatable();
        }
    };
}();

jQuery(document).ready(function () {
    Items.init();

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.drop-2') && !e.target.classList.contains('btn-smm')) {
            document.querySelectorAll('.drop-2').forEach(drop => {
                drop.style.display = 'block';
            });
        }
        if (e.target && e.target.classList.contains('btn-smm')) {
            document.querySelectorAll('.drop-2').forEach(drop => {
                drop.style.display = 'block';
            });
            setTimeout(() => {
                document.querySelectorAll('.drop-2').forEach(drop => {
                    drop.style.display = 'block';   
                });
            }, 1000)
        }
    },true);

});