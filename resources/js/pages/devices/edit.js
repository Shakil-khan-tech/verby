// Class definition
var EditDevice = function() {
    // Private variables
    var tagify;

    var manualName = $('input[name="manualName"]');
    var manualCategory = $('select[name="manualCategory"]');
    var manualDepaMin = $('input[name="manualDepaMin"]');
    var manualRestantMin = $('input[name="manualRestantMin"]');
    var btnManualAdd = $('#btnManualAdd');

    var autoPrefix = $('input[name="autoPrefix"]');
    var autoPad = $('input[name="autoPad"]');
    var autoFrom = $('input[name="autoFrom"]');
    var autoTo = $('input[name="autoTo"]');
    var autoSufix = $('input[name="autoSufix"]');
    var autoCategory = $('select[name="autoCategory"]');
    var autoDepaMin = $('input[name="autoDepaMin"]');
    var autoRestantMin = $('input[name="autoRestantMin"]');
    var btnAutoAdd = $('#btnAutoAdd');

    var btnRemoveAllRooms = $('#generatedRooms_remove');

    // Private functions
    var generatedRooms = function() {

      tagify = new Tagify(document.querySelector('#generatedRooms'), {
          delimiters : null,
          // duplicates: true,
          editTags: false,
          transformTag: transformTag,
      });

      function transformTag(tagData) {
          tagData.class = 'tagify__tag tagify__tag-light--' + constants.colors[ tagData.color ];
      }

      tagify.on('add', onAddTag)
          .on('remove', onRemoveTag)
          .on('click', onTagClick);

      function onAddTag(e) {
          // console.log( "original Input:", tagify.DOM.originalInput);
          // console.log( "original Input's value:", tagify.DOM.originalInput.value);
          // console.log( "event detail:", e.detail);
      }

      function onRemoveTag(e) {
          console.log(e.detail);
      }

      function onTagClick(e) {
          console.log(e.detail);
      }

      // "remove all tags" button event listener
      btnRemoveAllRooms.on('click', function() {
        confirm( Lang.get('script.are_you_sure') ) ? tagify.removeAllTags() : '';
      });

    }

    var manualAdd = function() {
      btnManualAdd.on('click', function(e) {

        manualName.removeClass('is-invalid');
        manualDepaMin.removeClass('is-invalid');
        manualRestantMin.removeClass('is-invalid');

        if ( manualName.val().length === 0  ) { manualName.addClass('is-invalid'); return; }
        if ( manualDepaMin.val().length === 0 || manualDepaMin.val() < 0 ) { manualDepaMin.addClass('is-invalid'); return; }
        if ( manualRestantMin.val().length === 0 || manualRestantMin.val() < 0 ) { manualRestantMin.addClass('is-invalid'); return; }

        if ( manualName.val() != '' ) {
          tagify.addTags([
            {
              // value: manualName.val() + ' - ' + manualCategory.find('option:selected').text(),
              value: manualName.val(),
              name: manualName.val(),
              room_cat: manualCategory.val(),
              color: manualCategory.val(),
              depa_min: manualDepaMin.val(),
              restant_min: manualRestantMin.val(),
              title: tagify_title( manualDepaMin.val(), manualRestantMin.val() )
            }
          ]); 
        }

      })
    }

    var autoAdd = function() {
      btnAutoAdd.on('click', function(e) {

        autoFrom.removeClass('is-invalid');
        autoTo.removeClass('is-invalid');
        autoDepaMin.removeClass('is-invalid');
        autoRestantMin.removeClass('is-invalid');

        if ( autoFrom.val().length === 0  ) { autoFrom.addClass('is-invalid'); return; }
        if ( autoTo.val().length === 0  ) { autoTo.addClass('is-invalid'); return; }
        if ( autoDepaMin.val().length === 0 || autoDepaMin.val() < 0 ) { autoDepaMin.addClass('is-invalid'); return; }
        if ( autoRestantMin.val().length === 0 || autoRestantMin.val() < 0 ) { autoRestantMin.addClass('is-invalid'); return; }

        var from = parseInt(autoFrom.val());
        var to = parseInt(autoTo.val());
        var pad = parseInt(autoPad.val());

        for (var i = from; i <= to; i++) {
          tagify.addTags([
            {
              value: autoPrefix.val() + String(i).padStart(pad, '0') + autoSufix.val(),
              name: autoPrefix.val() + String(i).padStart(pad, '0') + autoSufix.val(),
              room_cat: autoCategory.val(),
              color: autoCategory.val(),
              depa_min: autoDepaMin.val(),
              restant_min: autoRestantMin.val(),
              title: tagify_title( autoDepaMin.val(), autoRestantMin.val() )
            }
          ]);
        }



      })
    }

    var currentDeviceRooms = function() {
      currentRooms.forEach((item, i) => {
        tagify.addTags([
          {
            value: item.name,
            name: item.name,
            room_id: item.id,
            room_cat: item.category,
            color: item.category,
            depa_min: item.depa_minutes,
            restant_min: item.restant_minutes,
            title: tagify_title( item.depa_minutes, item.restant_minutes )
          }
        ]);
      });

    }

    var tagify_title = function(depa, restant) {
      let title = '';
      title = `${Lang.get('script.depa')}: ${depa} min \n`;
      title += `${Lang.get('script.restant')}: ${restant} min`;

      return title;
    }

    return {
        // public functions
        init: function() {
            generatedRooms();
            manualAdd();
            autoAdd();
            currentDeviceRooms();
        }
    };
}();

jQuery(document).ready(function() {
    EditDevice.init();
});
