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
      acceptedFiles: "image/*, application/pdf, .rtf, .odf, .doc, .docx, .xls, .xlsx, .csv, .txt",
      accept: function (file, done) {
        done();
      },
      init: function () {
        this.on("queuecomplete", function (file) {
          datatable.reload();
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
      },
      {
        field: 'name',
        title: Lang.get('script.name'),
        class: 'font-bold',
        template: function (row) {
          return `
            <div class="d-flex align-items-center justify-content-between w-100">
              <div class="file-name-text text-truncate" id="file-name-${row.id}" title="${row.name}">
                ${row.name}
              </div>
              <a href="javascript:;" class="editFileName" data-id="${row.id}" data-name="${row.name}" title="${Lang.get('script.edit')}">
                <i class="fas fa-pen text-info ms-2" style="cursor:pointer;"></i>
              </a>
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
                <a href="javascript:;" class="btnPreviewFile btn btn-sm btn-clean btn-icon mr-2" data-preview-url="/employees/files/preview/${row.id}" title="${Lang.get('script.preview')}">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="/employees/files/download/${row.id}" class="btn btn-sm btn-clean btn-icon mr-2" title="${Lang.get('script.download')}">
                    <span class="svg-icon svg-icon-md">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g id="Stockholm-icons-/-Files-/-Download" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                              <path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" id="Path-57" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                              <rect id="Rectangle" fill="#000000" opacity="0.3" transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) " x="11" y="1" width="2" height="14" rx="1"></rect>
                              <path d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z" id="Path-102" fill="#000000" fill-rule="nonzero" transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) "></path>
                          </g>
                        </svg>
                    </span>
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
        // url: formEl.attr('action'),
        url: `/employees/files/delete/${theBtn.data('record_id')}`,
        type: "DELETE",
        cache: false,
        datatype: 'JSON',
        data: {
          "id": theBtn.data('record_id'),
          "_method": "DELETE"
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response, status, xhr, $form) {
          var content = {};
          content.title = response.success;
          content.message = '';
          var notify = $.notify(content, {
            type: 'success',
            mouse_over: true,
            z_index: 1051,
          });
          datatable.reload();
        },
        error: function (response) {
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
    init: function () {
      document.querySelector('.future-dropzone').classList.add('dropzone');
      initUpload();
      initDatatable();
      deleteFile();
    }
  };
}();



$('#files_datatable').on('click', '.editFileName', function () {
  const id = $(this).data('id');
  const name = $(`#file-name-${id}`).text().trim(); // ✅ always gets current text

  $('#editFileId').val(id);
  $('#editFileName').val(name);
  $('#editFileNameModal').modal('show');
});


jQuery(document).ready(function () {
  Items.init();
});

// // Track original value when editing starts
// $('#files_datatable').on('click', '.btnEditFileName', function () {
//   const id = $(this).data('id');
//   const rowText = $(`.file-name-text[data-id="${id}"]`);
//   const rowInput = $(`.file-name-input[data-id="${id}"]`);

//   rowText.addClass('d-none');
//   rowInput.removeClass('d-none').focus().select();
//   rowInput.data('original-value', rowInput.val());

// });

// // On blur, compare and either send AJAX or revert
// $('#files_datatable').on('blur', '.file-name-input', function () {
//   const input = $(this);
//   const id = input.data('id');
//   const originalValue = input.data('original-value');
//   const newValue = input.val().trim();
//   const rowText = $(`.file-name-text[data-id="${id}"]`);

//   if (newValue && newValue !== originalValue) {
//     $.ajax({
//       url: `/employees/files/madia-name/${id}`,
//       type: 'POST',
//       headers: {
//         'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
//       },
//       data: {
//         name: newValue
//       },
//       success: function (response) {
//         // Update text, show notification, and hide input
//         rowText.text(response.name);
//         rowText.removeClass('d-none');
//         input.addClass('d-none');
//         input.data('original-value', response.name); // Update stored value

//         // Optional notification
//         $.notify({
//           title: response.success || 'Updated!',
//           message: ''
//         }, {
//           type: 'success',
//           z_index: 1051,
//           mouse_over: true
//         });

//         if (typeof datatable !== 'undefined' && datatable.reload) {
//           datatable.reload(); // For KTDatatable
//         } else if ($('#files_datatable').DataTable) {
//           $('#files_datatable').DataTable().ajax.reload(); // For DataTables
//         }
//       },
//       error: function (xhr) {
//         let msg = 'Error updating name';
//         if (xhr.responseJSON && xhr.responseJSON.message) {
//           msg = xhr.responseJSON.message;
//         }
//         $.notify({
//           title: 'Error',
//           message: msg
//         }, {
//           type: 'danger',
//           z_index: 1051,
//           mouse_over: true
//         });

//         revert();
//       }
//     });

//   } else {
//     revert();
//   }

//   function revert() {
//     input.val(originalValue);
//     input.addClass('d-none');
//     rowText.removeClass('d-none');
//   }
// });

// Open modal on pencil icon click
// Open modal with data
// $(document).on('click', '.editFileName', function () {
//   const id = $(this).data('id');
//   const name = $(this).data('name');

//   $('#editFileId').val(id);
//   $('#editFileName').val(name);

//   $('#editFileNameModal').modal('show');
// });

$('#files_datatable').on('click', '.editFileName', function () {
  const id = $(this).data('id');

  // ✅ Always get the CURRENT visible text, not the stale data-name
  const name = $(`#file-name-${id}`).text().trim();

  $('#editFileId').val(id);
  $('#editFileName').val(name);
  $('#editFileNameModal').modal('show');

  // Optional: focus the input and select text
  setTimeout(() => {
    $('#editFileName').focus().select();
  }, 300);
});

$('#closeFileNameBtn').on('click', function () {
  $('#editFileName').val('');
  $('#editFileId').val('');
  $('#editFileNameModal').modal('hide');
});

// Save and update
$('#saveFileNameBtn').on('click', function () {
  const id = $('#editFileId').val();
  const newName = $('#editFileName').val().trim();
  if (!newName) {
    alert('Name cannot be empty.');
    return;
  }

  $.ajax({
    url: `/employees/files/madia-name/${id}`,
    type: 'POST',
    headers: {
      'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
    },
    data: { name: newName },
    success: function (response) {
      $('#editFileNameModal').modal('hide');
    
      // ✅ Update name text in table
      const nameDiv = $(`#file-name-${id}`);
      nameDiv.text(response.name).addClass('text-success fw-bold');

    // ✅ Also update the data-name attribute on the edit icon
    $(`#file-name-${id}`)
      .closest('div.d-flex')
      .find('.editFileName')
      .attr('data-name', response.name);
        // ✨ Highlight effect
        // const row = nameDiv.closest('tr');
        // row.addClass('bg-success bg-opacity-10');
        setTimeout(() => {
          nameDiv.removeClass('text-success fw-bold');
        }, 2000);
      
        // Notification
        $.notify({
          title: response.success || 'File Name Updated!',
          message: ''
        }, {
          type: 'success',
          z_index: 1051,
          mouse_over: true
        });
      },      
      error: function (xhr) {
        let msg = 'Error updating name';
        if (xhr.responseJSON?.message) {
          msg = xhr.responseJSON.message;
        }
        $.notify({
          title: 'Error',
          message: msg
        }, {
          type: 'danger',
          z_index: 1051,
          mouse_over: true
        });
      }
  });
});



// $(document).on('click', '.btnPreviewFile', function () {
//   // const previewUrl = $(this).data('preview-url');
//   // $('#filePreviewIframe').attr('src', previewUrl);
//   // $('#previewFileModal').modal('show');
//   const previewUrl = $(this).data('preview-url');

//   $.ajax({
//     url: previewUrl,
//     type: 'GET',
//     success: function (response) {
//       const fileUrl = response.media;
//       //console
//       $('#filePreviewIframe').attr('src', fileUrl);
//       $('#previewFileModal').modal('show');
//     },
//     error: function () {
//       alert('Could not load the file preview.');
//     }
//   });
// });


$(document).on('click', '.btnPreviewFile', function () {
  const previewUrl = $(this).data('preview-url');

  $.ajax({
    url: previewUrl,
    type: 'GET',
    success: function (response) {
      const fileUrl = response.media;
      const fileName = response.file_name;
      const extension = fileName.split('.').pop().toLowerCase();

      let previewContent = '';

      if (['pdf'].includes(extension)) {
        previewContent = `<iframe src="${fileUrl}" width="100%" height="100%" style="border: none;"></iframe>`;
      } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
        previewContent = `<img src="${fileUrl}" alt="Image Preview" style="max-width: 100%; height: auto; display: block; margin: auto;">`;
      } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
        const encodedUrl = encodeURIComponent(fileUrl);
        previewContent = `<iframe src="https://docs.google.com/gview?url=${encodedUrl}&embedded=true" width="100%" height="100%" style="border: none;"></iframe>`;
      } else {
        previewContent = `<div class="text-center p-4"><strong>Preview not available for this file type.</strong></div>`;
      }
      $('#previewFileModalLabel').text('');
      $('#previewFileModalLabel').text(fileName);
      $('#filePreviewIframe').replaceWith(`<div id="filePreviewIframe" style="height: 80vh;">${previewContent}</div>`);
      $('#previewFileModal').modal('show');
    },
    error: function () {
      alert('Could not load the file preview.');
    }
  });
});

