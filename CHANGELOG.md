CHANGELOG
=========

1.3
---

 * Replaced `text` input with `textarea` for `keywords` attribute
 * Fixed issue with quote escaping
 * Added `getChildAlbums()` in site service object
 * Changed the way of storing configuration data. Since now its stored in the database
 * Added `getImageUrl()` shortcuts in entities
 * Merged several fetching methods into one `fetchAll()`
 * Removed `fetchAllPublihedByPage()` in `PhotoMapper`
 * Merged `fetchAllByAlbumIdAndPage()` and `fetchAllPublishedByAlbumIdAndPage()` with `fetchAllByPage()`
 * Added missing highlighter for active album in the grid
 * Switched to two columns view
 * Changed module icon
 * Added additional "Go home" item to reset album filter
 * Improved internal structure

1.2
---

 * Improved internals

1.1
---

 * Added zoom support when viewing a grid

1.0
---

 * First public version