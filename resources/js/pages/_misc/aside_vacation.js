"use strict";

// Class definition
var AsideVacation = function () {
	// Elements
	var btnVacation = $('#btnVacation');
	var vacationData = $('.employeeVacationData');

	// Private functions
	var _initDateChange = function () {
    $('.lohnMonthYear').on('change', function(){
      window.location = `/${Lang.locale}/lohn/${currentLohnUser}/${$(this).val()}`;
    });
	}

	var _initBtnVacation = function() {
    btnVacation.on('click', function(e){
      e.stopPropagation();
      if ( $('input.vacation').val() == '' ) {
        return;
      }
      var btnVacation = $(this);

      btnVacation.addClass('spinner spinner-right spinner-white pr-15');
      $('.userVacationTable tbody').removeClass('spinner spinner-right spinner-white pr-15');
      var drp = $('#kt_daterangepicker').data('daterangepicker');

      $.ajax({
          url: "/employees/" + currentLohnUser + "/vacation",
          type: "PUT",
          cache: false,
          datatype: 'JSON',
          data: {
            "action": "add",
            "date_start" : drp.startDate.format('YYYY-MM-DD'),
            "date_end" : drp.endDate.format('YYYY-MM-DD')
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response, status, xhr, $form) {
            btnVacation.removeClass('spinner spinner-right spinner-white pr-15');
            $('.userVacationTable tbody').removeClass('spinner spinner-right spinner-white pr-15');

            let rows = '';
            let soMany = 10;

            response.pushimi.forEach((item, i) => {
              rows += `<tr>
                  <th scope="row">${item.days}</th>
                  <td>${item.fillimi}</td>
                  <td>${item.mbarimi}</td>
                  <td>
                    <form class="formDeleteVacation" action="/employees/${currentLohnUser}/vacation" onsubmit="event.preventDefault();" method="post">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="date_start" value="${item.fillimi}">
                      <input type="hidden" name="date_end" value="${item.mbarimi}">
                      <input type="hidden" name="_method" value="DELETE">
                      <button type="submit" class="btnDeleteVacation btn btn-sm btn-clean btn-icon">
                        <span class="svg-icon svg-icon-md">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <rect x="0" y="0" width="24" height="24"></rect>
                              <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"></path>
                              <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                            </g>
                          </svg>
                        </span>
                      </button>
                    </form>
                  </td>
              </tr>`;
            });
            $('.userVacationTable tbody').html(rows);


          },
          error: function (response)
          {
              btnVacation.removeClass('spinner spinner-right spinner-white pr-15');
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

	var _initVacationData = function() {
    vacationData.find('form.formDeleteVacation').on('submit', function(e) {
        e.preventDefault();
    });

    vacationData.on('click', '.btnDeleteVacation', function(e){
      e.stopPropagation();
      var theBtn = $(this);
      var formEl = theBtn.parent('form');
      var data = formEl.serializeArray();

      theBtn.addClass('spinner spinner-right spinner-white pr-15');
      $('.userVacationTable tbody').removeClass('spinner spinner-right spinner-white pr-15');

      $.ajax({
          // url: formEl.attr('action'),
          url: "/employees/" + currentLohnUser + "/vacation",
          type: "DELETE",
          cache: false,
          datatype: 'JSON',
          data: data,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response, status, xhr, $form) {
            theBtn.removeClass('spinner spinner-right spinner-white pr-15');
            $('.userVacationTable tbody').removeClass('spinner spinner-right spinner-white pr-15');

            let rows = '';
            let soMany = 10;

            response.pushimi.forEach((item, i) => {
              rows += `<tr>
                  <th scope="row">${item.days}</th>
                  <td>${item.fillimi}</td>
                  <td>${item.mbarimi}</td>
                  <td>
                    <form class="formDeleteVacation" action="/employees/${currentLohnUser}/vacation" onsubmit="event.preventDefault();" method="post">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="date_start" value="${item.fillimi}">
                      <input type="hidden" name="date_end" value="${item.mbarimi}">
                      <input type="hidden" name="_method" value="DELETE">
                      <button type="submit" class="btnDeleteVacation btn btn-sm btn-clean btn-icon">
                        <span class="svg-icon svg-icon-md">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <rect x="0" y="0" width="24" height="24"></rect>
                              <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"></path>
                              <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                            </g>
                          </svg>
                        </span>
                      </button>
                    </form>
                  </td>
              </tr>`;
            });
            $('.userVacationTable tbody').html(rows);
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
			_initDateChange();
			_initBtnVacation();
			_initVacationData();
		}
	};
}();

jQuery(document).ready(function() {
	AsideVacation.init();
});
