// Class definition
var ShowCalendar = function() {
    // Private variables
    var tagifyDepa;
    var tagifyRestant;
    var tableCalendar = $('#tableCalendar');
    var modalRoomsDepa = $('#modalRoomsDepa');
    var modalRoomsRestant = $('#modalRoomsRestant');
    var modalExtraDepa = $('select[name="modalExtraDepa"]');
    var modalExtraRestant = $('select[name="modalExtraRestant"]');
    var btnModalAddDepa = $('#btnModalAddDepa');
    var btnModalAddRestant = $('#btnModalAddRestant');
    var btnRemoveAllRoomsDepa = $('#generatedRoomsDepa_remove');
    var btnRemoveAllRoomsRestant = $('#generatedRoomsRestant_remove');
    var modalDay = $('#calendarDay');
    var modalForm = $('#modalForm');
    var btnModalGo = $('#btnModalGo');
    var inputSearchDepa = $('#calendar_room_depa_search');
    var inputSearchRestant = $('#calendar_room_restant_search');
    var plan_data = $('.plan_data');

    var selectedDate, selectedEmployee, selectedClean_type;

    // Private functions
    var initCalendar = function() {

      KTApp.block(tableCalendar, {
          overlayColor: '#000000',
          state: 'danger',
          message: 'Please wait...'
      });

      $('body').tooltip({
          selector: '.tooltiped'
      });

      tableCalendar.find('td').removeClass('bg-success');
      
      $.ajax({
          // url: formEl.attr('action'),
          url: "/calendars/" + deviceId + "/load",
          type: "POST",
          cache: false,
          datatype: 'JSON',
          data: {
            "date" : calendarDate,
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response, status, xhr, $form) {
            tableCalendar.find('td.plan_data a').addClass('empty').html(0);
            response.forEach((employee) => {
              employee.calendars.forEach((calendar) => {
                var depa = 0;
                var restant = 0;
                calendar.rooms.forEach((room, i) => {
                  if ( room.pivot.clean_type == 0 ) {
                    depa++;
                  } else if ( room.pivot.clean_type == 1 ) {
                    restant++;
                  }
                });
                var depaElem    = $('td[data-date="'+calendar.date+'"][data-employee="'+calendar.employee_id+'"][data-type="0"]');
                var restantElem = $('td[data-date="'+calendar.date+'"][data-employee="'+calendar.employee_id+'"][data-type="1"]');
                if (depa > 0) {
                  depaElem.addClass('bg-success')
                  depaElem.find('a').removeClass('empty');
                }
                if (restant > 0) {
                  restantElem.addClass('bg-success')
                  restantElem.find('a').removeClass('empty');
                }
                depaElem.find('a').html(depa);
                restantElem.find('a').html(restant);

              });

            });

            KTApp.unblock(tableCalendar);

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
              KTApp.unblock(tableCalendar);
          }
      });
    }

    var openCalendar = function() {

      modalDay.on('show.bs.modal', function (event) {
        // $('.modalRooms').perfectScrollbar({
        //   suppressScrollY: true,
        //   theme: 'dark',
        //   handlers: ['click-rail', 'drag-scrollbar', 'wheel', 'touch'],
        //   wheelPropagation: true,
        //   useBothWheelAxes: true,
        // });

        setTimeout( function() {
          KTApp.block(modalForm, {
              overlayColor: '#000000',
              state: 'danger',
              message: 'Please wait...'
          });
        }, 300);

        tagifyDepa.removeAllTags();
        tagifyRestant.removeAllTags();
        var modal = $(this);
        var button = $(event.relatedTarget) // Button that triggered the modal
        var td = button.parent('td');
        selectedDate = td.data('date');
        selectedEmployee = td.data('employee');
        selectedClean_type = td.data('type');

        $.ajax({
            url: "/calendars/" + deviceId + "/loadSingleDay",
            type: "GET",
            cache: false,
            datatype: 'JSON',
            data: {
              "date" : selectedDate,
              "employee" : selectedEmployee,
              // "clean_type" : selectedClean_type,
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response, status, xhr, $form) {
              // console.log(response.calendar);
              // modal.find('#modalCleanType').html( constants.clean_types[selectedClean_type] );
              if ( selectedClean_type == 0 ) {
                $('#navi_depa').trigger("click").change();
              } else {
                $('#navi_restant').trigger("click").change();
              }
              modal.find('#modalCleanType').html( constants.clean_types[selectedClean_type] );
              modal.find('#modalEmployee').html( response.employee.fullname );
              modal.find('#modalDate').html( selectedDate );

              response.calendar.forEach((calendar) => {
                calendar.rooms.filter(r => r.pivot.clean_type == 0).forEach((room) => {
                  tagifyDepa.addTags([
                    {
                      value: room.name,
                      room_id: room.id,
                      extra: room.pivot.extra,
                      color: room.pivot.extra
                    }
                  ]);
                });
                calendar.rooms.filter(r => r.pivot.clean_type == 1).forEach((room) => {
                  tagifyRestant.addTags([
                    {
                      value: room.name,
                      room_id: room.id,
                      extra: room.pivot.extra,
                      color: room.pivot.extra
                    }
                  ]);
                });
              });


              let fresh_select = '';
              modalRoomsDepa.empty();
              modalRoomsRestant.empty();

              // same rooms, same day, different employee
              $.each(response.rooms, function(i, category) {
                fresh_select += '<label>'+ constants.room_categories[i] +'</label>';
                fresh_select += '<div class="checkbox-inline">';
                // fresh_select += '<optgroup label="'+ constants.room_categories[i] +'">';
                $.each(category, function(i, room) {
                    // fresh_select += '<option value="' + room.value + '">' + room.text + '</option>';
                    let text_color = '';
                    if (room.calendars.length) {
                      text_color = 'text-gray-400'
                      let employees = [];
                      room.calendars.forEach(calendar => {
                        employees.push(calendar.employee.fullname)
                      });
                      fresh_select += `<label class="checkbox tooltiped" data-toggle="tooltip" data-html="true" title="${Lang.get('script.room_assigned')}: <strong>${employees.join(', ')}</strong>." >`;
                    } else {
                      fresh_select += '<label class="checkbox">'; 
                    }
                    fresh_select +=   '<input type="checkbox" name="' + room.id + '" data-text="'+ room.name +'" />';
                    fresh_select +=   '<span></span><div class="'+ text_color +'">' + room.name + '</div>';
                    fresh_select +=  '</label>';
                });
                // fresh_select += '</optgroup>'; 
                fresh_select += '</div>';
              });


              modalRoomsDepa.append(fresh_select);
              modalRoomsRestant.append(fresh_select);

              // Depa-Restant make readonly
              setTimeout( function() {
                response.calendar.forEach((calendar) => {
                  calendar.rooms.filter(r => r.pivot.clean_type == 0).forEach((room) => {
                    console.log( $(`#modalRoomsDepa input[name="${room.id}"]`) );
                  });
                  calendar.rooms.filter(r => r.pivot.clean_type == 1).forEach((room) => {
                    //
                  });
                });
              }, 2000);

              setTimeout( function() {
                KTApp.unblock(modalForm);
              }, 300);

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

                KTApp.unblock(modalForm);
            }
        });

      });

      modalDay.on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('#modalEmployee').html( Lang.get('script.loading') );
        modal.find('#modalDate').html( Lang.get('script.loading') );
        inputSearchDepa.val('').trigger('change');
        inputSearchRestant.val('').trigger('change');
      });
    }

    var generatedRooms = function() {

      tagifyDepa = new Tagify(document.querySelector('#generatedRoomsDepa'), {
          delimiters : null,
          // duplicates: true,
          editTags: false,
          transformTag: transformTag,
      });

      tagifyRestant = new Tagify(document.querySelector('#generatedRoomsRestant'), {
          delimiters : null,
          // duplicates: true,
          editTags: false,
          transformTag: transformTag,
      });

      function transformTag(tagData) {
          tagData.class = 'tagify__tag tagify__tag-light--' + constants.colors[ tagData.color ];
      }

      tagifyDepa.on('add', onAddDepaTag)
          .on('remove', onDepaRemoveTag)
          .on('click', onDepaTagClick);

      tagifyRestant.on('add', onAddRestantTag)
          .on('remove', onRestantRemoveTag)
          .on('click', onRestantTagClick);

      function onAddDepaTag(e) {
        // console.log( "original Input:", tagifyDepa.DOM.originalInput);
        // console.log( "original Input's value:", tagifyDepa.DOM.originalInput.value);
        // console.log( "event detail:", e.detail);
        let chbox = modalRoomsRestant.find( `input[name="${e.detail.data.room_id}"]` );
        let lbl = chbox.parent('.checkbox');
        let options = {
          html: true,
          title: `${Lang.get('script.room_assigned')}: <strong>Depa</strong>`,
          // trigger: 'hover focus',
        }
        lbl.tooltip(options);
        lbl.addClass('text-muted');
        chbox.prop('disabled', true);
      }

      function onDepaRemoveTag(e) {
        let chbox = modalRoomsRestant.find( `input[name="${e.detail.data.room_id}"]` );
        let lbl = chbox.parent('.checkbox');
        lbl.tooltip('dispose');
        lbl.removeClass('text-muted');
        chbox.prop('disabled', false);
      }

      function onDepaTagClick(e) {

      }

      function onAddRestantTag(e) {
        let chbox = modalRoomsDepa.find( `input[name="${e.detail.data.room_id}"]` );
        let lbl = chbox.parent('.checkbox');
        let options = {
          html: true,
          title: `${Lang.get('script.room_assigned')}: <strong>Restant</strong>`,
          // trigger: 'hover focus',
        }
        lbl.tooltip(options);
        lbl.addClass('text-muted');
        chbox.prop('disabled', true);
      }

      function onRestantRemoveTag(e) {
        let chbox = modalRoomsDepa.find( `input[name="${e.detail.data.room_id}"]` );
        let lbl = chbox.parent('.checkbox');
        lbl.tooltip('dispose');
        lbl.removeClass('text-muted');
        chbox.prop('disabled', false);
      }

      function onRestantTagClick(e) {

      }

      // "remove all tags" button event listener
      btnRemoveAllRoomsDepa.on('click', function() {
        confirm(Lang.get('script.are_you_sure')) ? tagifyDepa.removeAllTags() : '';
      });
      btnRemoveAllRoomsRestant.on('click', function() {
        confirm(Lang.get('script.are_you_sure')) ? tagifyRestant.removeAllTags() : '';
      });

    }

    var addDayPlan = function() {
      btnModalAddDepa.on('click', function(e) {
        modalRoomsDepa.find('input:checked').each(function() {
            var checkbox = $(this);
            checkbox.prop('checked', false);
            tagifyDepa.addTags([
              {
                value: checkbox.data('text'),
                room_id: checkbox.attr('name'),
                // room_cat: manualCategory.val(),
                extra: modalExtraDepa.val(),
                color: modalExtraDepa.val(),
              }
            ]);
        });
      });
      btnModalAddRestant.on('click', function(e) {
        modalRoomsRestant.find('input:checked').each(function() {
            var checkbox = $(this);
            checkbox.prop('checked', false);
            tagifyRestant.addTags([
              {
                value: checkbox.data('text'),
                room_id: checkbox.attr('name'),
                // room_cat: manualCategory.val(),
                extra: modalExtraRestant.val(),
                color: modalExtraRestant.val(),
              }
            ]);
        });
      });
    }

    var updateDayPlan = function() {
      btnModalGo.on('click', function(e) {
        e.preventDefault();
        KTApp.block(modalForm, {overlayColor: '#000000', state: 'danger', message: 'Please wait...'});
        $.ajax({
            type: 'PATCH',
            url: modalForm.attr('action'),
            // data: modalForm.serialize(),
            data: {
              'date': selectedDate,
              'employee': selectedEmployee,
              // 'clean_type': selectedClean_type,
              'rooms_depa': tagifyDepa.DOM.originalInput.value,
              'rooms_restant': tagifyRestant.DOM.originalInput.value
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response, status, xhr) {
                KTApp.unblock(modalForm);
                modalDay.modal('toggle');
                initCalendar();
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
    }

    var searchRooms = function() {

      inputSearchDepa.on('input',function(e){
        let search = $(this).val();

        $.each( modalRoomsDepa.find('input'), function(i, input) {

          if ( $(input).data('text').toString().toUpperCase().match(search.toUpperCase()) )
            $(input).parent('label').removeClass('hidden')
          else
            $(input).parent('label').addClass('hidden')

        });
      });
      
      inputSearchRestant.on('input',function(e){
        let search = $(this).val();

        $.each( modalRoomsRestant.find('input'), function(i, input) {

          if ( $(input).data('text').toString().toUpperCase().match(search.toUpperCase()) )
            $(input).parent('label').removeClass('hidden')
          else
            $(input).parent('label').addClass('hidden')

        });
      });

    }

    var _initEntries = function () {
      KTApp.block(tableCalendar, {
        overlayColor: '#000000',
        state: 'danger',
        message: Lang.get('script.please_wait')
      });
      let startTime = performance.now()
  
      $.each(plan_data, function() {
        let td = $(this);
        if (td.hasClass('out_of_entry')) {
          td.tooltip({
            title: Lang.get('script.out_of_entry'),
            html: true,
          });
        }
      });
  
      let endTime = performance.now()
      console.log(`Adding tooltips to out of entry symbols took ${endTime - startTime} milliseconds`);
      // in seconds
      console.log(`Adding tooltips to out of entry symbols took ${(endTime - startTime) / 1000} seconds`);
      KTApp.unblock(tableCalendar);
    }

    return {
        // public functions
        init: function() {
            initCalendar();
            openCalendar();
            generatedRooms();
            addDayPlan();
            updateDayPlan();
            searchRooms();
            _initEntries();
        }
    };
}();

jQuery(document).ready(function() {
    ShowCalendar.init();
});
