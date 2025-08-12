// Class definition
var Issues = function() {
    // Private variables
    var issues_repeater = $('#issues_repeater');

    // Private functions
    var initRepeater = function() {
      issues_repeater.repeater({
          initEmpty: false,
          //
          // defaultValues: {
          //     'text-input': 'foo'
          // },

          show: function () {
              $(this).slideDown();
          },

          hide: function(deleteElement) {
            $(this).slideUp(deleteElement);
          }
      });
    }

    return {
        // public functions
        init: function() {
            initRepeater();
        }
    };
}();

jQuery(document).ready(function() {
  Issues.init();
});
 