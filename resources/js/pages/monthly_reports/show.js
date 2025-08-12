// Class definition
var ShowDailyReports = function() {
    // Private variables
    const date_picker = $('#date_picker');
    var form = $('#MonthlyReportForm');
	  var btnSubmit = $('#reportFormSubmitAjax');

    // Private functions
    var timePicker = function() {
      date_picker.datetimepicker({
          format: 'YYYY-MM',
          // format: 'L',
          locale: Lang.locale,
          // defaultDate: moment(),
      });

      date_picker.on('change.datetimepicker', function (e) {
        window.location = `/${Lang.locale}/monthly_reports/${deviceId}?date=${e.date.format('YYYY-MM')}`;
      });
    }

    var _initSubmit = function () {
      btnSubmit.on('click', function(e){
        e.preventDefault();
        KTApp.block('#tableMonthlyReport', {});
  
        $.ajax({
          type: 'PATCH',
          url: form.attr('action'),
          data: form.serialize(),
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response, status, xhr) {
            KTApp.unblock('#tableMonthlyReport');
            initial_form_state = form.serialize();
            var content = {};
            content.title = response.success;
            content.message = '';
            var notify = $.notify(content, {
              type: 'success',
              mouse_over:  true,
              z_index: 1051,
            });
            location.reload();
          },
          error: function (data) {
            KTApp.unblock('#tableMonthlyReport');
            console.log(data);
          },
        });       
  
      });
    }

    var _initSaveChanges = function () {

      $(window).bind('beforeunload', function(e) {
        var form_state = form.serialize();
        if(initial_form_state != form_state){
          var message = Lang.get('script.unsaved_changes');
          e.returnValue = message; // Cross-browser compatibility (src: MDN)
          return message;
        }
      });
  
    }

    return {
        // public functions
        init: function() {
          timePicker();
          _initSubmit();
          _initSaveChanges();
        }
    };
}();

jQuery(document).ready(function() {
    ShowDailyReports.init();
});
