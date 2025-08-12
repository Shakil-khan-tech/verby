// Class definition
var ShowDailyReports = function() {
    // Private variables
    const date_picker = $('#date_picker');
    var roomModal = $('#roomModal');
    var modalTitle = $('#performModal').find('span');
    var showRooms = $('.showRooms');

    // Private functions
    var timePicker = function() {
      date_picker.datetimepicker({
          format: 'DD.MM.YYYY',
          // format: 'L',
          locale: Lang.locale,
          defaultDate: moment(),
      });

      date_picker.on('change.datetimepicker', function (e) {
        window.location = `/${Lang.locale}/daily_reports/${deviceId}?date=${e.date.format('DD.MM.YYYY')}`;
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
        let employee_id = button.data('employee') // Extract info from data-* attributes
        let current_records = button.data('records') // Extract info from data-* attributes
        let clean_type = button.data('clean_type') // Extract info from data-* attributes
        let records = daily_employees.find(o => o.id === employee_id).records.filter(m => current_records.includes(m.id));
        let employee_name = daily_employees.find(o => o.id === employee_id).fullname;
        modalTitle.text(employee_name);

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
              let volunteer = (room.pivot.volunteer != null) ? room.pivot.volunteer_name : '';
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

    return {
        // public functions
        init: function() {
            timePicker();
            initShowRooms();
            initRooms();
        }
    };
}();

jQuery(document).ready(function() {
    ShowDailyReports.init();
});
