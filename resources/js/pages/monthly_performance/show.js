// Class definition
var ShowMonthlyPerformance = function() {
    // Private variables
    const date_picker = $('#date_picker');

    // Private functions
    var timePicker = function() {
      date_picker.datetimepicker({
          format: 'YYYY-MM',
          // format: 'L',
          locale: Lang.locale,
          // defaultDate: moment(),
      });

      date_picker.on('change.datetimepicker', function (e) {
        window.location = `/${Lang.locale}/monthly_performance/${deviceId}?date=${e.date.format('DD.MM.YYYY')}`;
      });
    }

    return {
        // public functions
        init: function() {
            timePicker();
        }
    };
}();

jQuery(document).ready(function() {
  ShowMonthlyPerformance.init();
});
