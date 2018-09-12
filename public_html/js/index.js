// Checks if search string includes any alphabetic characters (including
// accents, unlauts, etc) before running query
function checkFormData() {
  validSearchString = /[A-Za-z\u00C0-\u017F]/.test(
    document.getElementById('searchInput').value);
  if (!validSearchString) {
    document.getElementById('searchInput').value = "";
  }
  return validSearchString;
}

// Loads a new batch of recipes from the database, displaying HTML card divs for each
function loadMoreRecipes() {
  let countLoaded = 0;
  let cards = document.getElementsByClassName('card-link');
  for (let i = 0; i < cards.length; i++) {
    if (cards[i].getAttribute('data-image-loaded') == "false" && countLoaded < 6) {
      // Get recipe image from database in form of base64-encoded URL
      fetch('getrecipeimage.php', {
        method: "post",
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          'id': cards[i].getAttribute('href').replace('recipe.php?id=', '')
        })
      })
        .then(function (response) {
          response.json()
            .then(function (data) {
              if (data != "False") {
                cards[i].querySelector('.card').querySelector('#card-image').setAttribute('style', 'background-image: url(' + data + ')');
              }
            });
        });
      // Display card div
      cards[i].style.display = "block";
      cards[i].setAttribute('data-image-loaded', "true");
      countLoaded++;
    }
  }
  if (allCardsLoaded()) {
    document.getElementById('div-load-more').style.display = "none";
  }
}

function allCardsLoaded() {
  let visibleCards = 0;
  let totalCards = 0;
  let cards = document.getElementsByClassName('card-link');
  for (let i = 0; i < cards.length; i++) {
    if (cards[i].getAttribute('data-image-loaded') == "true") {
      visibleCards++;
    }
    totalCards++;
  }
  if (visibleCards == totalCards) {
    return true;
  } else {
    return false;
  }
}

// Code on page load
document.addEventListener("DOMContentLoaded", function (event) {
  let cards = document.getElementsByClassName('card-link');
  for (let i = 0; i < cards.length; i++) {
    if (cards[i].getAttribute('data-image-loaded') == "false") {
      cards[i].style.display = "none";
    }
  }
  if (allCardsLoaded()) {
    document.getElementById('div-load-more').style.display = "none";
  }
});