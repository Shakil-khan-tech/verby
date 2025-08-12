// Class definition
var Issues = function() {
    // Private variables
    

    // Private functions
    var initUpload = function() {
      
      $('#kt_dropzone_external_issues').dropzone({
          url: "https://keenthemes.com/scripts/void.php", // Set the url for your upload script location
          paramName: "file", // The name that will be used to transfer the file
          maxFiles: 10,
          maxFilesize: 10, // MB
          addRemoveLinks: true,
          acceptedFiles: "image/*,application/pdf,.psd",
          accept: function(file, done) {
              if (file.name == "justinbieber.jpg") {
                  done("Naha, you don't.");
              } else {
                  done();
              }
          }
      });

    }

    return {
        // public functions
        init: function() {
          document.querySelector('.future-dropzone').classList.add('dropzone');
          initUpload();
        }
    };
}();

jQuery(document).ready(function() {
  Issues.init();
});
