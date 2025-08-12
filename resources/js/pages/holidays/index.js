// Class definition
var IndexHoliday = function() {
    // Private variables
    var holiday_repeater = $('#holiday_repeater');

    // Private functions
    var initRepeater = function() {
      holiday_repeater.repeater({
          initEmpty: false,
          //
          // defaultValues: {
          //     'text-input': 'foo'
          // },

          show: function () {
              $(this).slideDown();
              initDatePicker();
          },

          hide: function(deleteElement) {
            $(this).slideUp(deleteElement);
          }
      });
    }

    var initDatePicker = function() {
      $('.date_picker').datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        startDate: new Date((new Date()).getFullYear(), 0, 1),
        endDate: new Date((new Date()).getFullYear(), 11, 31),
        hideIfNoPrevNext: true,
        format: 'mm-dd',
        defaultDate: moment().format('MM-DD'),
      });
    }

    return {
        // public functions
        init: function() {
            initRepeater();
            initDatePicker();
        }
    };
}();

jQuery(document).ready(function() {
    IndexHoliday.init();
});
