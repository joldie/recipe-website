# To Do

## High priority
- [ ] Add bugs as GitHub issues
- [ ] Add tags: (addrecipe.php, recipe.php) include input/display fields for lowercase tag names (branch: add-tags-input)
- [ ] CSS: Improve display on mobile phone, other browsers (e.g. input field sizes, styles). Need to reduce font size for small screens?
- [ ] Performance: Implement image resizing/optimization direct on server, instead of external API
- [ ] UX: (addrecipe.php, editrecipe.php) ability to delete arbitrary ingredient/step, not just the last one

## Medium priority
- [ ] Code cleanup: Add comments to code, in particular functions in separate files
- [ ] Code cleanup: remove unnecessary IDs from ingredients, steps? qty1, etc...
- [ ] UX: (index.php) add more recipes to page dynamically as user scrolls down (test with showing 6 first, instead of 12)
- [ ] Bug: (CSS) new ingredient/step input fields expands width of div
- [ ] Refactor: move recipeindb, deleterecipe to resources/library
- [ ] Refactor: make insertrecipe.php, updaterecipe.php functions in library
- [ ] Test viewing on variety of browsers
- [ ] UX: (addrecipe.php) ingredient unit dropdown box
- [ ] UX: (addrecipe.php, editrecipe.php) ability to move up and down (maybe drag with mouse/finger) position of ingredients or steps in list

## Low priority
- [ ] UX: Button to rotate uploaded photos 90°
- [ ] UX: (index.php) enable quotes "" and wildcards * in search, search for parts of words
- [ ] UX: show suggestions for tags, based on previously used names
- [ ] CSS: highlight/underline menu links on hover
- [ ] UX: loading screen turns to tick for successful query, etc (instead of alert box)
- [ ] Perform Lighthouse audit via Chrome

## Future features
- Publish site in German, also. Add ability in code to switch language between EN/DE
