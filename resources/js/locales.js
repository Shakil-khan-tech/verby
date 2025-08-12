"use strict";

var TraceLocales = function() {
    // Private properties
    var _element;

	// Private functions
    var _datatables = function() {
        var translation = {
            records: {
                processing: Lang.get('script.datatable.processing'),
                noRecords: Lang.get('script.datatable.noRecords'),
            },
            toolbar: {
                pagination: {
                    items: {
                        default: {
                            first: Lang.get('script.datatable.first'),
                            prev: Lang.get('script.datatable.prev'),
                            next: Lang.get('script.datatable.next'),
                            last: Lang.get('script.datatable.last'),
                            more: Lang.get('script.datatable.more'),
                            input: Lang.get('script.datatable.input'),
                            select: Lang.get('script.datatable.select'),
                        },
                        info: Lang.get('script.datatable.info'),
                    },
                },
            },
        };

        return translation;
    }

    // Public Methods
	return {
        datatables: function() {
            return _datatables();
        }
	};
}();

// Webpack support
if (typeof module !== 'undefined') {
	module.exports = TraceLocales;
}
