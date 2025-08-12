// Class definition
var CalendarPrint = function() {
    // Private variables
    const date_picker = $('#date_picker');
    var roomModal = $('#roomModal');
    var showRooms = $('.showRooms');
    var sendEmail = $('#sendEmail');
    var cardPrint = $('#cardPrint');

    // Private functions
    var _switcher = function() {
      $('#hide_columns').change(function() {
        if ( this.checked ) {
          $('.to_hide').addClass('d-none');
        } else {
          $('.to_hide').removeClass('d-none');
        }
      })
    }

    var timePicker = function() {
      date_picker.on('change', function(){
        window.location = `/${Lang.locale}/records/calendar/${current_employee.id}/print?date=${$(this).val()}`;
      });
    }

    var initShowRooms = function() {
      showRooms.on('click', function(e) {
        e.preventDefault();
        roomModal.modal('show', $(this));
      })
    }

    var initRooms = function() {
      roomModal.on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget) // Button that triggered the modal
        // let employee_id = button.data('employee') // Extract info from data-* attributes
        let current_records = button.data('records') // Extract info from data-* attributes
        let clean_type = button.data('clean_type') // Extract info from data-* attributes
        let records = employee_records.filter(m => current_records.includes(m.id));

        var output = 
        `<table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">${Lang.get('script.room_name')}</th>
              <th scope="col">${Lang.get('script.room_category')}</th>
              <th scope="col">${Lang.get('script.clean_type')}</th>
              <th scope="col">${Lang.get('script.extra')}</th>
              <th scope="col">${Lang.get('script.status')}</th>
              <th scope="col">${Lang.get('script.volunteer')}</th>
            </tr>
          </thead>
          <tbody>
        `;

        records.forEach((record) => {
          $.each(record.rooms, function(i, room) {
          // record.rooms.forEach((room, i) => {
            if (room.pivot.clean_type == clean_type) {
              let volunteer = (room.pivot.volunteer) ? room.pivot.volunteer_name : '';
              output += '\
                <tr>\
                  <td>'+ (i+1) +'</span></td>\
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
            }
          });
        });

        output += '\
          </tbody>\
        </table>\
        ';

        var modal = $(this);
        modal.find('.rooms').html( output )
      })
    }

    var _initMail = function() {
      sendEmail.on('click', function(e){
        e.preventDefault();
        KTApp.block(cardPrint, {overlayColor: '#000000', state: 'danger', message: Lang.get('script.please_wait')});

        var content = {};
        content.title = Lang.get('script.sending_email');
        content.message = Lang.get('script.checking_emp_email');
        var notify = $.notify(content, {
          type: 'primary',
          mouse_over:  true,
          showProgressbar: true,
          timer: 10,
          z_index: 1051,
        });

        $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            data: {
              'date': $(this).data('month')
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response, status, xhr) {
                KTApp.unblock(cardPrint);
                console.log(response);

                setTimeout(function() {
                  notify.update('message', response.success);
                  notify.update('type', 'success');
                  notify.update('progress', 100);
                }, 5);
            },
            error: function (response) {
                KTApp.unblock(cardPrint);
                var err = '';
                for (var err in response.responseJSON.errors) {
                  if (response.responseJSON.errors.hasOwnProperty(err)) {
                    err += response.responseJSON.errors[err] + '<br>';
                  }
                }

                
                setTimeout(function() {
                  notify.update('message', String(response.responseJSON.message));
                  notify.update('type', 'danger');
                  notify.update('progress', 60);
                }, 500);
            }
        })
      });
    }

    return {
        // public functions
        init: function() {
            _switcher();
            timePicker();
            initShowRooms();
            initRooms();
            _initMail();
        }
    };
}();

jQuery(document).ready(function() {
    CalendarPrint.init();
});
