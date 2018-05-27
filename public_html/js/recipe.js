var MAX_SERVES = 12;

var originalNumberServes = 0;
var originalIngredientQtys = [];

function getOriginalNumberServes(){
  originalNumberServes = document.getElementById('serves').innerHTML;
}

function getOriginalIngredientQtys(){
  var listItems = $("#ingredients-list li");
  listItems.each(function(idx, li) {
    originalIngredientQtys.push($(li).text().substring(0,$(li).text().indexOf(' ')));
  });
}

function updateIngredientQtys(original_num_serves, new_num_serves){
  var listItems = $("#ingredients-list li");
  listItems.each(function(idx, li) {
    var oldVal = $(li).text().substring(0,$(li).text().indexOf(' '));
    var newVal = originalIngredientQtys[idx] * new_num_serves / original_num_serves;
    newVal = Number(newVal.toPrecision(3)).toString();
    $(li).text(newVal + $(li).text().substring($(li).text().indexOf(' '), $(li).text().length));
  });
}

function getRecipeIdFromUrl(){
  var url = document.URL;
  id = url.substring(url.indexOf("=") + 1);
  return id;
}

function confirmDelete(){
  var returnValue = confirm('WARNING: Are you sure you want to permanently delete this recipe?');
  if ( returnValue == true ) {
    $.ajax({ url: 'deleterecipe.php',
         async: false,
         data: {id: getRecipeIdFromUrl()},
         type: 'post',
         success: function(output) {
            if (output == "True") {
              alert('Recipe deleted.');
              window.location.href = "index.php";
            }
         }
    });
  }
}

// Code on page load
document.addEventListener("DOMContentLoaded", function(event) {

  getOriginalNumberServes();
  getOriginalIngredientQtys();

  $('#plus').click(function(e){
      // Stop acting like a button
      e.preventDefault();

      // Get the current value
      var currentVal = parseInt($('#serves').text());

      var newVal = 0;

      // If is not undefined and not above maximum number of serves, increment
      if (!isNaN(currentVal)) {
          if (currentVal < MAX_SERVES) {
            newVal = currentVal + 1;
            $('#serves').text(newVal);
          }
          else {
            newVal = MAX_SERVES;
          }
      } else {
          // Otherwise set value to 1
          newVal = 1;
          $('#serves').text(newVal);
      }
      updateIngredientQtys(originalNumberServes, newVal);
  });

  $('#minus').click(function(e) {
      // Stop acting like a button
      e.preventDefault();

      // Get the current value
      var currentVal = parseInt($('#serves').text());

      var newVal = 0;

      // If it is not undefined or value is greater than 1, decrement
      if (!isNaN(currentVal) && currentVal > 1) {
          newVal = currentVal - 1;
          $('#serves').text(newVal);

      } else {
          // Otherwise set value to 1
          newVal = 1;
          $('#serves').text(newVal);
      }
      updateIngredientQtys(originalNumberServes, newVal);
  });

  $('#edit-button-link').attr('href', 'editrecipe.php?id=' + getRecipeIdFromUrl());

});
