const { log } = require("handlebars");
const { split } = require("lodash");

// Class definition
var Items = function () {
    // Private variables
    var datatable;
    var initDatatable = function () {
        datatable = $('#files_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: files_get_url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                        },
                        map: function (raw) {
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        },
                    },
                },
                pageSize: 10,
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
                input: $('#files_datatable_search_query'),
                key: 'generalSearch'
            },

            rowGroup: {
                dataSrc: 'room_id',
            },
            columns: [
                {
                    field: 'id',
                    title: '#',
                    width: 40,
                    textAlign: 'center',
                    autoHide: false,
                    template: function (row, index, datatable) {
                        let page = datatable.getCurrentPage();
                        let perpage = datatable.getPageSize();
                        return ((page - 1) * perpage) + (index + 1);
                    }
                },
                    {
        field: 'contract_name',
        title: Lang.get('script.name'),
        class: 'font-bold',
        template: function (row) {
            return `
                <div class="d-flex align-items-center gap-3" style="max-width: 100%;">
                    <!-- Contract Name (truncated if too long) -->
                    <div class="text-dark-75 font-weight-bold line-height-sm text-truncate"
                        id="file-name-${row.id}" 
                        title="${row.contract_name}"
                        style="max-width: 180px; flex-shrink: 1;">
                        ${row.contract_name}
                    </div>

                    <!-- Download Prefilled Button -->
                    <a href="/employees/${employee_id}/${row.id}/populate"
                    title="${row.contract_name}"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top"
                    class="flex-shrink-0">
                        <i class="icon-md fas fa-download"></i>
                    </a>
                </div>

            `;
        }
    },
                {
                    field: 'is_sign',
                    title: Lang.get('script.sign_contract'),
                    template: function (row) {
                        const hasSignedDocument = row.employee_file_name;
                        const displayText = hasSignedDocument ? row.employee_file_name : '-';
                        
                        return `
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="d-flex align-items-center flex-grow-1">
                                    ${hasSignedDocument ? `
                                    <div class="mr-3 d-flex flex-column align-items-center">
                                        <label class="checkbox checkbox-success">
											<input type="checkbox" class="contract-sign-status" 
                                                ${row.is_signed ? 'checked' : ''}
                                                data-contract-id="${row.employee_contract_id}"
                                                data-id="${row.id}">
										<span></span></label>
                                    </div>
                                    ` : ''}
                                    
                                    <div class="flex-grow-1" 
                                        style="max-width: 200px;" 
                                        title="${displayText}">
                                        ${displayText}
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    field: 'Actions',
                    title: Lang.get('script.Actions'),
                    sortable: false,
                    width: 150,
                    overflow: 'visible',
                    autoHide: false,
                    template: function (row) {
                        const hasSignedDocument = row.employee_file_name;
                        
                        return `
                            <div class="d-flex align-items-center">
                                <!-- Download Signed Document (if exists) -->
                                ${hasSignedDocument ? `
                                    
                                    <a href="/employees/${row.employee_contract_id}/signeddocument" 
                                        title="${Lang.get('script.signed_document_download')}"
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top">
                                            <i class="icon-md fas fa-download"></i>
                                    </a>` : ''
                                }
                                
                                <!-- Upload Button -->
                                <a href="javascript:;" 
                                class="btnUploadFile btn btn-sm btn-clean btn-icon ml-2"
                                data-contract-id="${row.id}" 
                                title="${Lang.get('script.upload')}">
                                    <i class="icon-md fas fa-cloud-upload-alt"></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            //translation
            translate: TraceLocales.datatables(),

        });

        var daterangepicker = $('#requestedDaterangepicker').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',

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

        // Handle checkbox changes
        $(document).on('change', 'input[type="checkbox"]', function () {
            const contractId = $(this).data('id');
            const isChecked = $(this).is(':checked');

            // Add your logic here to handle the checkbox state change
            console.log(`Contract ${contractId} signed status changed to: ${isChecked}`);
        });
    };
    var deleteFile = function () {
        datatable.on('click', '.btnDeleteFile', function (e) {
            e.stopPropagation();
            if (!confirm(Lang.get('script.are_you_sure'))) {
                return;
            }
            var theBtn = $(this);
            theBtn.addClass('spinner spinner-right spinner-white pr-15');

            $.ajax({
                // url: formEl.attr('action'),
                url: `/contracts/${theBtn.data('record_id')}`,
                type: "DELETE",
                cache: false,
                datatype: 'JSON',
                data: {
                    "id": theBtn.data('record_id'),
                    "_method": "DELETE"
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response, status, xhr, $form) {
                    var content = {};
                    content.title = response.success;
                    content.message = '';
                    var notify = $.notify(content, {
                        type: 'success',
                        mouse_over: true,
                        z_index: 1051,
                    });
                    datatable.reload();
                },
                error: function (response) {
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

    // $('#uploadContractForm').on('submit', function (e) {
    //     e.preventDefault();
    //     const formData = new FormData(this);

    //     $.ajax({
    //         url: contract_store_url, // create this route
    //         method: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function (res) {
    //             $('#uploadContractForm')[0].reset();
    //             $('.custom-file-label').html('Choose file...');
    //             $('#uploadContractModal').modal('hide');
    //             datatable.reload();
    //         },
    //         error: function (err) {
    //             $.notify({
    //                 title: 'Error',
    //                 message: xhr.responseJSON.message || 'An error occurred'
    //             }, {
    //                 type: 'danger'
    //             });
    //         }
    //     });
    // });
    $('#uploadContractForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: contract_store_url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $('#uploadContractForm')[0].reset();
                $('.custom-file-label').html('Choose file...');
                $('#uploadContractModal').modal('hide');
                datatable.reload();

                // Show success notification
                $.notify({
                    title: 'Success',
                    message: res.message || 'File uploaded successfully'
                }, {
                    type: 'success'
                });
            },
            error: function (xhr, status, error) {
                // Show error notification
                let errorMessage = 'An error occurred';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }

                $.notify({
                    title: 'Error',
                    message: errorMessage
                }, {
                    type: 'danger'
                });
            }
        });
    });
    return {
        // public functions
        init: function () {
            initDatatable();
            deleteFile();
        }
    };


}();

