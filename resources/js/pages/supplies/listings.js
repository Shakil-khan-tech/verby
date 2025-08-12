"use strict";
// Class definition

var IndexRecord = function() {
    // Private functions
    var requested_datatable;
    var fixed_datatable;
    var addModal = $('#addSupplyListingModal');
    var modalForm = $('#addSupplyListingForm');
    var btnModalGo = $('#btnModalGo');
    var btnGeneratePdf = $('#generate_pdf');
    var pdfStartDate = null;
    var pdfEndDate = null;

    var initTooltips = function() {
      $('body').tooltip({
        selector: '.tooltiped'
      });
    };

    var initInputs = function() {
      $('#listing_device').select2({
        width: '100%'
      });
      $('#listing_supply').select2({
        width: '100%'
      });
    };

    // listings requested_datatable
    var initRequestedListings = function() {

      requested_datatable = $('#requested_supply_listing_datatable').KTDatatable({
          // datasource definition
          data: {
              type: 'remote',
              source: {
                  read: {
                      url: listings_json_url  + '?type=0',
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                      },
                      map: function(raw) {
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
              input: $('#requested_supply_listing_datatable_search_query'),
              key: 'generalSearch'
          },

          rowGroup: {
            dataSrc: 'room_id',
          },

          // columns definition
          columns: [{
              field: 'id',
              title: '#',
              width: 40,
              type: 'number',
              selector: false,
              textAlign: 'center',
              autoHide: false,
          },
          {
            field: 'supply_id',
            title: Lang.get('script.Inventory'),
            class: 'font-bold',
            template: function(row) {
                return row.supply.name;
            },
          },
          {
            field: 'device_id',
            title: Lang.get('script.device'),
            template: function(row) {
                return row.device.name;
            },
          },
          {
            field: 'date_requested',
            title: Lang.get('script.date_requested'),
            template: function(row) {
                return moment(row.date_requested).format('DD.MM.YYYY HH:mm:ss');
            },
          },
          {
            field: 'user_requested',
            title: Lang.get('script.user_requested'),
            template: function(row) {
              return row.user_requested.name;
            },
          },
          {
            field: 'comment',
            class: 'dt_comment italic',
            title: Lang.get('script.comment'),
            sortable: false,
            template: function(row) {
              if ( row.comment ) {
                return `<div class="tooltiped" data-toggle="tooltip" data-html="true" title="${row.comment}">
                  ${row.comment}
                </div>`; 
              } else {
                return Lang.get('script.no_comment');
              }
            },
          },
          {
            field: 'Actions',
            title: Lang.get('script.Actions'),
            sortable: false,
            width: 125,
            overflow: 'visible',
            autoHide: false,
            template: function(row) {
                return `
                  <a href="javascript:;" class="btn btn-sm btn-light-primary" data-supply_id="${row.id}">
                    ${Lang.get('script.fulfill_order')}
                  </a>
                `;
            },
          }],

          //translation
          translate: TraceLocales.datatables(),

      });

      $('#requested_supply_listing_datatable_search_device').on('change', function() {
        requested_datatable.search($(this).val().toLowerCase(), 'device');
      });

      $('#requested_supply_listing_datatable_search_device').selectpicker();

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
      }, function(start, end, label) {
          $('#kt_daterangepicker_4 .form-control').val( start.format('MM/DD/YYYY h:mm A') + ' / ' + end.format('MM/DD/YYYY h:mm A'));
          requested_datatable.search( {'start': start.format('YYYY-MM-DD HH:mm:00'), 'end': end.format('YYYY-MM-DD HH:mm:00')}, 'Date');
          pdfStartDate = start.format('MM/DD/YYYY');
          pdfEndDate = end.format('MM/DD/YYYY');
      });

      daterangepicker.on('cancel.daterangepicker', function (ev, picker) {
          $(this).val('');
          requested_datatable.search( {'start': null, 'end': null}, 'Date');
          pdfStartDate = null;
          pdfEndDate = null;
      });

      $('#clearRequestedDaterangepicker').click(function(){
          $('#requestedDaterangepicker').val('');
          requested_datatable.search( {'start': null, 'end': null}, 'Date');
          pdfStartDate = null;
          pdfEndDate = null;
      });

      requested_datatable.on('click', '[data-supply_id]', function() {
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
          url: "/supplies/listings/fix",
          type: "POST",
          cache: false,
          datatype: 'JSON',
          data: {
            "listing" : $(this).data('supply_id')
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response, status, xhr, $form) {
            console.log(response);
            $('.current_requested_listings').html(response.total_requested);
            $('.current_fixed_listings').html(response.total_fixed);
            requested_datatable.reload();
            fixed_datatable.reload();
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

    // listings fixed_datatable
    var initFixedListings = function() {

      fixed_datatable = $('#fixed_supply_listing_datatable').KTDatatable({
          // datasource definition
          data: {
              type: 'remote',
              source: {
                  read: {
                      url: listings_json_url  + '?type=1',
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                      },
                      map: function(raw) {
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
              input: $('#fixed_supply_listing_datatable_search_query'),
              key: 'generalSearch'
          },

          rowGroup: {
            dataSrc: 'room_id',
          },

          // columns definition
          columns: [{
              field: 'id',
              title: '#',
              width: 40,
              type: 'number',
              selector: false,
              textAlign: 'center',
              autoHide: false,
          },
          {
            field: 'supply_id',
            title: Lang.get('script.Inventory'),
            class: 'font-bold',
            template: function(row) {
                return row.supply.name;
            },
          },
          {
            field: 'device_id',
            title: Lang.get('script.device'),
            template: function(row) {
                return row.device.name;
            },
          },
          {
            field: 'date_requested',
            title: Lang.get('script.date_requested'),
            template: function(row) {
                return moment(row.date_requested).format('DD.MM.YYYY HH:mm:ss');
            },
          },
          {
            field: 'date_fixed',
            title: Lang.get('script.date_fixed'),
            template: function(row) {
                return moment(row.date_fixed).format('DD.MM.YYYY HH:mm:ss');
            },
          },
          {
            field: 'user_requested',
            title: Lang.get('script.user_requested'),
            template: function(row) {
              return row.user_requested.name;
            },
          },
          {
            field: 'user_fixed',
            title: Lang.get('script.user_fixed'),
            template: function(row) {
              return row.user_fixed.name;
            },
          },
          {
            field: 'comment',
            class: 'dt_comment',
            title: Lang.get('script.comment'),
            sortable: false,
            template: function(row) {
              if ( row.comment ) {
                return `<div class="tooltiped" data-toggle="tooltip" data-html="true" title="${row.comment}">
                  ${row.comment}
                </div>`; 
              } else {
                return Lang.get('script.no_comment');
              }
            },
          },
          {
              field: 'Actions',
              title: Lang.get('script.Actions'),
              sortable: false,
              width: 125,
              overflow: 'visible',
              autoHide: false,
              template: function(row) {
                  return `\
                    <a href="javascript:;" class="btnDeleteSingleRecord btn btn-sm btn-clean btn-icon" data-record_id="${row.id}" title="${Lang.get('script.delete_inventory')}">\
                        <span class="svg-icon svg-icon-md">\
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                    <rect x="0" y="0" width="24" height="24"/>\
                                    <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"/>\
                                    <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>\
                                </g>\
                            </svg>\
                        </span>\
                    </a>\
                  `;
              },
          }],

          //translation
          translate: TraceLocales.datatables(),

      });

      $('#fixed_supply_listing_datatable_search_device').on('change', function() {
        fixed_datatable.search($(this).val().toLowerCase(), 'device');
      });

      $('#fixed_supply_listing_datatable_search_device').selectpicker();

      var daterangepicker = $('#fixedDaterangepicker').daterangepicker({
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
          fixed_datatable.search( {'start': start.format('YYYY-MM-DD HH:mm:00'), 'end': end.format('YYYY-MM-DD HH:mm:00')}, 'Date');
      });

      daterangepicker.on('cancel.daterangepicker', function (ev, picker) {
          $(this).val('');
          fixed_datatable.search( {'start': null, 'end': null}, 'Date');
      });

      $('#clearFixedDaterangepicker').click(function(){
          $('#fixedDaterangepicker').val('');
          fixed_datatable.search( {'start': null, 'end': null}, 'Date');
      });
    };

    var singleRowDelete = function() {
      $('#fixed_supply_listing_datatable').on('click', '.btnDeleteSingleRecord', function(e){
        e.stopPropagation();
        if (!confirm(Lang.get('script.are_you_sure'))) {
          return;
        }
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            url: "/supplies/listings/delete/" + theBtn.data('record_id'),
            type: "DELETE",
            cache: false,
            datatype: 'JSON',
            data: {
              // "listing" : theBtn.data('record_id'),
              "_method": "DELETE"
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response, status, xhr, $form) {
              $('.current_requested_listings').html(response.total_requested);
              $('.current_fixed_listings').html(response.total_fixed);
              requested_datatable.reload();
              fixed_datatable.reload();
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

    var addSupplyListing = function() {
      btnModalGo.on('click', function(e) {
        e.preventDefault();
        KTApp.block(modalForm, {overlayColor: '#000000', state: 'danger', message: 'Please wait...'});
        $.ajax({
            type: 'PATCH',
            url: modalForm.attr('action'),
            data: modalForm.serialize(),
            // data: {
            //   'date': selectedDate,
            //   'employee': selectedEmployee,
            //   // 'clean_type': selectedClean_type,
            //   'rooms_depa': tagifyDepa.DOM.originalInput.value,
            //   'rooms_restant': tagifyRestant.DOM.originalInput.value
            // },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response, status, xhr) {
                $('.current_requested_listings').html(response.total);
                KTApp.unblock(modalForm);
                addModal.modal('toggle');
                requested_datatable.reload();
                fixed_datatable.reload();
            },
            error: function (response) {
                KTApp.unblock(modalForm);
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
            }
        })
      });

      addModal.on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('#listing_room').val('').trigger('change');
        modal.find('#listing_supply').val('').trigger('change');
        modal.find('#listing_comment').val('');
      });
    }

    var generatePdf = function() {
      btnGeneratePdf.on('click', function() {
        location.href = `
        /pdf/supplies?
        type=${ 0 }&
        start=${ pdfStartDate }&
        end=${ pdfEndDate }&
        search=${ $('#requested_supply_listing_datatable_search_query').val() }&
        device=${ $('#requested_supply_listing_datatable_search_device').val() }
        `;
      });
    };

    return {
        // public functions
        init: function() {
          initInputs();
          initTooltips();
          initRequestedListings();
          initFixedListings();
            // initRooms();
          singleRowDelete();
          addSupplyListing();
          generatePdf();
            // selectEmployee();
            // initDatatableModal3();
        },
    };
}();

jQuery(document).ready(function() {
    IndexRecord.init();
});
