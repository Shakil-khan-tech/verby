"use strict";

var KTCalendarExternalEvents = function() {
    // Public functions
    let records;
    var calendarEl = document.getElementById('kt_calendar');
    var containerEl = document.getElementById('kt_calendar_external_events');
    var calendar;
    var externalEventIds = [];
    var current_event;

    var volunteers_depa = $('#volunteers_depa_input');
    var volunteers_restant = $('#volunteers_restant_input');
    var record_datetime = $('#record_datetime');
    var device_input = $('#device_input');
    var action_input = $('#action_input');
    var perform_input = $('#perform_input');
    var row_rooms = $('.row_rooms');
    var eventTitle = $('#eventTitle');

    var tagify_depa = null;
    var tagify_restant = null;
    var select_depa_rooms = $('#recordDepaRooms');
    var select_restant_rooms = $('#recordRestantRooms');
    var btnDepaAdd = $('#btnDepaAdd');
    var btnRestantAdd = $('#btnRestantAdd');
    var selectDepaExtra = $('#depaExtra');
    var selectRestantExtra = $('#restantExtra');
    var btnDepa_remove = $('#depaRooms_remove');
    var btnRestant_remove = $('#restantRooms_remove');
    var formModalSubmit = $('#formRecordCalendar');
    var btnModalSubmit = $('#btnRecordCalendar');
    var btnColseModal = $('#btnColseModal');

    var selectDepaStatus = $('#depaStatus');
    var selectRestantStatus = $('#restantStatus');
    var volunteers_depa = $('#volunteers_depa_input');
    var volunteers_restant = $('#volunteers_restant_input');

    var btnPrint = $('#btnCalendarRecordPrint');

    var timeObj = {};

    var total_seconds_monthly = [];

    // Private functions
    var initExternalEvents = function() {
        $('#kt_calendar_external_events .fc-draggable-handle').each(function() {
          // store data so the calendar knows to render an event upon drop
          $(this).data('event', {
              title: $.trim($(this).text()), // use the element's text as the event title
              stick: true, // maintain when user navigates (see docs on the renderEvent method)
              classNames: [$(this).data('color')],
              startTime: $(this).data('hour'),
              extendedProps: {
                description: Lang.get('script.new_record'),
                hour: $(this).data('hour'),
                action: $(this).data('action'),
                device: $(this).data('device'),
                perform: $(this).data('perform'),
                identity: $(this).data('identity'),
              },
          });
        });
    }

    var initCalendar = function() {
        var todayDate = moment().startOf('day');
        var TODAY = todayDate.format('YYYY-MM-DD');

        var Draggable = FullCalendarInteraction.Draggable;

        new Draggable(containerEl, {
            itemSelector: '.fc-draggable-handle',
            eventData: function(eventEl) {
                $(eventEl).data('event').id = generateRandom();
                return $(eventEl).data('event');
            }
        });

        calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
            eventTimeFormat: { // like '14:30:00'
              hour: '2-digit',
              minute: '2-digit',
              // second: '2-digit',
              meridiem: false,
              hour12: false
            },
            views: {
              timelineDay: {
                slotLabelFormat: ['HH:mm'],
              },
              timelineMonth: {
                slotLabelFormat: ['DD'],
              },
            },
            nextDayThreshold: '24:00:00',
            // locale: 'en-GB',
            locale: Lang.locale,
            buttonText: {
              today: Lang.get('script.today'),
              month: Lang.get('script.month'),
              week: Lang.get('script.week'),
              day: Lang.get('script.day'),
            },
            // plugins: [ ],
            // timeZone: 'Europe/London',
            // timeZone: 'Europe/Zurich',
            // timeZone: 'UTC',
            // timeZone: 'local',
            // timeZone: 'GMT',
            initialDate: moment().format('YYYY-MM-DD'),

            header: {
                left: 'prev,next today',
                center: 'title',
                // right: 'dayGridMonth,timeGridWeek,timeGridDay'
                right: ''
            },
            // titleFormat: (info) => `Year: ${info.date.year}  Month: ${info.date.month+1}`,
            // titleFormat: (info) => console.log(info),

            height: 800,
            contentHeight: 780,
            aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio
            firstDay: 1,

            nowIndicator: true,
            now: new Date(),

            views: {
                dayGridMonth: { buttonText: 'month' },
                timeGridWeek: { buttonText: 'week' },
                timeGridDay: { buttonText: 'day' },
                timeGrid: {
                    eventLimit: 2 // adjust to 6 only for timeGridWeek/timeGridDay
                }
            },

            defaultView: 'dayGridMonth',
            defaultDate: TODAY,

            droppable: true, // this allows things to be dropped onto the calendar
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            navLinks: true,
            eventOrder: 'start',
            events: function(info, successCallback, failureCallback) {
                $.ajax({
                    url: `/records/calendar/${current_employee.id}/ajax`,
                    type: "POST",
                    cache: false,
                    datatype: 'JSON',
                    data: {
                      "start": info.start.valueOf(),
                      "end": info.end.valueOf(),
                    },
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response, status, xhr, $form) {
                      
                      console.log(response.seconds);
                      records = response.records;
                      timeObj = {}; //reset
                      total_seconds_monthly = []; //reset

                      successCallback(                       
                        $.map( records, function( record ) {
                          /* timearray start */
                          if (moment(record.time).format('YYYY-MM-DD') in timeObj) {
                            timeObj[ moment(record.time).format('YYYY-MM-DD') ].push( {'time' : moment(record.time), 'action' : record.action} );
                          } else {
                            timeObj[ moment(record.time).format('YYYY-MM-DD') ] = [ {'time' : moment(record.time), 'action' : record.action} ];
                          }
                          /* timearray end */

                          return {
                            id: record.id,
                            title: constants.actions[record.action],
                            start: record.time,
                            // start: moment.tz(record.time, "Europe/Zurich").format('YYYY-MM-DD HH:mm:ss'),
                            end: null,
                            // description: record.device.name,
                            className: eventColors(record.action),
                            editable: false,
                            extendedProps: {
                              description: record.device.name,
                              // startTime: moment(record.time).format('HH:mm:ss'),
                              device: record.device.id,
                              action: record.action,
                              perform: record.perform,
                              identity: record.identity,
                            },
                          }
                        }),
                        
                      );

                      $('.totalday').remove(); //remove all totals per day

                      for (const date of Object.keys( response.seconds )) {
                        let month = moment(date).format('MM');
                        if ( !(month in total_seconds_monthly) ) {
                          total_seconds_monthly[month] = [];
                        }
                        console.log(response.seconds[date]);
                        total_seconds_monthly[month].push( response.seconds[date] );

                        if ( response.seconds[date] == 0 ) {
                          continue;
                        }
                        //seconds to hours
                        var hours = (response.seconds[date] / 3600).toFixed(2);
                        var day_top = $('.fc-day-top[data-date="'+ date +'"]');
                        day_top.append(
                          `<div class="totalday md:block">
                            <span class="label label-sm label-inline label-pill mt-1 py-0 px-1">
                              <span class="hidden sm:hidden md:block">${Lang.get('script.total')}:</span>
                              <b>${hours}h</b>
                            </span>
                          </div>`
                        );
                      }

                      setDayBackgrounds(records);
                      setDayPlans(response.plans);
                      setTotalMonth();
                      // KTApp.unblock('.fc-view-container');
                      
                    },
                    error: function (response)
                    {
                        failureCallback(response);
                    }
                });
            },
            loading: function( isLoading, view ) {
              if(isLoading) {
                KTApp.block('.fc-view-container');
              } else {
                KTApp.unblock('.fc-view-container');
              }
            },
            eventClick: function(info) {
              if ( info.jsEvent.srcElement.classList.contains('la-trash') ) {
                return;
              }
              eventModal(info.event);
              console.log( info.event );
            },
            drop: function(arg) {
                // is the "remove after drop" checkbox checked?
                // if ($('#kt_calendar_external_events_remove').is(':checked')) {
                //     // if so, remove the element from the "Draggable Events" list
                //     $(arg.draggedEl).remove();
                // }
            },
            eventReceive: function(info) {
                // called when a proper external event is dropped
                if ( checkReceivedEvent(info.event) ) {
                  eventModal(info.event);
                } else {
                  info.event.remove();
                  var content = {};
                  content.title = Lang.get('script.not_allowed');
                  content.message = '';
                  var notify = $.notify(content, {
                    type: 'danger',
                    mouse_over:  true,
                    z_index: 1051,
                  });
                }
            },

            datesRender: function(info) {
              if ( calendar_time != 'null' ) {
                calendar.gotoDate( calendar_time );
              }
            },
            dayRender: function (date, cell) {
              //
            },
            eventRender: function(info, element) {
                var element = $(info.el);

                element.find('.fc-content').append(`
                <a href="#!" class="deleteEvent" data-id="${info.event.id}">
                  <span class="navi-icon">
                      <i class="la la-trash hover:text-red-400"></i>
                  </span>
                </a>
                `);

                if (info.event.extendedProps && info.event.extendedProps.description) {
                    if (element.hasClass('fc-day-grid-event')) {
                      element.data('placement', 'right');
                      element.data('html', true);
                      element.data('title', Lang.get('script.record_summary'));
                      var content = `
                        <div class="font-bold font-size-h6 text-center mb-2">${info.event.title}</div>
                        <div class="text-sm text-success">
                          <span class="font-bold">${Lang.get('script.time')}:</span> ${moment(info.event.start).format('HH:mm')}
                        </div>
                        <div class="text-sm">
                          <span class="font-bold">${Lang.get('script.device')}:</span> ${info.event.extendedProps.description}
                        </div>
                        <div class="text-sm">
                          <span class="font-bold">${Lang.get('script.action')}:</span> ${constants.actions[info.event.extendedProps.action]}
                        </div>
                        <div class="text-sm">
                          <span class="font-bold">${Lang.get('script.perform')}:</span> ${constants.performs[info.event.extendedProps.perform]}
                        </div>
                        <div class="text-sm">
                          <span class="font-bold">${Lang.get('script.identity')}:</span> ${constants.identities[info.event.extendedProps.identity]}
                        </div>
                      `;
                      if ( info.event.extendedProps.action == 1  ) {
                        let record = records.find(r => r.id == info.event.id);
                        if (record) {
                          content += `
                            <div class="separator separator-dashed my-1"></div>
                            <div class="text-sm">
                              <span class="font-bold">${Lang.get('script.depa')}:</span> ${ Object.values(record.rooms).filter(r => r.pivot.clean_type == 0).length }
                            </div>
                            <div class="text-sm">
                              <span class="font-bold">${Lang.get('script.restant')}:</span> ${ Object.values(record.rooms).filter(r => r.pivot.clean_type == 1).length }
                            </div>
                          `;
                        }
                      }
                      // element.data('content', info.event.extendedProps.description);
                      element.data('content', content);
                      KTApp.initPopover(element);
                    }
                }
            },

            eventPositioned: function(info) {
              //
            }
        });

        calendar.render();
    }

    var setDayBackgrounds = function(raw_records) {
      const grouped = _.groupBy(raw_records, function(record){
        return moment(record.time).format("YYYY-MM-DD");
      });
      let blocks = [];
      let day_errors = [];

      //mark as errors days that have two conecutive same actions
      _.each(grouped, function (records, day) {
        let {ch_open, ch_close, p_open, p_close} = false;
        blocks[day] = [];
        day_errors[day] = false;
        _.each(records, function (record) {
          blocks[day].push(record.action);
          
          if (record.action==0 && ch_open) { day_errors[day] = true; }
          if (record.action==1 && ch_close) { day_errors[day] = true; }
          if (record.action==2 && p_open) { day_errors[day] = true; }
          if (record.action==3 && p_close) { day_errors[day] = true; }

          ch_open = record.action == 0 ? true : false;
          ch_close = record.action == 1 ? true : false;
          p_open = record.action == 2 ? true : false;
          p_close = record.action == 3 ? true : false;
        });
      });

      let opened = false;
      let previous_day = null;
      Object.entries(blocks).forEach(entry => {
        const [day, actions] = entry;
        //mark as warnings days that dont start and end with checkin & checkout (nightshifts)
        if ( actions[0] != 0 || actions[actions.length-1] != 1 ) {
          $('.fc-day[data-date="'+ day +'"]').css('background', '#ffffbc'); //yellow
        }

        //mark as errors neighbouring days that have two checkins without checkout in between
        Object.entries(actions).forEach(a => {
          const [key, action] = a;
          if (action == 0 && opened) {
            $('.fc-day[data-date="'+ previous_day +'"]').css('background', '#ff5959'); //red
          }
          if (action == 0) { opened = true; }
          if (action == 1) { opened = false; }
        });

        previous_day = day;
      });

      Object.entries(day_errors).forEach(entry => {
        const [day, error] = entry;
        if ( error ) {
          $('.fc-day[data-date="'+ day +'"]').css('background', '#ff5959'); //red
        }
      });
    }

    var checkReceivedEvent = function(event) {
      //Disable checking validity of dropped event with the following line:
      return true;
      
      let events = calendar.getEvents().filter(item => item.id !== event.id);
      let days = [];
      let last_action = null;
      let current_day = moment(event.start).format("YYYY-MM-DD");

      events.forEach(event => {
        let day = moment(event.start).format("YYYY-MM-DD");
        if ( current_day == day ) {
          days.push( event.extendedProps.action );
          last_action = event.extendedProps.action;
        }
      });

      console.log(days);

      if ( event.extendedProps.action == 0 && (last_action == null || days.includes(1)) ) { //event check IN
        if ( (days.filter(a => a == 1).length >= days.filter(a => a == 0).length) || days.length == 0 ) {
          return true;
        }
      }
      if ( event.extendedProps.action == 1 && days.includes(0) ) { //event check OUT
        if ( days.filter(a => a == 0).length > days.filter(a => a == 1).length ) {
          return true;
        }
      }
      if ( event.extendedProps.action == 2 && days.includes(0) ) { //event pause IN
        if ( last_action == 0 || last_action == 3 ) {
          return true;
        }
      }
      if ( event.extendedProps.action == 3 && days.includes(2) ) { //event pause OUT
        if ( days.filter(a => a == 2).length > days.filter(a => a == 3).length ) {
          if ( last_action == 2 ) {
            return true;
          }
        }
      }
      return false;
    }

    var eventColors = function(action) {
        let the_class = '';
        switch ( parseInt(action) ) {
            case 0:
                the_class = "fc-event-primary";
                break;
            case 1:
                the_class = "fc-event-danger";
                break;
            case 2:
                the_class = "fc-event-success";
                break;
            case 3:
                the_class = "fc-event-warning";
                break;

            default:
                break;
        }
        return the_class;
    }

    var eventModal = function(event) {
      current_event = event.id;
      $('#eventModal').modal('show', $(this));
      eventTitle.html( current_employee.fullname );
      record_datetime.timepicker('setTime', moment(event.start).format("HH:mm:ss"));
      $('#record_id').val( event.id );
      device_input.val( event.extendedProps.device ).change();
      action_input.val( event.extendedProps.action ).change();
      perform_input.val( event.extendedProps.perform ).change();
      if ( event.extendedProps.action == 1 ) { //checkout
        let record = records.find(r => r.id == event.id);
        if (record) {
          populateRooms(record);
        }
      }
    }

    var datetime = function() {

      record_datetime.timepicker({
          minuteStep: 1,
          defaultTime: '',
          showSeconds: true,
          showMeridian: false,
          snapToStep: false,
          template: 'dropdown'
      });

    }

    var inputs = function() {

      $('#device_input', '#action_input', '#perform_input').select2({
          placeholder: 'Select'
      });

      if (
          action_input.find("option:selected").val() != 1 || //Checkout
          device_input.find("option:selected").val() == 4 //Buro
      ) {
          row_rooms.hide();
          if (tagify_depa !== null || tagify_restant !== null) {
              tagify_depa.removeAllTags();
              tagify_restant.removeAllTags();
          }
      }

      device_input.on('change', function(e){
        if ( device_input.find("option:selected").val() == 4 ) { //Buro
          perform_input.val(4).change();
        }
        if (
          action_input.find("option:selected").val() != 1 || //Checkout
          device_input.find("option:selected").val() == 4 //Buro
        ) {
          row_rooms.hide();
          if (tagify_depa !== null || tagify_restant !== null) {
            tagify_depa.removeAllTags();
            tagify_restant.removeAllTags();
          }
        } else {
          row_rooms.show();
          rooms();
        }
      });

      action_input.on('change', function(e) {
        if ( device_input.find("option:selected").val() == 4 ) {
          return;
        }
        if ( $(this).find("option:selected").val() != 1 ) { //Checkout
          row_rooms.hide();
          if (tagify_depa !== null || tagify_restant !== null) {
            tagify_depa.removeAllTags();
            tagify_restant.removeAllTags();
          }
        } else {
          row_rooms.show();
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
          // url: `/records/${current_record.id}/rooms`,
          url: typeof current_record !== 'undefined' ? `/records/rooms/${current_record.id}` : '/records/rooms' ,
          type: "POST",
          cache: false,
          datatype: 'JSON',
          data: {
            // "device" : device_input.find("option:selected").val(),
            "date" : moment( calendar.getEventById( current_event ).start ).format('YYYY-MM-DD'),
            "calendar" : true,
            "employee" : current_employee.id,
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

    var _initSubmit = function () {
      btnModalSubmit.on('click', function(e){
        e.preventDefault();
        KTApp.block('.modal-content');

        let _start = calendar.getEventById( current_event ).start;
        let _day = moment(_start).format('YYYY-MM-DD');
        let _time = ('0' + record_datetime.val()).slice(-8); //fix hour zero padding of timepicker

        $.ajax({
          type: 'PATCH',
          url: formModalSubmit.attr('action'),
          data: formModalSubmit.serialize() + "&_day=" + _day + "&_time=" + _time,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response, status, xhr) {
            console.log(response);
            KTApp.unblock('.modal-content');
            calendar.removeAllEvents();
            calendar.refetchEvents();
            $('.fc-day').css('background', 'none');
            var content = {};
            content.title = response.success;
            content.message = '';
            var notify = $.notify(content, {
              type: 'success',
              mouse_over:  true,
              z_index: 1051,
            });
            $('#eventModal').modal('toggle');
            volunteers_depa.val(null).trigger('change');
            volunteers_restant.val(null).trigger('change');
          },
          error: function (response, status, error) {
            KTApp.unblock('.modal-content');
            console.log(response);
            var content = {};
            content.title = response.responseJSON.message;
            content.message = $.map( response.responseJSON.errors, function( error ) {
              return error;
            })
            var notify = $.notify(content, {
              type: 'danger',
              mouse_over:  true,
              z_index: 1051,
            });
          },
        });
      });
    }

    var _closeModal = function () {
      btnColseModal.on('click', function(e){
        if ( parseInt(current_event) < 1 ) { //if event is saved (ie. exists in DB)
          calendar.getEventById( current_event ).remove();
        }
        current_event = null;
        volunteers_depa.val(null).trigger('change');
        volunteers_restant.val(null).trigger('change');
      });
    }

    var generateRandom = function () {
      var rand = Math.random() * -10;
      if ($.inArray(rand, externalEventIds) === -1) {
        externalEventIds.push( rand );
        return rand;
      } else {
        return generateRandom();
      }
    }

    var deleteClick = function () {
      $(calendarEl).on("click", ".deleteEvent", function(e) {
        e.preventDefault();
        if (!confirm(Lang.get('script.are_you_sure'))) {
          return;
        }
        _deleteEvent( $(this).data('id') );
      });

      $('#btnRecordDelete').on("click", function(e) {
        e.preventDefault();
        if (!confirm(Lang.get('script.are_you_sure'))) {
          return;
        }
        _deleteEvent( current_event );
      });
    }

    var _deleteEvent = function ( event_id ) {

      KTApp.block( calendarEl );

        $.ajax({
          type: 'PATCH',
          url: "/records/calendar/delete",
          datatype: 'JSON',
          data: {
            record_id: event_id
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response, status, xhr) {
            KTApp.unblock( calendarEl );
            calendar.removeAllEvents();
            calendar.refetchEvents();
            $('.fc-day').css('background', 'none');
            var content = {};
            content.title = response.success;
            content.message = '';
            var notify = $.notify(content, {
              type: 'success',
              mouse_over:  true,
              z_index: 1051,
            });
            $('#eventModal').modal('hide');
          },
          error: function (response, status, error) {
            KTApp.unblock( calendarEl );
            console.log(response);
            var content = {};
            content.title = response.responseJSON.message;
            content.message = $.map( response.responseJSON.errors, function( error ) {
              return error;
            })
            var notify = $.notify(content, {
              type: 'danger',
              mouse_over:  true,
              z_index: 1051,
            });
          },
        });

    }

    var populateRooms = function(record) {
      if (typeof record !== 'undefined') {

        $.each(Object.values(record.rooms).filter(r => r.pivot.clean_type == 0), function(i, room) {
          tagify_depa.addTags([
            {
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
        });

        $.each(Object.values(record.rooms).filter(r => r.pivot.clean_type == 1), function(i, room) {
          tagify_restant.addTags([
            {
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
        });

      }
    }

    var get_status = function(pivot) {
      let status = `${Lang.get('script.status')}: ${constants.room_statuses[pivot.status]}`;
      if (pivot.volunteer && pivot.status == 3) {
        status += `\n${Lang.get('script.volunteer')}: ${pivot.volunteer_name}`;
      }
      return status;
    }

    var _print = function() {
      btnPrint.on('click', function(e) {
        e.preventDefault();
        window.location = `/${Lang.locale}/records/calendar/${current_employee.id}/print?date=${moment(calendar.getDate()).format('YYYY-MM')}`;
      })
    }

    var _volunteers = function() {

      // calendar.addEvent({
      //   title: 'dynamic event',
      //   start: '2022-09-10',
      //   allDay: true,
      //   // color: '#FF0000',
      //   rendering: 'background',
      //   className: 'record_calendar_plan ' + '0',
      //   allDay: true
      // });

      // loading remote data

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

    var setDayPlans = function(plans) {

      $.map( plans, function( plan ) {

        let day_events = calendar.getEvents().filter(item => moment(item.start).isSame(plan.dita, 'day'));
        //prevent double backgrounds
        if ( !day_events.length ) {
          calendar.addEvent({
            title: 'background event',
            start: plan.dita,
            allDay: true,
            // color: '#FF0000',
            rendering: 'background',
            className: `record_calendar_plan ${plan.symbol} ${constants.plan_colors[plan.symbol].color}`,
            allDay: true
          });
        }

        // if (event.rendering == 'background' && event.start <  moment("2015-02-13")){
        //   element.hide();
        // }
          
        });
    }

    var setTotalMonth = function() {
      let current_month = moment(calendar.getDate()).format('MM');
      let seconds = (total_seconds_monthly[current_month]?.reduce((a, b) => a + b, 0) ?? 0); //check if array exists and sum values of array
      let hours = (seconds / 3600000).toFixed(2);
      // let days = moment(calendar.getDate()).daysInMonth();
      let days = total_seconds_monthly[current_month]?.length ?? 0; //count only days with events
      let average = days ? (seconds / 3600000 / days).toFixed(2) : 0;

      $('.fc-toolbar .fc-center').find('.totalmonth').remove();
      $('.fc-toolbar .fc-center').append(
        `<div class="totalmonth md:block">
          <span class="label label-inline label-pill mt-1 py-0 px-1">
            <span class="mr-1">${Lang.get('script.total')}: <b>${hours}h</b></span>
            <span>${Lang.get('script.average')}: <b>${average}h</b></span>
          </span>
        </div>`
      );
    }

    return {
        //main function to initiate the module
        init: function() {
            inputs();
            initExternalEvents();
            initCalendar();
            datetime();
            _initSubmit();
            _closeModal();
            deleteClick();
            // _deleteEvent();
            _print();
            _volunteers();
        }
    };
}();

jQuery(document).ready(function() {
    KTCalendarExternalEvents.init();
});
