const { log } = require("handlebars");
const { split } = require("lodash");

// Class definition
var Items = function () {
  // Private variables
  var datatable;


  // Private functions
  var initUpload = function () {


    let employeeDrompzone = $('#kt_dropzone_files').dropzone({
      // let employeeDrompzone = new Dropzone("#kt_dropzone_files", {
      url: files_store_url, // Set the url for your upload script location
      method: 'post',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      paramName: "files", // The name that will be used to transfer the file
      maxFiles: 10,
      maxFilesize: 25, // MB
      addRemoveLinks: true,
      acceptedFiles: ".docx",
      accept: function (file, done) {
        done();
      },
      init: function () {
        this.on("queuecomplete", function (file) {
          datatable.reload();
          this.removeAllFiles(true);
        });
      }
    });

  }

  var initDatatable = function () {

    datatable = $('#files_datatable').KTDatatable({
      // datasource definition
      data: {
        type: 'remote',
        source: {
          read: {
            url: files_get_url,
            headers: {
              'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
            },
            map: function (raw) {
              var dataSet = raw;
              if (typeof raw.data !== 'undefined') {
                dataSet = raw.data;
              }
              return dataSet;
            },
          },
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
      },

      // layout definition
      layout: {
        scroll: false,
        footer: false,
      },

      // column sorting
      sortable: true,

      pagination: true,

      search: {
        input: $('#files_datatable_search_query'),
        key: 'generalSearch'
      },

      rowGroup: {
        dataSrc: 'room_id',
      },

      // columns definition
      columns: [{
        field: 'id',
        title: '#',
        width: 40,
        type: 'number',
        selector: false,
        textAlign: 'center',
        autoHide: false,
        template: function(row, index, datatable) {
        // Calculate serial number based on current page and position
        const currentPage = datatable.getCurrentPage();
        const perPage = datatable.getPageSize();
        return (currentPage - 1) * perPage + index + 1;
    },
      },
      {
        field: 'name',
        title: Lang.get('script.name'),
        class: 'font-bold',
        template: function (row) {
          return `
            
              <div class="text-dark-75 font-weight-bold line-height-sm" id="file-name-${row.id}" title="${row.name}">
                ${row.name}
              </div>
            
          `;
        },
      },
      {
        field: 'date',
        title: Lang.get('script.date'),
        template: function (row) {
          return moment(row.updated_at).format('DD.MM.YYYY HH:mm:ss');
        },
      },
      {
        field: 'mime_type',
        title: Lang.get('script.type'),
        template: function (row) {
          let mime = row.mime_type.split('/')[1];
          if (mime)
            return `<img class="max-h-35px" src="/media/svg/files/${mime}.svg">`;

          return row.mime_type;
        },
      },
      {
        field: 'size',
        title: Lang.get('script.size'),
        template: function (row) {
          return NiceBytes.init(row.size);
        },
      },
      {
        field: 'Actions',
        title: Lang.get('script.Actions'),
        sortable: false,
        width: 125,
        overflow: 'visible',
        autoHide: false,
        template: function (row) {
          return `
                <a href="/contracts/download/${row.id}" class="btn btn-sm btn-clean btn-icon mr-2" title="${Lang.get('script.download')}">
                    <i class="icon-md fas fa-download"></i>
                </a>

                <a href="javascript:;" class="btnDeleteFile btn btn-sm btn-clean btn-icon" data-record_id="${row.id}" title="${Lang.get('script.Delete')}">
                    <span class="svg-icon svg-icon-md">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"/>
                                <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg>
                    </span>
                </a>
              `;
        },
      }],

      //translation
      translate: TraceLocales.datatables(),

    });

    var daterangepicker = $('#requestedDaterangepicker').daterangepicker({
      buttonClasses: ' btn',
      applyClass: 'btn-primary',
      cancelClass: 'btn-secondary',

      // autoUpdateInput: false,

      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY h:mm A'
      },
      ranges: {
        [Lang.get('script.today')]: [moment(), moment()],
        [Lang.get('script.yesterday')]: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        [Lang.get('script.last7days')]: [moment().subtract(6, 'days'), moment()],
        [Lang.get('script.last30days')]: [moment().subtract(29, 'days'), moment()],
        [Lang.get('script.this_month')]: [moment().startOf('month'), moment().endOf('month')],
        [Lang.get('script.last_month')]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
    }, function (start, end, label) {
      $('#kt_daterangepicker_4 .form-control').val(start.format('MM/DD/YYYY h:mm A') + ' / ' + end.format('MM/DD/YYYY h:mm A'));
      datatable.search({ 'start': start.format('YYYY-MM-DD HH:mm:00'), 'end': end.format('YYYY-MM-DD HH:mm:00') }, 'Date');
    });

    daterangepicker.on('cancel.daterangepicker', function (ev, picker) {
      $(this).val('');
      datatable.search({ 'start': null, 'end': null }, 'Date');
    });

    $('#clearRequestedDaterangepicker').click(function () {
      $('#requestedDaterangepicker').val('');
      datatable.search({ 'start': null, 'end': null }, 'Date');
    });

  };

  var deleteFile = function () {
    datatable.on('click', '.btnDeleteFile', function (e) {
      e.stopPropagation();
      if (!confirm(Lang.get('script.are_you_sure'))) {
        return;
      }
      var theBtn = $(this);
      theBtn.addClass('spinner spinner-right spinner-white pr-15');

      $.ajax({
        url: `/contracts/${theBtn.data('record_id')}`,
        type: "DELETE",
        cache: false,
        dataType: 'json', // Fixed typo: datatype -> dataType
        data: {
          "id": theBtn.data('record_id'),
          "_method": "DELETE"
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          theBtn.removeClass('spinner spinner-right spinner-white pr-15');
          console.log(response.success);
          $.notify({
            //title: Lang.get('script.success'),
            message: response.success
          }, {
            type: 'success',
            placement: {
              from: "top",
              align: "right"
            },
            mouse_over: true,
            z_index: 1051,
            delay: 5000
          });

          datatable.reload();
        },
        error: function (response) {
          theBtn.removeClass('spinner spinner-right spinner-white pr-15');
          var message = response.responseJSON?.message || 'An error occurred';
          var errors = response.responseJSON?.errors || {};

          var e = '<b>' + message + '</b><br>';
          for (var err in errors) {
            if (errors.hasOwnProperty(err)) {
              e += errors[err] + '<br>';
            }
          }

          $.notify({
            title: Lang.get('script.error'),
            message: e
          }, {
            type: 'danger',
            placement: {
              from: "top",
              align: "right"
            },
            mouse_over: true,
            z_index: 1051
          });
        }
      });
    });
  }

  return {
    // public functions
    init: function () {
      document.querySelector('.future-dropzone').classList.add('dropzone');
      initUpload();
      initDatatable();
      deleteFile();
    }
  };
}();

jQuery(document).ready(function () {
  Items.init();
});



