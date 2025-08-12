// Class definition

var BootstrapDaterangepicker = function () {

    // Private functions
    var _dateRange = function () {
        $('#kt_daterangepicker').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            // opens: 'right',
            // showDropdowns: false,
            parentEl: "#kt_profile_aside"
        }, function(start, end, label) {
            $('#kt_daterangepicker .form-control').val( start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
        });
    }

    return {
        // public functions
        init: function() {
            _dateRange();
        }
    };
}();

jQuery(document).ready(function() {
    BootstrapDaterangepicker.init();
});
