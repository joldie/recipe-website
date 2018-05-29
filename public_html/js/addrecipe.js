var MAX_INPUT_FIELDS = 20;

function checkFormData() {
  // Check if there is a recipe in DB with the same name
  fetch('recipeindb.php', {
    method: "post",
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      'name': document.getElementById('recipe-name').value
    })
  })
  .then( function(response) {
    response.json()
    .then(function(data) {
      if (data == "True") {
        alert('Recipe with that name already exists. Please try something else.');
        document.getElementById('recipe-name').focus();
        window.scrollTo({ top: 0, behavior: "smooth" });
      } else {
        callBack();
      }
    });
  });
}

function callBack() {
  // Submit form only once AJAX call has completed successfully
  document.getElementById('formInput').submit();
}

function updateImageDisplay() {

  var imgInput = document.querySelector('.img-upload');
  var imgPreview = document.querySelector('.img-preview');
  var selectedFiles = imgInput.files;

  if(selectedFiles.length === 1) {
    if (selectedFiles[0].size > 2000000) {
      alert("Image file size exceeds 2MB. Please select another image.");
      imgInput.value = '';
      imgPreview.setAttribute('src', 'images/image.png');
    } else {
      imgPreview.setAttribute('src', window.URL.createObjectURL(selectedFiles[0]));
    }
  }

}

// Code on page load
document.addEventListener("DOMContentLoaded", function(event) {

  // Clear all text input fields
  document.getElementById('formInput').querySelectorAll("input[type=text], textarea, input[type='file']").value = "";

  document.getElementById('plus-ingredient').addEventListener('click', function(e) {
    // Stop acting like a button
    e.preventDefault();

    // Get the current number of fields
    var numberInputFields =
      document.querySelectorAll('.ingredient-input').length;

    // If is not undefined and not above maximum number of inputs,
    // add new input
    if (!isNaN(numberInputFields) && numberInputFields < MAX_INPUT_FIELDS) {

      var firstNode = document.getElementById('ingredient1');
      var newNode = firstNode.cloneNode(true);
      var newName = 'ingredient' + (numberInputFields + 1);
      newNode.setAttribute('id', newName);
      newNode.setAttribute('name', newName);
      firstNode.parentNode.insertBefore(newNode, null); // Insert at end

      document.getElementById(newName).children[0].setAttribute('id', 'qty' + (numberInputFields + 1));
      document.getElementById(newName).children[0].setAttribute('name', 'qty' + (numberInputFields + 1));
      document.getElementById(newName).children[0].value = '';
      document.getElementById(newName).children[1].setAttribute('id', 'unit' + (numberInputFields + 1));
      document.getElementById(newName).children[1].setAttribute('name', 'unit' + (numberInputFields + 1));
      document.getElementById(newName).children[1].value = '';
      document.getElementById(newName).children[2].setAttribute('id', 'item' + (numberInputFields + 1));
      document.getElementById(newName).children[2].setAttribute('name', 'item' + (numberInputFields + 1));
      document.getElementById(newName).children[2].value = '';
    }
  });

  document.getElementById('minus-ingredient').addEventListener('click', function(e) {
    // Stop acting like a button
    e.preventDefault();

    // Get the current number of fields
    var numberInputFields =
      document.querySelectorAll('.ingredient-input').length;

    var newVal = 0;

    // If it is not undefined or value is greater than 1, remove input
    if (!isNaN(numberInputFields) && numberInputFields > 1) {
        document.getElementById('ingredient' + (numberInputFields)).remove();

    }
  });

  document.getElementById('plus-step').addEventListener('click', function(e) {
    // Stop acting like a button
    e.preventDefault();

    // Get the current number of fields
    var numberInputFields =
      document.querySelectorAll('.step-input').length;

    // If is not undefined and not above maximum number of inputs,
    // add new input
    if (!isNaN(numberInputFields) && numberInputFields < MAX_INPUT_FIELDS) {
      var firstNode = document.getElementById('step1');
      var newNode = firstNode.cloneNode(true);
      var newName = 'step' + (numberInputFields + 1);
      newNode.setAttribute('id', newName);
      newNode.setAttribute('name', newName);
      newNode.setAttribute('placeholder', 'Step '+ (numberInputFields+1));
      newNode.value = '';
      firstNode.parentNode.insertBefore(newNode, null); // Insert at end
    }
  });

  document.getElementById('minus-step').addEventListener('click', function(e) {
    // Stop acting like a button
    e.preventDefault();

    // Get the current number of fields
    var numberInputFields =
      document.querySelectorAll('.step-input').length;

    var newVal = 0;

    // If it is not undefined or value is greater than 1, remove input
    if (!isNaN(numberInputFields) && numberInputFields > 1) {
        document.getElementById('step' + (numberInputFields)).remove();
    }
  });

  document.getElementById('discard').addEventListener('click', function(e) {
    // Stop acting like a button
    e.preventDefault();
    // Return to homepage
    window.location.replace('index.php');
  });

  document.getElementById('formInput').addEventListener('submit', function (e) {
    // Prevent default behaviour
    e.preventDefault();
    // Check user input data with AJAX call before submitting form
    checkFormData();
  });

  // Check user-uploaded image and update display
  document.querySelector('.img-upload').addEventListener('change', updateImageDisplay);

  // Set focus to first text input
  document.getElementById('recipe-name').focus();

});
