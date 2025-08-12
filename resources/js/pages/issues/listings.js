"use strict";
// Class definition

var IndexRecord = function() {
    // Private functions
    var daterangepicker;
    var requested_datatable;
    var fixed_datatable;
    var addModal = $('#addIssueListingModal');
    var modalForm = $('#addIssueListingForm');
    var btnModalGo = $('#btnModalGo');
    var viewModal = $('#viewIssueListingModal');
    var fixed_listings;
    var btnGeneratePdf = $('#generate_pdf');
    var pdfStartDate = null;
    var pdfEndDate = null;
    
    var initTooltips = function() {
      $('body').tooltip({
        selector: '.tooltiped'
      });
    };

    var initInputs = function() {
      $('#hotel').select2({
        placeholder: Lang.get('script.select_hotel'),
        width: '100%'
      });
      $('#listing_room').select2({
        width: '100%'
      });
      $('#listing_issue').select2({
        width: '100%'
      });

      $('#hotel').on('select2:select', function (e) {
        var device_id = e.params.data.id;
        populate_rooms( device_id );
      });
    };

    // listings requested_datatable
    var initRequestedListings = function() {

      requested_datatable = $('#requested_issue_listing_datatable').KTDatatable({
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
          iDeferLoading: null,

          // layout definition
          layout: {
              scroll: false,
              footer: false,
          },

          // column sorting
          sortable: true,

          pagination: true,

          search: {
              input: $('#requested_issue_listing_datatable_search_query'),
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
            field: 'issue_id',
            title: Lang.get('script.Issue'),
            class: 'font-bold',
            template: function(row) {
                return row.issue.name;
            },
          },
          {
            field: 'device',
            title: Lang.get('script.device'),
            template: function(row) {
                return row.room.device.name;
            },
          },
          {
            field: 'room_id',
            title: Lang.get('script.room_name'),
            template: function(row) {
                return row.room.name;
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
            field: 'comment_requested',
            class: 'dt_comment italic',
            title: Lang.get('script.comment'),
            sortable: false,
            template: function(row) {
              if ( row.comment_requested ) {
                return `<div class="tooltiped" data-toggle="tooltip" data-html="true" title="${row.comment_requested}">
                  ${row.comment_requested}
                </div>`; 
              } else {
                return Lang.get('script.no_comment');
              }
            },
          },
          {
            field: 'priority',
            title: Lang.get('script.priority'),

            template: function(row) {
              return `<span class="label label-xl label-light-${constants.priority_colors[row.priority]} label-pill label-inline">${constants.priorities[row.priority]}</span>`;
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
                  <a href="javascript:;" class="btn btn-sm btn-light-primary" data-issue_id="${row.id}">
                      ${Lang.get('script.fix_issue')}
                  </a>
                `;
                  return `\
                      <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-issue_id="${row.id}" title="${Lang.get('script.fix_issue')}">\
                          <span class="svg-icon svg-icon-md">\
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                              <g id="Stockholm-icons-/-Code-/-Done-circle" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                <rect id="bound" x="0" y="0" width="24" height="24"></rect>\
                                <circle id="Oval-5" fill="#000000" opacity="0.3" cx="12" cy="12" r="10"></circle>\
                                <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" id="Path-92" fill="#000000" fill-rule="nonzero"></path>\
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

      $('#requested_issue_listing_datatable_search_device').on('change', function() {
        requested_datatable.search($(this).val().toLowerCase(), 'device');
      });

      $('#requested_issue_listing_datatable_search_device').selectpicker();

      daterangepicker = $('#requestedDaterangepicker').daterangepicker({
          buttonClasses: ' btn',
          applyClass: 'btn-primary',
          cancelClass: 'btn-secondary',

          // autoUpdateInput: false,

          timePicker: true,
          timePickerIncrement: 30,
          locale: {
              format: 'MM/DD/YYYY'
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
          $('#kt_daterangepicker_4 .form-control').val( start.format('MM/DD/YYYY') + ' / ' + end.format('MM/DD/YYYY'));
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

      requested_datatable.on('click', '[data-issue_id]', function() {
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
          url: "/issues/listings/fix",
          type: "POST",
          cache: false,
          datatype: 'JSON',
          data: {
            "listing" : $(this).data('issue_id')
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
    };

    // listings fixed_datatable
    var initFixedListings = function() {

      fixed_datatable = $('#fixed_issue_listing_datatable').KTDatatable({
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
                          fixed_listings = dataSet;
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
              input: $('#fixed_issue_listing_datatable_search_query'),
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
            field: 'issue_id',
            title: Lang.get('script.Issue'),
            class: 'font-bold',
            template: function(row) {
                return row.issue.name;
            },
          },
          {
            field: 'device',
            title: Lang.get('script.device'),
            template: function(row) {
                return row.room.device.name;
            },
          },
          {
            field: 'room_id',
            title: Lang.get('script.room_name'),
            template: function(row) {
                return row.room.name;
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
            field: 'email_fixed',
            title: Lang.get('script.email_fixed'),
            template: function(row) {
              return row.email_fixed;
            },
          },
          {
            field: 'priority',
            title: Lang.get('script.priority'),
            template: function(row) {
              return `<span class="label label-xl label-light-${constants.priority_colors[row.priority]} label-pill label-inline">${constants.priorities[row.priority]}</span>`;
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
                    <button data-record-id="${row.id}" class="btn btn-sm btn-clean" title="${Lang.get('script.view_records')}" data-toggle="modal" data-target="#viewIssueListingModal">
                      <i class="flaticon2-document"></i> ${Lang.get('script.details')}
                    </button>

                    <a href="javascript:;" class="btnDeleteSingleRecord btn btn-sm btn-clean btn-icon" data-record_id="${row.id}" title="${Lang.get('script.delete_issue')}">\
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

      $('#fixed_issue_listing_datatable_search_device').on('change', function() {
        fixed_datatable.search($(this).val().toLowerCase(), 'device');
      });

      $('#fixed_issue_listing_datatable_search_device').selectpicker();

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
      $('#fixed_issue_listing_datatable').on('click', '.btnDeleteSingleRecord', function(e){
        e.stopPropagation();
        if (!confirm(Lang.get('script.are_you_sure'))) {
          return;
        }
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            url: "/issues/listings/delete/" + theBtn.data('record_id'),
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

    var addIssueListing = function() {
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
        modal.find('#hotel').val('').trigger('change');
        modal.find('#listing_room').val('').trigger('change');
        modal.find('#listing_issue').val('').trigger('change');
        modal.find('#listing_comment').val('');
      });
    }

    var initListingModal = function() {
      viewModal.on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var listing_id = button.data('record-id') // Extract info from data-* attributes
        var listing = fixed_listings.find(o => o.id === listing_id);

        // var device = current_records.find(o => o.id === record_id).device.name;
        // $('#roomsTitle').find('span').html(device);
        viewModal.find('[data-trace-listing="requested_comment"').html( '' ); //reset
        viewModal.find('[data-trace-listing="fixed_comment"').html( '' ); //reset
        viewModal.find('[data-trace-listing="images"').html( '' ); //reset
        viewModal.find('[data-trace-listing="priority"').html( '' ); //reset

        viewModal.find('[data-trace-listing="issue"').html( listing.issue.name );
        viewModal.find('[data-trace-listing="room"').html( listing.room.name );
        viewModal.find('[data-trace-listing="hotel"').html( listing.room.device.name );
        viewModal.find('[data-trace-listing="requested_date"').html( listing.date_requested );
        viewModal.find('[data-trace-listing="requested_comment"').html( listing.comment_requested );

        viewModal.find('[data-trace-listing="fixed_email"').html( listing.email_fixed );
        viewModal.find('[data-trace-listing="fixed_date"').html( listing.date_fixed );
        viewModal.find('[data-trace-listing="fixed_comment"').html( listing.comment_fixed );
        viewModal.find('[data-trace-listing="priority"').html( constants.priorities[ listing.priority ] );
        

        if ( listing.media.length ) {
          let element = '<div class="grid grid-cols-3 gap-2">';
          listing.media.forEach(media => {
            element += `
            <div class="">
              <a class="d-block overlay" data-fslightbox="lightbox-basic" href="${media.original_url}">
                <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-150px"
                    style="background-image:url('${media.original_url}')">
                </div>
                <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                    <i class="fa fa-eye text-white icon-xl"></i>
                </div>
              </a>
            </div>
            `;
          });
          element += '</div>';

          viewModal.find('[data-trace-listing="images"').html( element );

          refreshFsLightbox();
        }

        
        // let img = $('<img />', {src : listing.media[0].original_url});
        // img.appendTo( viewModal.find('[data-trace-listing="images"') );
      })
    };

    var populate_rooms = function(device_id) {

      $('#listing_room').select2({
          placeholder: Lang.get('script.select_room'),
          allowClear: false,
          ajax: {
              url: `/${Lang.getLocale()}/issues/rooms_ajax/${device_id}`,
              dataType: 'json',
              delay: 250,
              data: function(params) {
                  return {
                      query: params.term, // search term
                      page: params.page
                  };
              },
              processResults: function(data, params) {
                  params.page = params.page || 1;

                  return {
                      results: data.items,
                      pagination: {
                          more: (params.page * 30) < data.total_count
                      }
                  };
              },
              cache: true
          },
          escapeMarkup: function(markup) {
              return markup;
          }, // let our custom formatter work
          minimumInputLength: 0,
          // templateResult: formatEmployee, // omitted for brevity, see the source of this page
          // templateSelection: formatEmployeeSelection // omitted for brevity, see the source of this page
      });

    }

    var generatePdf = function() {
      btnGeneratePdf.on('click', function() {
        // console.log( daterangepicker.data('daterangepicker').startDate.format('MM/DD/YYYY') );
        location.href = `
        /pdf/issues?
        type=${ 0 }&
        start=${ pdfStartDate }&
        end=${ pdfEndDate }&
        search=${ $('#requested_issue_listing_datatable_search_query').val() }&
        device=${ $('#requested_issue_listing_datatable_search_device').val() }
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
          addIssueListing();
          initListingModal();
          generatePdf();
            // selectEmployee();
            // initDatatableModal3();
        },
    };
}();

jQuery(document).ready(function() {
    IndexRecord.init();
});
