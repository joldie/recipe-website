var MAX_SERVES = 12;

var originalNumberServes = 0;
var originalIngredientQtys = [];

function getOriginalNumberServes() {
  originalNumberServes = document.getElementById('serves').innerHTML;
}

function getOriginalIngredientQtys() {
  var listItems = document.querySelectorAll("#ingredients-list li");
  listItems.forEach(function (li, index) {
    originalIngredientQtys.push(
      li.innerHTML.substring(0, li.innerHTML.indexOf(' ')));
  });
}

function updateIngredientQtys(original_num_serves, new_num_serves) {
  var listItems = document.querySelectorAll("#ingredients-list li");
  listItems.forEach(function (li, index) {
    var newVal = "";
    var oldVal = li.innerHTML.substring(0, li.innerHTML.indexOf(' '));
    if (oldVal > 0) {
      newVal = originalIngredientQtys[index] *
        new_num_serves / original_num_serves;
      newVal = Number(newVal.toPrecision(3)).toString();
    }
    li.innerHTML = newVal +
      li.innerHTML.substring(li.innerHTML.indexOf(' '), li.innerHTML.length);
  });
}

function showLoadingScreen() {
  document.querySelector('.full-page-loading-canvas').style.display = 'block';
}

function hideLoadingScreen() {
  document.querySelector('.full-page-loading-canvas').style.display = 'none';
}

function getRecipeIdFromUrl() {
  var url = document.URL;
  id = url.substring(url.indexOf("=") + 1);
  return id;
}

function confirmDelete() {
  var returnValue = confirm('WARNING: Are you sure you want to permanently delete this recipe?');
  if (returnValue == true) {
    // Show loading screen in case query takes long time
    showLoadingScreen();
    // Delete recipe with given ID in DB using AJAX request
    fetch('deleterecipe.php', {
      method: "post",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        'id': getRecipeIdFromUrl()
      })
    })
      .then(function (response) {
        response.json()
          .then(function (data) {
            if (data == "True") {
              hideLoadingScreen();
              alert('Recipe deleted.');
              window.location.href = "index.php";
            }
          });
      });
  }
}

// Code on page load
document.addEventListener("DOMContentLoaded", function (event) {

  getOriginalNumberServes();
  getOriginalIngredientQtys();

  document.getElementById('plus').addEventListener('click', function (e) {
    // Stop acting like a button
    e.preventDefault();

    // Get the current value
    var currentVal = parseInt(document.getElementById('serves').innerHTML);

    var newVal = 0;

    // If is not undefined and not above maximum number of serves, increment
    if (!isNaN(currentVal)) {
      if (currentVal < MAX_SERVES) {
        newVal = currentVal + 1;
        document.getElementById('serves').innerHTML = newVal;
      }
      else {
        newVal = MAX_SERVES;
      }
    } else {
      // Otherwise set value to 1
      newVal = 1;
      document.getElementById('serves').innerHTML = newVal;
    }
    updateIngredientQtys(originalNumberServes, newVal);
  });

  document.getElementById('minus').addEventListener('click', function (e) {
    // Stop acting like a button
    e.preventDefault();

    // Get the current value
    var currentVal = parseInt(document.getElementById('serves').innerHTML);

    var newVal = 0;

    // If it is not undefined or value is greater than 1, decrement
    if (!isNaN(currentVal) && currentVal > 1) {
      newVal = currentVal - 1;
      document.getElementById('serves').innerHTML = newVal;

    } else {
      // Otherwise set value to 1
      newVal = 1;
      document.getElementById('serves').innerHTML = newVal;
    }
    updateIngredientQtys(originalNumberServes, newVal);
  });

  document.getElementById('edit-button-link')
    .setAttribute('href', 'editrecipe.php?id=' + getRecipeIdFromUrl());

});
