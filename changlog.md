Changes:

v0.5.3 - January 22, 2016
=========================
- `conn.php`:
 - Fixed SQL query taking exceptionally long when using MySQL to get result from more than two tables (e.g. container and chat)


v0.5.2 - January 21, 2016
=========================
- `conn.php`:
 - **Fixed SQL syntax error when using MySQL server.** Index page would just show "undefined" until now.
 - Fixed block parameter being ignored when looking up container transaction
 - Added option to translate legacy Minecraft block names to the current names
 - Improved block lookup compatibility for both old and new blocks
 - Fixed time lookup
- `index.php`:
 - Block search now requires "minecraft:" prefix or similar
 - Block search works with both legacy block names and current names.
  - Priority name will be given based on `$translateCo2Mc` configuration in settings.php.
 - Fixed exclusive search clearing out the entire request
 - Removed checkboxes in favor of checkbox buttons
 - Added container to warp around the lookup form and the table
 - Date not utilizes datetime selector by Eonasdan.


v0.5.1 - January 21, 2016
=========================
- `index.php`:
 - Fixed "undefined" upon query result
 - Fixed table style
 - Fixed username/chat/command lookup breaking the table
 - Fixed "Command" and "Session" button not working


v0.5.0 - January 20, 2016
=========================
- Started recording this changelog.md
- Revamped the entire plugin, including
 - Server Connection Script (`conn.php`)
 - Lookup Interface (`index.php`)
 - caching method (`cachectrl.php`)
- Utilizies Bootstrap for better usability
- Added ability to connect to sqlite server
 - `conn.php` now utilizes PDO instead of MySQLi