// Handle checkbox changes
$(document).on('change', '.contract-sign-status', function () {
    const contractId = $(this).data('contract-id');
    const isChecked = $(this).is(':checked') ? 1 : 0;
    const checkbox = $(this);

    $.ajax({
        url: `/employee-contracts/${contractId}/update-sign-status`,
        method: 'POST',
        data: {
            is_signed: isChecked,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            checkbox.prop('disabled', true);
        },
        success: function (response) {
            $.notify({
                title: response.success ? 'Success' : 'Warning',
                message: response.message
            }, {
                type: response.success ? 'success' : 'warning'
            });

            if (!response.success) {
                checkbox.prop('checked', !isChecked);
            }
        },
        error: function (xhr) {
            $.notify({
                title: 'Error',
                message: xhr.responseJSON.message || 'An error occurred'
            }, {
                type: 'danger'
            });
            checkbox.prop('checked', !isChecked);
        },
        complete: function () {
            checkbox.prop('disabled', false);
        }
    });
});

$(document).on('click', '.btnUploadFile', function () {
    const contractId = $(this).data('contract-id');;
    $('#modal_contract_id').val(contractId);
    $('#uploadContractModal').modal('show');
});

$('#cancelUploadBtn').click(function () {
    // Reset form
    $('#uploadContractForm')[0].reset();
    $('.custom-file-label').html('Choose file...');
    // Close modal
    $('#uploadContractModal').modal('hide');
});

jQuery(document).ready(function () {
    Items.init();
});



