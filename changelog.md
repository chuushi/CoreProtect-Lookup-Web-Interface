Changelog:
===============================================================================

v0.9.3 - August 24, 2016
------------------------
- Enhanced Feature:
  - Time can be localized easily now.
- Fixes:
  - Webservers running PHP 5.4 and below wasn't able to use this tool due to an
    error.  This now supports 5.4 properly.
  - "Cache" directory is now included in the project itself.


v0.9.2 - August 17, 2016
------------------------
- New feature:
  - Now using minecraft.woff and a sign image to display sign data to better
    replicate the sign in-game.
  - Making a lookup will update the URL to make the output look the same even
    when you give the link to someone else and they make a lookup with it.
- Fix:
  - Form labels has been centered again.
  - Container option is fixed.
  - The date ordering for multiple action search is fixed.
- Code:
  - Removed bloaty comments.
  - Disabled test scripts.
- Git Repository-specific
  - Removed the entire "theme" folder.  This is now clone-friendly.
  - Restyle `README.md` and this file to fit in 80-character width consoles and
    for better bullet points.
- Technical:
  - Merged `form-handler.js` and `out-table.js` into `main.js`.


v0.9.1 - August 17, 2016
------------------------
- Optimization
 - GOOD GRACIOUS THERE WAS AN "ORDER BY ROWID" OPTION???!??!!
  - The lookup has been optimized infinitely by sorting results by rowid
    instead of by time.
- Fix:
  - Loading more after time is set (or not while server is being too active)
    will not create any duplicate data anymore.


