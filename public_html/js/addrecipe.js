var MAX_INPUT_FIELDS = 20;

function checkFormData() {
  var result = false;
  // Check if there is a recipe in DB with the same name
  $.ajax({ url: 'recipeindb.php',
       async: false,
       data: {name: $('#recipe-name').val()},
       type: 'post',
       success: function(output) {
          if (output == "False") {
            result = true;
          } else {
            alert('Recipe with that name already exists. Please try something else.');
            $('html,body').animate({scrollTop: 100}, 500);
            $('#recipe-name').focus();
            result = false;
          }
       }
  });
  return result;
}

// jQuery code on page load
$(function () {

  // Clear all text input fields
  $('#formInput').find("input[type=text], textarea, input[type='file']").val('');

  // Set focus to first text input
  $('#recipe-name').focus();

  $('#plus-ingredient').click(function(e){
      // Stop acting like a button
      e.preventDefault();

      // Get the current value
      var currentVal = $('.ingredient-input').length;

      // If is not undefined and not above maximum number of inputs, add new input
      if (!isNaN(currentVal)) {
          if (currentVal < MAX_INPUT_FIELDS) {
            $('#ingredient1')
                    .clone()
                    .attr({'id': 'ingredient'+ (currentVal+1), 'name': 'ingredient'+ (currentVal+1)})
                    .insertAfter('[id^=ingredient]:last');
            $('[id^=ingredient]:last').children('#qty1').attr({'id': 'qty'+ (currentVal+1), 'name': 'qty'+ (currentVal+1)});
            $('[id^=ingredient]:last').children('[id^=qty]').val('');
            $('[id^=ingredient]:last').children('#unit1').attr({'id': 'unit'+ (currentVal+1), 'name': 'unit'+ (currentVal+1)});
            $('[id^=ingredient]:last').children('[id^=unit]').val('');
            $('[id^=ingredient]:last').children('#item1').attr({'id': 'item'+ (currentVal+1), 'name': 'item'+ (currentVal+1)});
            $('[id^=ingredient]:last').children('[id^=item]').val('');
          }
      }
  });

  $('#minus-ingredient').click(function(e) {
      // Stop acting like a button
      e.preventDefault();

      // Get the current value
      var currentVal = $('.ingredient-input').length;

      var newVal = 0;

      // If it is not undefined or value is greater than 1, remove input
      if (!isNaN(currentVal) && currentVal > 1) {
          $('#ingredient'+(currentVal)).remove();

      }
  });

  $('#plus-step').click(function(e){
      // Stop acting like a button
      e.preventDefault();

      // Get the current value
      var currentVal = $('.step-input').length;

      // If is not undefined and not above maximum number of inputs, add new input
      if (!isNaN(currentVal)) {
          if (currentVal < MAX_INPUT_FIELDS) {
            $('#step1')
                    .clone()
                    .attr({'id': 'step'+ (currentVal+1), 'name': 'step'+ (currentVal+1), 'placeholder': 'Step '+ (currentVal+1)})
                    .val('')
                    .insertAfter('[id^=step]:last');
          }
      }
  });

  $('#minus-step').click(function(e) {
      // Stop acting like a button
      e.preventDefault();

      // Get the current value
      var currentVal = $('.step-input').length;

      var newVal = 0;

      // If it is not undefined or value is greater than 1, remove input
      if (!isNaN(currentVal) && currentVal > 1) {
          $('#step'+(currentVal)).remove();

      }
  });

  var imgInput = document.querySelector('.img-upload');
  imgInput.addEventListener('change', updateImageDisplay);

  function updateImageDisplay() {
    var selectedFiles = imgInput.files;
    if(selectedFiles.length === 1) {
      if (selectedFiles[0].size > 2000000) {
        alert("Image file size exceeds 2MB. Please select another image.");
        imgInput.value = '';
        $('.img-preview').attr('src', 'images/image.png');
      } else {
        $('.img-preview').attr('src', window.URL.createObjectURL(selectedFiles[0]));
      }
    }
  }

});
