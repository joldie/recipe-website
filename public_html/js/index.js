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