v0.9.0 - August 16, 2016
------------------------
- New Features:
  - You can make a lookup from multiple servers/databases! #2
  - You can make configuration changes using the web browser.
  - You can make multiple accounts with varying permissions. (#3) Those include:
    - Full permission
    - Cache Purge Permission
    - Usage Permission
- Fixes:
  - Load More has been re-enabled without any possible SQL injection problems.
  - Fixed "minecraft:" not appearing on old lookup block data.
- Technical:
  - Login script was re-written into a separate class.
    - Login script and login page are now separated.
  - PDO connection is now a function file.
  - JavaScript files have been re-written.
    - They are surrounded by an anonymous function to prevent possible global
      variable overwrites and allow cleaner codes.
    - Uses better use of jQuery.
  - Uses more-standard file, class, variable and function naming.
  - Fixes name for "co2mc".  CoreProtect actually uses Bukkit block names for
    old blocks.  Renamed to "BukkitToMinecraft" class.
- **Configuration must be re-made in order to use this!**  This is not
  compatible with `settings.php` file anymore.

  
v0.8.2.1 - August 7, 2016
-------------------------
- Patch:
  - Disabled possible SQL injection by temporary disabling Load More support.
    - This will be fixed in the coming days.
- Rewrote `conn.php`
- Moved around all the files.
- This update aims to make it possible for people to collaborate.


v0.8.2 - February 22, 2016
--------------------------
- Patch:
  - The SQL injection is made harder by encrypting the SQL term itself.


v0.8.1 - February 11, 2016
--------------------------
- Fix:
  - Corner 2 has been fixed.
  - Bukkit-specific:
    - Cache folder has been cleared of scrap data that was accidently included in
      the v0.8.0-beta release.
- Interface:
  - Advanced dropdown menu has been added with an option to clear the cache.
- Backend:
  - Simple script to clear cache was added: `purge.php`


v0.8.0 - February 10, 2016
--------------------------
- Major Fix:
  - Fixed database.db file in settings not pointing to the path defined in the
   settings.php.
- Interface:
  - Added smooth scrolling to anchor link (courtesy of 
    [mattsince87](http://codepen.io/mattsince87/pen/exByn)).
  - Added result index.
  - Added a way to add links to the navbar through settings.php.
  - Moved scrollspy navbar to the bottom for ease.
    - To Fix: make the bar scrollable in one way or another. (Any suggestions?)
  - Fixed "Kill" button.
  - Made searching by keyword more versatile.
  - Added tooltips to aid in meaning of some searches.
  - Index now alerts you when the `cache/` is not writable.
  - Increased "Load More" default value to from 10 to 30.
  - `out-table.js` - error handling code has been modified to fix display style.
- Backend:
  - Keyword search has been improved.
  - Modified the block "minecraft:bed" to "minecraft:bed(block)" to avoid
    confusion with the item "minecraft:bed".
  - `login.php` - code has been optimized.


v0.7.0 - February 7, 2016
-------------------------
- `index.php`
  - The page now has a Navigation bar!  When you go over certain number of
   pages, the page link will pop up dynamically on the navbar!
  - Added optional logon screen (created by
   [richcheting](http://www.ricocheting.com/code/php/simple-login)).
  - Separated JavaScript from index.php to res/out-table.js.
    - Modified JavaScript to strict format.
  - Dropdown menu for coordinates now comes with optional Dynmap link.
  - Adding to user or block from dropdown menu now appends to the lookup form.
  - Autocomplete feature has been added.  The script will take data from cache
    to send suggestion texts.
  - Keyword search has been re-enabled.
  - Most errors are responsive.  If the script did not go through completely,
    the error will come up on the table in place of the data.
- `conn.php`
  - Code for keyword search is now added.
- Some scripts are minified.
- The plugin is now available on the
  [Bukkit Dev page](http://dev.bukkit.org/bukkit-plugins/coreprotect-lwi/).


v0.6.1 - January 27, 2016
-------------------------
- `conn.php`
  - Searching by world alone is possible.
  - Coordinate search has been fixed (if it was broken before).
- `index.php`
  - Table header color has been changed.
  - Time, username, coordinate, and block terms can now be picked directly from
    the results and placed directly into the search field in a few clicks!
    - The four fields specified has a dropdown menu that allows such.
    - Block and user fields will get some modification.
  - You can now lookup what was written on the sign!
    - This feature is valid only if the blocks "minecraft:wall_sign" or
      "minecraft:standing_sign" has a valid sign history.  Signs generated
      using, for example, worldedit might not come out correctly. (untested)
  - World field has been added.


v0.6.0 - January 25, 2016
-------------------------
- No Javascript Update - The v0.6 will focus on improving the lookup UI's look
  and usability.
  - It will focus on making the lookups possible without JavaScript friendly
    for those who doesn't like using JavaScript.
     - However, those using this with JavaScript turned on will benefit much
       more from this update!
- Feature/bug fixes:
  - `settings.php`:
    - Separated PDO part to another file `PDO.php` in order to make `index.php`
      load faster.
    - Set PDO mysql charset to UTF-8 for special character compatibility
    - Added global date and time formatting option
  - `conn.php`:
    - Added a way to get sign data!
    - Modified the way requests are handled.
    - Added support for searching with current Minecraft block/item names
      - Searching with legacy names work as long as it isn't overriden by new
        names.
  - `index.php`:
    - An error will be thrown for unconfigured or badly configured database
      connect settings.
    - Table size has been decreased and colors has been added.
    - Time/date display method has been overhauled into using moment.js.
    - Time can be selected from the table list to put it in the search form.
    - Request duration time has been added on the bottom of the table so you
      can feel like Google!
    - Coordinate defining and date/limit fields are combined to save room when
      the window is large enough.
    - Added sign results. It's not working properly yet.
    - "Load more" has been styled to bootstrap.
    - Form submitting now uses browser's built-in handling rather than by
      unnecessary javascript.
    - Now submits request with POST rather than GET.


v0.5.3 - January 22, 2016
-------------------------
- `conn.php`:
  - **Fixed coordinate search not working** (xyz, xyz2/r)
  - Fixed SQL query taking exceptionally long when using extremely large
    database to get result from more than two tables (e.g. container and chat)
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
