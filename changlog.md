Changes:

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
