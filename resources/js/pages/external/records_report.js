// Class definition
var RecordsReport = function() {
    // Private variables
    var dropdownMenu = $('#dropdownMenuAction');
    var feedbackDeclineModal = $('#feedbackDeclineModal');
    var reportDeclineButton = $('#reportDeclineButton');
    var reportAcceptButton = $('#reportAcceptButton');
    var btnDeclineSend = $('#btnDeclineSend');
    var declineForm = $('#formDecline');

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

    var initDeclineModal = function() {
      reportDeclineButton.on('click', function(e) {
        e.preventDefault();
        feedbackDeclineModal.modal('show', $(this));
      })
    }

    var _initDeclineForm = function() {
      btnDeclineSend.on('click', function(e){
        e.preventDefault();
        KTApp.block(feedbackDeclineModal, {overlayColor: '#000000', state: 'danger', message: Lang.get('script.please_wait')});

        $.ajax({
            type: 'POST',
            url: $(declineForm).attr('action'),
            data: declineForm.serialize(),
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response, status, xhr) {
              KTApp.unblock(feedbackDeclineModal);

              var content = {};
              content.title = String(response.message);
              content.message = '';

              var notify = $.notify(content, {
                type: 'success',
                mouse_over:  true,
                z_index: 1051,
              });

              feedbackDeclineModal.modal('hide');

              setTimeout(function() {
                location.reload();
              }, 1000);
            },
            error: function (response) {
                KTApp.unblock(feedbackDeclineModal);
                var err = '';
                for (var err in response.errors) {
                  if (response.errors.hasOwnProperty(err)) {
                    err += response.errors[err] + '<br>';
                  }
                }

                setTimeout(function() {
                  notify.update('message', String(response.message));
                  notify.update('type', 'danger');
                  notify.update('progress', 60);
                }, 500);
            }
        })
      });
    }

    var _initAccept = function() {
      reportAcceptButton.on('click', function(e){
        e.preventDefault();

        if (!confirm( Lang.get('script.are_you_sure') )) {
          return;
        }

        KTApp.block(dropdownMenu, {overlayColor: '#000000', state: 'danger', message: Lang.get('script.please_wait')});

        $.ajax({
            type: 'POST',
            url: $(this).data('url'),
            data: {
              _token: $('meta[name="csrf-token"]').attr('content'),
              feedback: 1,
              date: $(this).data('date'),
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response, status, xhr) {
              KTApp.unblock(dropdownMenu);

              var content = {};
              content.title = String(response.message);
              content.message = '';

              var notify = $.notify(content, {
                type: 'success',
                mouse_over:  true,
                z_index: 1051,
              });

              setTimeout(function() {
                location.reload();
              }, 1000);
            },
            error: function (response) {
                KTApp.unblock(dropdownMenu);
                var err = '';
                for (var err in response.errors) {
                  if (response.errors.hasOwnProperty(err)) {
                    err += response.errors[err] + '<br>';
                  }
                }

                setTimeout(function() {
                  notify.update('message', String(response.message));
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
            initDeclineModal();
            _initDeclineForm();
            _initAccept();
        }
    };
}();

jQuery(document).ready(function() {
  RecordsReport.init();
});