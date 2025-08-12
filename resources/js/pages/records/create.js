// Class definition
var CreateRecord = function() {
    //
    var tagify_depa = tagify_restant = null;
    var device = $('#device_input');
    var employee = $('#employee_input');
    var record_datetime = $('#record_datetime');
    var select_depa_rooms = $('#recordDepaRooms');
    var select_restant_rooms = $('#recordRestantRooms');
    var btnDepaAdd = $('#btnDepaAdd');
    var btnRestantAdd = $('#btnRestantAdd');
    var selectDepaExtra = $('#depaExtra');
    var selectRestantExtra = $('#restantExtra');
    var btnDepa_remove = $('#depaRooms_remove');
    var btnRestant_remove = $('#depaRestant_remove');

    var selectDepaStatus = $('#depaStatus');
    var selectRestantStatus = $('#restantStatus');
    var volunteers_depa = $('#volunteers_depa_input');
    var volunteers_restant = $('#volunteers_restant_input');

    var generatedDepa = $('#depaRooms');
    var generatedRestant = $('#restantRooms');

    var current_datetime = moment(record_datetime.find('input').val());

    // Private functions
    var inputs = function() {

      $('#device_input', '#action_input', '#perform_input', '#identity_input').select2({
          placeholder: Lang.get('script.select_device')
      });

      if (
          $('#action_input').find("option:selected").val() != 1 || //Checkout
          device.find("option:selected").val() == 4 //Buro
      ) {
        $('.row_rooms').hide();
        if (tagify_depa !== null || tagify_restant !== null) {
          tagify_depa.removeAllTags();
          tagify_restant.removeAllTags();
        }
      }

      device.on('change', function(e){
        if (
          $('#action_input').find("option:selected").val() != 1 || //Checkout
          device.find("option:selected").val() == 4 //Buro
        ) {
          $('.row_rooms').hide();
          $('#perform_input').val(6).change();
          if (tagify_depa !== null || tagify_restant !== null) {
            tagify_depa.removeAllTags();
            tagify_restant.removeAllTags();
          }
        } else {
          $('.row_rooms').show();
          rooms();
        }
      });

      $('#action_input').on('change', function(e) {
        if ( device.find("option:selected").val() == 4 ) {
          return;
        }
        if ( $(this).find("option:selected").val() != 1 ) { //Checkout
          $('.row_rooms').hide();
          if (tagify_depa !== null || tagify_restant !== null) {
            tagify_depa.removeAllTags();
            tagify_restant.removeAllTags();
          }
        } else {
          $('.row_rooms').show();
          rooms();
        }
      });

      btnDepaAdd.on('click', function(e) {
        tagify_depa.addTags([
          {
            value: select_depa_rooms.find('option:selected').text(),
            room_id: select_depa_rooms.val(),
            extra: selectDepaExtra.val(),
            color: selectDepaExtra.val(),
            status: selectDepaStatus.val(),
            volunteer: volunteers_depa.find(':selected').val(),
            title: get_status({
              'status': selectDepaStatus.val(),
              'volunteer': volunteers_restant.find(':selected').val(),
              'volunteer_name': volunteers_restant.select2('data')[0] ? volunteers_restant.select2('data')[0].fullname : ''
            }),
          }
        ]);
      })

      btnRestantAdd.on('click', function(e) {
        tagify_restant.addTags([
          {
            value: select_restant_rooms.find('option:selected').text(),
            room_id: select_restant_rooms.val(),
            extra: selectRestantExtra.val(),
            color: selectRestantExtra.val(),
            status: selectRestantStatus.val(),
            volunteer: volunteers_restant.find(':selected').val(),
            title: get_status({
              'status': selectRestantStatus.val(),
              'volunteer': volunteers_restant.find(':selected').val(),
              'volunteer_name': volunteers_restant.select2('data')[0] ? volunteers_restant.select2('data')[0].fullname : ''
            }),
          }
        ]);
      })

    }

    var employees = function() {

      // loading remote data

      function formatEmployee(employee) {
          if (employee.loading) return employee.id;

          var states = ['success', 'info', 'primary', 'warning', 'danger'];
          var state = (typeof states[employee.function] === 'undefined') ? states[4] : states[employee.function];
          var e_function = employee.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

          var markup = `<div class="d-flex align-items-center">
            <div class="symbol symbol-40 symbol-${state} flex-shrink-0">
              <div class="symbol-label">${employee.fullname.substring(0, 1)}</div>
            </div>
            <div class="ml-2">
              <div class="text-dark-75 font-weight-bold line-height-sm">${employee.fullname}</div>
              <span class="font-size-sm text-dark-50 text-hover-primary">${e_function}</span>
            </div>
          </div>`;

          return markup;
      }

      function formatEmployeeSelection(employee) {
          return employee.fullname || employee.text;
      }

      employee.select2({
          placeholder: Lang.get('script.search_employees'),
          allowClear: true,
          ajax: {
              url: "/records/employees",
              dataType: 'json',
              delay: 250,
              data: function(params) {
                  return {
                      query: params.term, // search term
                      page: params.page
                  };
              },
              processResults: function(data, params) {
                  // parse the results into the format expected by Select2
                  // since we are using custom formatting functions we do not need to
                  // alter the remote JSON data, except to indicate that infinite
                  // scrolling can be used
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
          templateResult: formatEmployee, // omitted for brevity, see the source of this page
          templateSelection: formatEmployeeSelection // omitted for brevity, see the source of this page
      });
      employee.trigger("change");

      employee.on("change", function (e) {
        if ( typeof current_record === 'undefined' ) {
          rooms();
        }
      });

    }

    var datetime = function() {

      record_datetime.datetimepicker({
        locale: Lang.locale,
        format: 'DD.MM.YYYY HH:mm:ss',
        defaultDate: moment(),
      });
      current_datetime = record_datetime.data('datetimepicker').date();

      record_datetime.on('change.datetimepicker', function (e) {
        // window.location = `/${Lang.locale}/daily_reports/${deviceId}?date=${e.date.format('DD.MM.YYYY')}`;
        // console.log(e.date.format('DD.MM.YYYY'));
        if ( current_datetime.format('L') === record_datetime.data('datetimepicker').date().format('L') ) {
          // if same day, do not query rooms
          current_datetime = record_datetime.data('datetimepicker').date();
        } else {
          current_datetime = record_datetime.data('datetimepicker').date();
          rooms();
        }
      });

    }

    var rooms = function() {
      
      if (tagify_depa !== null || tagify_restant !== null) {
        tagify_depa.removeAllTags();
        tagify_restant.removeAllTags();
        tagify_depa.destroy();
        tagify_restant.destroy();
        // tagify_depa = null;
        // tagify_restant = null;
      }

      tagify_depa = new Tagify(document.querySelector('#depaRooms'), {
          delimiters : null,
          // duplicates: true,
          editTags: false,
          transformTag: transformTag,
      });

      tagify_restant = new Tagify(document.querySelector('#restantRooms'), {
          delimiters : null,
          // duplicates: true,
          editTags: false,
          transformTag: transformTag,
      });

      function transformTag(tagData) {
          tagData.class = `tagify__tag tagify__tag-light--${constants.colors[tagData.color]} status-${tagData.status}`;
      }

      btnDepa_remove.on('click', function() {
        confirm(Lang.get('script.are_you_sure')) ? tagify_depa.removeAllTags() : '';
      });
      btnRestant_remove.on('click', function() {
        confirm(Lang.get('script.are_you_sure')) ? tagify_restant.removeAllTags() : '';
      });

      $.ajax({
          url: typeof current_record !== 'undefined' ? `/records/rooms/${current_record.id}` : '/records/rooms' ,
          type: "POST",
          cache: false,
          datatype: 'JSON',
          data: {
            // "device" : device.find("option:selected").val(),
            // "date" : record_datetime.data('datetimepicker').date(),
            "date" : current_datetime.format('YYYY-MM-DD'),
            "calendar" : true,
            "employee" : employee.find(':selected').val(),
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response, status, xhr, $form) {
            let fresh_select_depa = '';
            let fresh_select_restant = '';
            select_depa_rooms.empty();
            select_restant_rooms.empty();
            console.log(response);

            $.each(response, function(i, category) {
              fresh_select_depa += '<optgroup label="'+ constants.room_categories[i] +'">';
              fresh_select_restant += '<optgroup label="'+ constants.room_categories[i] +'">';
              $.each(category, function(i, room) {
                if (room.pivot.clean_type == 0) {
                  fresh_select_depa += '<option value="' + room.id + '">' + room.name + '</option>';
                } else {
                  fresh_select_restant += '<option value="' + room.id + '">' + room.name + '</option>';
                }
              });
              fresh_select_depa += '</optgroup>';
              fresh_select_restant += '</optgroup>';
            });

            select_depa_rooms.append(fresh_select_depa);
            select_restant_rooms.append(fresh_select_restant);
            select_depa_rooms.selectpicker('refresh');
            select_restant_rooms.selectpicker('refresh');

            // if (tagify_depa !== null) {
            //   tagify_depa.destroy();
            //   tagify_restant.destroy();
            //   tagify_depa = null;
            //   tagify_restant = null;
            // }


          },
          error: function (response)
          {
              var e = '<b>' + String(response.responseJSON.message) + '</b><br>';
              for (var err in response.responseJSON.errors) {
                if (response.responseJSON.errors.hasOwnProperty(err)) {
                  e += response.responseJSON.errors[err] + '<br>';
                }
              }
              console.log(e);
          }
      });

    }

    var populateEmployee = function() {

      if (typeof currentEmployee !== 'undefined') {
          // Fetch the preselected item, and add to the control
          $.ajax({
              url: '/records/employees',
              type: "POST",
              cache: false,
              datatype: 'JSON',
              data: {
                "single_employee" : currentEmployee,
              },
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
          }).then(function (data) {
              // create the option and append to Select2
              var option = new Option(data.fullname, data.id, true, true);
              employee.append(option).trigger('change');
              // manually trigger the `select2:select` event
              employee.trigger({
                  type: 'select2:select',
                  params: {
                      data: data
                  }
              });
          });
      }

    }

    var populateRooms = function() {
      if (typeof currentDepaRooms !== 'undefined' && typeof currentRestantRooms !== 'undefined') {
        $.each(currentDepaRooms, function(i, room) {
          tagify_depa.addTags([
            {
              // value: room.name + ` (${constants.room_statuses[room.pivot.status].charAt(0)})`,
              value: room.name,
              room_id: room.id,
              clean_type: room.pivot.clean_type,
              extra: room.pivot.extra,
              status: room.pivot.status,
              color: room.pivot.extra,
              volunteer: room.pivot.volunteer,
              title: get_status(room.pivot),
            }
          ]);
        })
        $.each(currentRestantRooms, function(i, room) {
          tagify_restant.addTags([
            {
              // value: room.name + ` (${constants.room_statuses[room.pivot.status].charAt(0)})`,
              value: room.name,
              room_id: room.id,
              clean_type: room.pivot.clean_type,
              extra: room.pivot.extra,
              status: room.pivot.status,
              color: room.pivot.extra,
              volunteer: room.pivot.volunteer,
              title: get_status(room.pivot),
            }
          ]);
        })
      }
    }

    var get_status = function(pivot) {
      let status = `${Lang.get('script.status')}: ${constants.room_statuses[pivot.status]}`;
      if (pivot.volunteer && pivot.status == 3) {
        status += `\n${Lang.get('script.volunteer')}: ${pivot.volunteer_name}`;
      }
      return status;
    }

    var _volunteers = function() {

      function formatEmployee(employee) {
          if (employee.loading) return employee.id;

          var states = ['success', 'info', 'primary', 'warning', 'danger'];
          var state = (typeof states[employee.function] === 'undefined') ? states[4] : states[employee.function];
          var e_function = employee.function == 0 ? Lang.get('script.Manager') : Lang.get('script.Employee');

          var markup = `<div class="d-flex align-items-center">\
            <div class="symbol symbol-40 symbol-${state} flex-shrink-0">\
              <div class="symbol-label">${employee.fullname.substring(0, 1)}</div>\
            </div>\
            <div class="ml-2">\
              <div class="text-dark-75 font-weight-bold line-height-sm">${employee.fullname}</div>\
              <span class="font-size-sm text-dark-50 text-hover-primary">${e_function}</span>\
            </div>\
          </div>`;

          return markup;
      }

      function formatEmployeeSelection(employee) {
          return employee.fullname || employee.text;
      }

      volunteers_depa.select2({
          placeholder: Lang.get('script.search_employees'),
          allowClear: true,
          ajax: {
              url: "/records/employees",
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
          templateResult: formatEmployee, // omitted for brevity, see the source of this page
          templateSelection: formatEmployeeSelection // omitted for brevity, see the source of this page
      });

      volunteers_restant.select2({
          placeholder: Lang.get('script.search_employees'),
          allowClear: true,
          ajax: {
              url: "/records/employees",
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
          templateResult: formatEmployee, // omitted for brevity, see the source of this page
          templateSelection: formatEmployeeSelection // omitted for brevity, see the source of this page
      });
      volunteers_restant.trigger("change");

    }

    return {
        // public functions
        init: function() {
            inputs();
            employees();
            datetime();
            rooms();

            populateEmployee();
            populateRooms();
            _volunteers();
        }
    };
}();

jQuery(document).ready(function() {
    CreateRecord.init();
});
