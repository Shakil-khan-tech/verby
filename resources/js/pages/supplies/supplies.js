// Class definition
var Supplies = function() {
    // Private variables
    var supplies_repeater = $('#supplies_repeater');

    // Private functions
    var initRepeater = function() {
      supplies_repeater.repeater({
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
  Supplies.init();
});
