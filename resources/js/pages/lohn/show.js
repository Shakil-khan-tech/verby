// Class definition
var LohnShow = function() {
    // Private variables
    var sendEmail = $('#sendEmail');
    var profileAside = $('#kt_profile_aside');

    // Private functions

    var _initMail = function() {
      sendEmail.on('click', function(e){
        e.preventDefault();
        KTApp.block(profileAside, {overlayColor: '#000000', state: 'danger', message: Lang.get('script.please_wait'), top: '2%'});

        var content = {};
        content.title = Lang.get('script.sending_email');
        content.message = Lang.get('script.checking_emp_email');
        var notify = $.notify(content, {
          type: 'primary',
          mouse_over:  true,
          showProgressbar: true,
          timer: 20000,
          z_index: 1051,
        });
        setTimeout(function() {
          notify.update('message', Lang.get('script.sending'));
          notify.update('progress', 10);
        }, 1000);

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
                KTApp.unblock(profileAside);
                console.log(response);

                setTimeout(function() {
                  notify.update('message', response.success);
                  notify.update('type', 'success');
                  notify.update('progress', 100);
                }, 50);

                setTimeout(function() {
                  notify.close();
                }, 1000);
            },
            error: function (response) {
                KTApp.unblock(profileAside);
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

                setTimeout(function() {
                  notify.close();
                }, 2000);
            }
        })
      });
    }

    return {
        // public functions
        init: function() {
            _initMail();
        }
    };
}();

jQuery(document).ready(function() {
  LohnShow.init();
});
