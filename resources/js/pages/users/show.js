"use strict";

// Class definition
var UserShow = function () {
	// Elements
	var avatar;

	// Private functions
	var _initForm = function() {
		avatar = new KTImageInput('kt_profile_avatar');
	}

	return {
		// public functions
		init: function() {
			_initForm();
		}
	};
}();

jQuery(document).ready(function() {
	UserShow.init();
});
