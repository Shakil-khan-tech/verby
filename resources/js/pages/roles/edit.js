// Class definition
var EditRole = function () {
	// Elements
	var form = $('#permissions_Role');
	var manageSwitches =
	form.find("input").filter(function () {
	   return $(this).data('action') === 'manage';
	});
	var viewSwitches =
	form.find("input").filter(function () {
	   return $(this).data('action') === 'view';
	});
	var dependableSwitches = $('.dependable');
	let dependableTitle = `${Lang.get('script.needto_enable_permissions')}: `;

	// Private functions
	var _initSwitches = function () {
		$.each(manageSwitches, function(i, manageInput) {
			let viewInput = $(manageInput).closest('.form-group').find('input[data-action="view"]');
			if ( manageInput.checked ) {
				viewInput.closest('.switch').addClass("opacity-50 pointer-events-none").prop('checked', true).change();
				// viewInput.attr("disabled","disabled");
			}
		});

		manageSwitches.on('change', function (e) {
			let manageInput = $(this);
			let viewInput = $(manageInput).closest('.form-group').find('input[data-action="view"]');
			viewInput.closest('.switch').toggleClass("opacity-50 pointer-events-none");
			viewInput.prop('checked', manageInput.is(':checked')).change();
			if ( manageInput.is(':checked') ) {
				// viewInput.attr("disabled","disabled");
			} else {
				// viewInput.removeAttr("disabled"); 
			}
		});
	}

	var checkDependencies = function () {

		$.each(dependableSwitches, function(i, _switch) {
			let missingPremission = false;
			let permissions = [];
			$(_switch).data('by').split( ',' ).map(item=>item.trim()).forEach((model, i) => {
				$.each( $('input[data-model="'+ model +'"][data-action="view"]'), function(i, viewInput) {
					if (viewInput.checked === false) {
						missingPremission = true;
						permissions.push(model);
					}
				});
			});

			if (missingPremission) {
				$(_switch).find('label').addClass("opacity-50 pointer-events-none");
				$(_switch).find('input').prop('checked', false);
				// $(_switch).find('input').attr("disabled","disabled");
				if ( $(_switch).data('bs.tooltip') ) {
					// already has tooltip
					$(_switch).attr('data-original-title', dependableTitle + '<strong>' +  permissions.join(', ') + '</strong>');

				} else {
					$(_switch).tooltip({
						title: dependableTitle + '<strong>' +  permissions.join(', ') + '</strong>',
						html: true,
					})
				}
			} else {
				$(_switch).find('label').removeClass("opacity-50 pointer-events-none");
				// $(_switch).find('input').removeAttr("disabled"); 
				$(_switch).tooltip('dispose');
			}
		});

	}

	var switchChaged = function() {

		viewSwitches.on('change', function (e) {
			checkDependencies();
		});

	}



	return {
		// public functions
		init: function() {
			_initSwitches();
			checkDependencies();
			switchChaged();
		}
	};
}();

jQuery(document).ready(function() {
    EditRole.init();
});
