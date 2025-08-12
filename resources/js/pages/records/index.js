"use strict";
// Class definition

var IndexRecord = function() {
    // Private functions
    var datatable;
    var current_records;
    var selectEmployeeModal = $('#selectEmployee');

    // record datatable
    var initRecords = function() {

      $('body').tooltip({
        selector: '.tooltiped'
      });

      datatable = $('#records_datatable').KTDatatable({
          // datasource definition
          // stateSave: false,
          data: {
              type: 'remote',
              source: {
                  read: {
                      url: records_json_url,
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
                      },
                      map: function(raw) {
                          var dataSet = raw;
                          if (typeof raw.data !== 'undefined') {
                              dataSet = raw.data;
                          }
                          current_records = dataSet;
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
              input: $('#records_datatable_search_query'),
              key: 'generalSearch'
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
              visible: false,
          }, {
              field: 'employee_id',
              title: Lang.get('script.Employee'),
              autoHide: false,
              // template: function(row) {
              //     return row.employee.name + ' ' + row.employee.surname;
              // },
              template: function(row) {
                  var states = ['success', 'info', 'primary', 'warning', 'danger'];
                  var state = (typeof states[row.employee.function] === 'undefined') ? states[4] : states[row.employee.function];
                  var e_function = row.employee.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

                  var output = `<div class="d-flex align-items-center">
                    <div class="symbol symbol-40 symbol-${state} flex-shrink-0">
                      <div class="symbol-label">${row.employee.id}</div>
                    </div>
                    <div class="ml-2">
                      <a href="/${Lang.locale}/records/${row.id}">
                        <div class="text-dark-75 font-weight-bold line-height-sm">${row.employee.name} ${row.employee.surname}</div>
                        <span class="font-size-sm text-dark-50 text-hover-primary">${e_function}<span>
                      </a>
                    </div>
                  </div>`;

                  return output;
              },
          }, {
              field: 'device_id',
              title: Lang.get('script.device'),
              template: function(row) {
                  return `<a href="/${Lang.locale}/devices/${row.device.id}">${row.device.name}</a>`;
              },
          }, {
              field: 'action',
              title: Lang.get('script.action'),
              template: function(row) {
                return constants.actions[row.action];
              },
          }, {
              field: 'perform',
              title: Lang.get('script.perform'),
              template: function(row) {
                return constants.performs[row.perform];
              },
          }, {
              field: 'rooms',
              title: Lang.get('script.rooms'),
              sortable: false,
              overflow: 'visible',
              textAlign: 'left',
              template: function(row) {
                if ( $.isEmptyObject(row.rooms) ) {
                  return '-';
                } else {
                  return `
                    <button data-record-id="${row.id}" class="btn btn-sm btn-clean" title="${Lang.get('script.view_records')}">
                      <i class="flaticon2-document"></i> ${Lang.get('script.details')}
                    </button>`;
                }
              },
          }, {
              field: 'identity',
              title: Lang.get('script.identity'),
              template: function(row) {
                if ( row.identity == 3 && row.user != null ) { //PC
                  return `
                    <div class="tooltiped" data-toggle="tooltip" data-html="true"
                      title="${Lang.get('script.edited_by_name', {name: row.user.name})} <br> ${Lang.get('script.edited_on_date', {date: moment(row.updated_at).format('DD.MM.YYYY HH:mm:ss')})}"
                    >
                      <span>${constants.identities[row.identity]}</span>
                      <span class="svg-icon svg-icon unprint inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <circle fill="#000000" cx="9" cy="15" r="6"></circle>
                            <path
                              d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
                              fill="#000000" opacity="0.3"></path>
                          </g>
                        </svg>
                      </span>
                    </div>
                  `;
                }
                return constants.identities[row.identity];
              },
          }, {
              field: 'time',
              title: Lang.get('script.time'),
              sortable: 'desc',
              template: function(row) {
                // return row.time;
                return moment( row.time ).format('DD.MM.YYYY HH:mm:ss');
              },
          }, {
              field: 'Actions',
              title: Lang.get('script.Actions'),
              sortable: false,
              width: 125,
              overflow: 'visible',
              autoHide: false,
              template: function(row) {
                  return `\
                      <a href="/${Lang.locale}/records/${row.id}" class="btn btn-sm btn-clean btn-icon mr-2" title="${Lang.get('script.edit_details')}">\
                          <span class="svg-icon svg-icon-md">\
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                      <rect x="0" y="0" width="24" height="24"/>\
                                      <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero"\ transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>\
                                      <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>\
                                  </g>\
                              </svg>\
                          </span>\
                      </a>\
                      <a href="javascript:;" class="btnDeleteSingleRecord btn btn-sm btn-clean btn-icon" data-record_id="${row.id}" title="${Lang.get('script.delete_record')}">\
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
      

      $('#records_datatable_search_device').on('change', function() {
          datatable.search($(this).val().toLowerCase(), 'device');
      });

      $('#records_datatable_search_device').selectpicker();

      var daterangepicker = $('#recordsDaterangepicker').daterangepicker({
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
          datatable.search( {'start': start.format('YYYY-MM-DD HH:mm:00'), 'end': end.format('YYYY-MM-DD HH:mm:00')}, 'Date');
      });

      daterangepicker.on('cancel.daterangepicker', function (ev, picker) {
          $(this).val('');
          datatable.search( {'start': null, 'end': null}, 'Date');
      });

      $('#clearRecordsDaterangepicker').click(function(){
          $('#recordsDaterangepicker').val('');
          datatable.search( {'start': null, 'end': null}, 'Date');
      });

      datatable.on('click', '[data-record-id]', function() {
          $('#roomModal').modal('show', $(this));
      });
    };

    var initRooms = function() {
      $('#roomModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var record_id = button.data('record-id') // Extract info from data-* attributes
        var rooms = current_records.find(o => o.id === record_id).rooms;

        var device = current_records.find(o => o.id === record_id).device.name;
        $('#roomsTitle').find('span').html(device);

        var output = 
        `<table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">
                <span class="text-dark-75">${Lang.get('script.room_name')}</span>
              </th>
              <th scope="col">${Lang.get('script.room_category')}</th>
              <th scope="col">${Lang.get('script.clean_type')}</th>
              <th scope="col">${Lang.get('script.extra')}</th>
              <th scope="col">${Lang.get('script.status')}</th>
              <th scope="col">${Lang.get('script.volunteer')}</th>
            </tr>
          </thead>
          <tbody>
        `;

        if ( !$.isEmptyObject(rooms) ) {
          // rooms.forEach( function(room, i) {
            let counter = 0;
          $.each(rooms, function(i, room) {
            let volunteer = (room.pivot.volunteer) ? room.pivot.volunteer_name : '';
            counter += 1;
            output += '\
              <tr>\
                <td>'+ (counter) +'</span></td>\
                <td>'+ room.name +'</td>\
                <td>'+ constants.room_categories[room.category] +'</td>\
                <td>\
                  <span class="label label-xl label-light-'+ constants.colors[room.pivot.clean_type] +' label-pill label-inline">'+ constants.clean_types[room.pivot.clean_type] +'</span>\
                </td>\
                <td>'+ constants.calendar_room_extra[room.pivot.extra] +'</td>\
                <td>'+ constants.room_statuses[room.pivot.status] +'</td>\
                <td>'+ volunteer +'</td>\
              </tr>\
            ';
          });
        } else {
          output += '<tr><td class="py-1" colspan="5"></td></tr>';
        }

        output += '\
          </tbody>\
        </table>\
        ';

        var modal = $(this);
        modal.find('.rooms').html( output )
      })
    };

    var singleRowDelete = function() {
      $('#records_datatable').on('click', '.btnDeleteSingleRecord', function(e){
        e.stopPropagation();
        if (!confirm(Lang.get('script.are_you_sure'))) {
          return;
        }
        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            url: "/records/" + theBtn.data('record_id'),
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

    var selectEmployee = function() {

      
      var datatable = $('#record_employee_datatable').KTDatatable({
        data: {
          pageSize: 10, // display records per page
        },
        pagination: true,
        search: {
          input: $('#record_employee_datatable_search_query'),
          key: 'generalSearch',
        },
        layout: {
          class: 'datatable-bordered',
          scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
          height: 400, // datatable's body's fixed height
          minHeight: 400,
          footer: false, // display/hide footer
        },
        columns: [
          {
              field: 'Employee',
              title: Lang.get('script.Employee'),
              sortable: true,
              // width: 150,
              autoHide: false,
              overflow: 'visible',
              textAlign: 'left',
          }, {
              field: 'View',
              title: Lang.get('script.view'),
              sortable: false,
              width: 80,
              autoHide: false,
              overflow: 'visible',
              textAlign: 'left',
          },
        ],
        //translation
        translate: TraceLocales.datatables(),
      });
  
      $('#record_employee_datatable_search_status').on('change', function() {
        datatable.search($(this).val().toLowerCase(), 'Function');
      });
  
      $('#record_employee_datatable_search_type').on('change', function() {
        datatable.search($(this).val().toLowerCase(), 'Type');
      });
  
      $('#record_employee_datatable_search_status, #record_employee_datatable_search_type').selectpicker();

      datatable.hide();
      var alreadyReloaded = false;
      selectEmployeeModal.on('show.bs.modal', function (event) {
        // fix datatable layout after modal shown
        if (!alreadyReloaded) {
            var modalContent = $(this).find('.modal-content');
            datatable.spinnerCallback(true, modalContent);

            datatable.reload();

            datatable.on('datatable-on-layout-updated', function() {
                datatable.show();
                datatable.spinnerCallback(false, modalContent);
                datatable.redraw();
            });

            alreadyReloaded = true;
        }

      });
  
    };

    return {
        // public functions
        init: function() {
            initRecords();
            initRooms();
            singleRowDelete();
            selectEmployee();
            // initDatatableModal3();
        },
    };
}();

jQuery(document).ready(function() {
    IndexRecord.init();
});
