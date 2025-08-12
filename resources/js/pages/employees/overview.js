"use strict";
// Class definition

var EmployeeOverview = function() {
    // Private functions
    var datatable;

    // Public functions

    var delete_entry = function() {
      $('[data-entry="delete"]').on('click', function(e) {
        if (!confirm( Lang.get('script.are_you_sure') )) {
          return;
        }

        var theBtn = $(this);
        theBtn.addClass('spinner spinner-right spinner-white pr-15');

        $.ajax({
            // url: formEl.attr('action'),
            url: `/employees/${currentLohnUser}/delete_entry_date`,
            type: "POST",
            cache: false,
            datatype: 'JSON',
            data: {
                'start' : $(this).data('start'),
                'end' : $(this).data('end'),
                'entry_id' : $(this).data('key')
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response, status, xhr, $form) {
                theBtn.removeClass('spinner spinner-right spinner-white pr-15');

                var content = {};
                content.title = response.title;
                content.message = response.message;
                $.notify(content, {
                    type: response.success ? 'success' : 'danger',
                    mouse_over:  true,
                    z_index: 1051,
                });

                if (response.success) {
                  location.reload();
                }
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

    return {
        // public functions
        init: function() {
            delete_entry();
        }
    };
}();

jQuery(document).ready(function() {
    EmployeeOverview.init();
});
