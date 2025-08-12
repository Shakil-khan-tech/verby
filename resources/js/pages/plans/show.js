// Class definition
var PlanForm = function () {
	// Elements
	var form = $('#planForm');
	var btnSubmit = $('#planFormSubmitAjax');
	var footerSymbols = $('.footerSymbol');
	var inputSymbols = $('.inputSymbol');
	var fv;

	var initial_form_state = form.serialize();

	// Private functions
	var _initLoaded = function () {
		if (typeof KTLayoutStickyCard !== 'undefined') {
			if ( window.outerWidth > 768 ) {
				KTLayoutStickyCard.init('planCard');
			}
		}
		KTApp.block('#tablePlans', {
			overlayColor: '#000000',
			state: 'danger',
			message: Lang.get('script.please_wait')
		});

		$.ajax({
			url: "/plans/" + deviceId + "/records",
			type: "POST",
			cache: false,
			datatype: 'JSON',
			data: {
			  "date" : calendarDate,
			  "inactive" : inactiveEmployees,
			},
			headers: {
			  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response, status, xhr, $form) {

				let startTime = performance.now()
				$.each(inputSymbols, function() {
					let symbol = $(this);
					let dita = symbol.parent('div').find("input[name*='dita']").val();
					if ( moment( moment().format("YYYY-MM-DD HH:mm:ss") ).isBefore(dita) ) return; //skip checking dates >= today!

					let emp_id = symbol.parent('div').find("input[name*='employee_id']").val();
					
					if ( !response.filter(e => e.employee_id == emp_id && e.date == dita).length > 0 ) {
						//There was a number for a plan, but employee has no record!
						if ( _isNumericAndPositive(symbol.val() ) ) {
							symbol.tooltip({
								// title: 'Reverted to 0, because employee is not in Records list. Original value was ' + '<strong>' +  symbol.val() + '</strong>',
								title: Lang.get('script.notin_record_list'),
								html: true,
								// trigger: 'hover',
							});
							// symbol.val('0');
							symbol.parent('div').prepend('<div class="symbol absolute"><i class="bg-warning symbol-badge symbol-badge-top-middle"></i></div>');
						}
					} else {
						//There is a record!
						if ( symbol.val() == '' ) {
							//There is a record, but the employee was not assigned on the plan.
							symbol.tooltip({
								title: Lang.get('script.added_as_vol'),
								html: true,
								// trigger: 'hover',
							});
							symbol.val('V').change();
							symbol.parent('div').prepend('<div class="symbol absolute"><i class="bg-success symbol-badge symbol-badge-top-middle"></i></div>');
						}
					}
				})
				let endTime = performance.now()
				console.log(`Checking and modifing symbols took ${endTime - startTime} milliseconds`)

				KTApp.unblock('#tablePlans');
				_initEntries();

			},
			error: function (response)
			{
				var e = '<b>' + String(response.responseJSON.message) + '</b><br>';
				for (var err in response.responseJSON.errors) {
				  if (response.responseJSON.errors.hasOwnProperty(err)) {
					e += response.responseJSON.errors[err] + '<br>';
				  }
				}
				console.log(e);
				KTApp.unblock('#tablePlans');
			}
		});
	}

	var _initEntries = function () {
		KTApp.block('#tablePlans', {
			overlayColor: '#000000',
			state: 'danger',
			message: Lang.get('script.please_wait')
		});
		let startTime = performance.now()

		$.each(inputSymbols, function() {
			let symbol = $(this);
			if (symbol.hasClass('out_of_entry')) {
				symbol.tooltip({
					title: Lang.get('script.out_of_entry'),
					html: true,
				});
				symbol.parent('div').prepend('<div class="symbol absolute"><i class="bg-danger symbol-badge symbol-badge-top-middle"></i></div>');
			}
		});

		let endTime = performance.now()
		console.log(`Adding tooltips to out of entry symbols took ${endTime - startTime} milliseconds`)
		KTApp.unblock('#tablePlans');
	}

	var _initSubmit = function () {
		btnSubmit.on('click', function(e){
			e.preventDefault();

			fv.validate().then(function(status) {
				if (status === 'Valid') {
					KTApp.block('#tablePlans', {});

					// var formArr = form.serializeArray();
					// var plans = [];
					// var i, j, chunk = 30 * 4;
					// for (i = 0,j = formArr.length; i < j; i += chunk) {
					// 	plans.push( formArr.slice(i, i + chunk) );
					// }

					$.ajax({
						type: 'PATCH',
						url: form.attr('action'),
						// data: {
						// 	"plans" : JSON.stringify(plans),
						// },
						data: form.serialize(),
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function (response, status, xhr) {
							KTApp.unblock('#tablePlans');
							initial_form_state = form.serialize();
							var content = {};
							content.title = Lang.get('script.form_saved');
							content.message = '';
							var notify = $.notify(content, {
								type: 'success',
								mouse_over:  true,
								z_index: 1051,
							});
						},
						error: function (data) {
							KTApp.unblock('#tablePlans');
							console.log(data);
						},
					})
				} else {
					var content = {};
					content.title = Lang.get('script.invalid_form');
					content.message = Lang.get('script.check_fileds_icon');
					var notify = $.notify(content, {
						type: 'danger',
						mouse_over:  true,
						z_index: 1051,
					});
				}
			});
			

		});
	}

	var _initFooterSymbol = function () {
		// document.activeElement instanceof HTMLInputElement && document.activeElement.type == 'text';
		footerSymbols.on('click', function() {
			var symbol = $(this).data('symbol');
			var input = $('input.current');
			if (input.prop('readonly')) {
				input.parents('td').next('td').find('input.inputSymbol').focus();
				return;
			}
			input.val( symbol );
			input.parent('div').removeClass();
			input.parent('div').addClass( constants.plan_colors[symbol].color );
			console.log(input);
			input.parents('td').next('td').find('input.inputSymbol').focus();
		});

		inputSymbols.on('focus', function(e) {
		inputSymbol = $(this);
		$(this).select();
		inputSymbols.removeClass('current');
		inputSymbol.addClass('current');
		});


		inputSymbols.on('change keydown paste', function(event) {
			inputSymbol = $(this);
			// inputSymbol.val( inputSymbol.val().toUpperCase() );

			if ( constants.plan_colors[inputSymbol.val()] !== undefined ) {
				inputSymbol.parent('div').addClass( constants.plan_colors[inputSymbol.val()].color );
			} else {
				inputSymbol.parent('div').removeClass();
			}

			const key = event.key

			if( key === "Backspace" ) {
				if ( inputSymbol.val() == '' ) {
					inputSymbol.parents('td').prev('td').find('input.inputSymbol').focus();
				}
			}

			// fv.enableValidator('digits');

			// console.log(inputSymbol.val().toUpperCase());
			// let regexp = /^\d{1,2}(-\d{1,2})?$/;
			// console.log( regexp.test( inputSymbol.val().toUpperCase() ) );

			//in server we save only inputs that are not null(empty) for faster sql queries so,
			//if a user whats to delete a symbol we set it as 0
			// if ( inputSymbol.val() == '' && inputSymbol.data('initial_value') != '' ) {
			// 	inputSymbol.val('0');
			// }
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

	var _initValidation = function () {

		fv = FormValidation.formValidation(document.getElementById('planForm'), {
		    fields: {
					digits: {
						selector: '.inputSymbol',
						validators: {
							callback: {
								message: Lang.get('script.wrong_answer'),
								callback: function (input) {
									if ( input.value === '' ) {
										return true;
									}
									if ( Object.keys(constants.plan_colors).includes(input.value.toUpperCase()) ) {
										return true;
									}
									if ( /^([2][0-3]|[0-1]?[0-9])([:][0-5][0-9])?([-]([2][0-3]|[0-1]?[0-9])?([:][0-5][0-9])?)?$/.test(input.value) ) {
										//regexr.com/698qc
										return true;
									}
									// if ( /^\d{1,2}(-\d{1,2})?$/.test(input.value) ) {
									// 	//8 or 8-17
									// 	return true;
									// }
									return false;
								},
							},
						}
					},
		    },
			plugins: {
				trigger: new FormValidation.plugins.Trigger(),
				// Validate fields when clicking the Submit button
				//submitButton: new FormValidation.plugins.SubmitButton(),
				// Submit the form when all fields are valid
				//defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
				// bootstrap: new FormValidation.plugins.Bootstrap(),
				icon: new FormValidation.plugins.Icon({
					// valid: 'fa fa-check',
					invalid: 'fa fa-times text-danger',
					validating: 'fa fa-refresh',
				}),
			}
		});

	}

	var _isNumericAndPositive = function (value) {
		// if ( /^\d+$/.test(value) || /^\d{1,2}(-\d{1,2})?$/.test(value) ) {
		if ( /^([2][0-3]|[0-1]?[0-9])([:][0-5][0-9])?([-]([2][0-3]|[0-1]?[0-9])?([:][0-5][0-9])?)?$/.test(value) ) {
			// regexr.com/698qc
			return true;
		}
		return false;
	}

	var _initStickyTableHeader = function (value) {
		window.addEventListener('scroll', function(e) {
			if ( $('body').hasClass('card-sticky-on') ) {
				// $('.table-sticky thead').css('transform', 'translateY(' + window.scrollY + 'px)');
				$('.table-sticky thead').css({'top': `${+window.scrollY+20}px`});
				// $('#planHead').detach().appendTo('#headerHolder');
			} else {
				// $('#planHead').detach().appendTo('#headerHolder');				
			}
		});
	}

	return {
		// public functions
		init: function() {
			_initLoaded();
			// _initEntries();
			_initSubmit();
			_initFooterSymbol();
			_initSaveChanges();
			_initValidation();
			_initStickyTableHeader();
		}
	};
}();

jQuery(document).ready(function() {
    PlanForm.init();
});
