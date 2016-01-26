Changes:
================================================================================

v0.6.1 - January 26, 2016
-------------------------


v0.6.0 - January 25, 2016
-------------------------
- No Javascript Update - The v0.6 will focus on improving the lookup UI's look
  and usability.
 - It will focus on making the lookups possible without JavaScript friendly for
   those who doesn't like using JavaScript.
   - However, those using this with JavaScript turned on will benefit much more
     from this update!
- Feature/bug fixes:
 - `settings.php`:
  - Separated PDO part to another file `PDO.php` in order to make `index.php`
    load faster.
  - Set PDO mysql charset to UTF-8 for special character compatibility
  - Added global date and time formatting option
 - `conn.php`:
  - Modified the way requests are handled.
  - Added support for searching with current Minecraft block/item names
   - Searching with legacy names work as long as it isn't overriden by new names.
 - `index.php`:
  - An error will be thrown for unconfigured or badly configured database
    connect settings.
  - Table size has been decreased and colors has been added.
  - Time/date display method has been overhauled into using moment.js.
  - Time can be selected from the table list to put it in the search form.
  - Request duration time has been added on the bottom of the table so you can
    feel like Google!
  - Coordinate defining and date/limit fields are combined to save room when the
    window is large enough.
  - "Load more" has been styled to bootstrap.
  - Form submitting now uses browser's built-in handling rather than by
    unnecessary javascript.
  - Now submits request with POST rather than GET.


v0.5.3 - January 22, 2016
-------------------------
- `conn.php`:
 - **Fixed coordinate search not working** (xyz, xyz2/r)
 - Fixed SQL query taking exceptionally long when using extremely large database
   to get result from more than two tables (e.g. container and chat)
 - Improved table formatting
- `index.php`:
 - Added rollback search
 - Added colors for Amount column
 - Changed second "action" column name to "amount"
 - Added clearer coordinate search
 - Added toggle to visually toggle coordinate/radius search


v0.5.2 - January 21, 2016
-------------------------
- `conn.php`:
 - **Fixed SQL syntax error when using MySQL server.** Index page would just
   show "undefined" until now.
 - Fixed block parameter being ignored when looking up container transaction
 - Added option to translate legacy Minecraft block names to the current names
 - Improved block lookup compatibility for both old and new blocks
 - Fixed time lookup
- `index.php`:
 - Block search now requires "minecraft:" prefix or similar
 - Block search works with both legacy block names and current names.
  - ~~Priority name will be given based on `$translateCo2Mc` configuration in
    settings.php.~~
 - Fixed exclusive search clearing out the entire request
 - Removed checkboxes in favor of checkbox buttons
 - Added container to warp around the lookup form and the table
 - Date not utilizes datetime selector by Eonasdan.


v0.5.1 - January 21, 2016
-------------------------
- `index.php`:
 - Fixed "undefined" upon query result
 - Fixed table style
 - Fixed username/chat/command lookup breaking the table
 - Fixed "Command" and "Session" button not working


v0.5.0 - January 20, 2016
-------------------------
- Started recording this changelog.md
- Revamped the entire plugin, including
 - Server Connection Script (`conn.php`)
 - Lookup Interface (`index.php`)
 - caching method (`cachectrl.php`)
- Utilizies Bootstrap for better usability
- Added ability to connect to sqlite server
 - `conn.php` now utilizes PDO instead of MySQLi