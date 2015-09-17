# CoreProtect-Web-Lookup-Interface
A flexible lookup web interface for CoreProtect 2.
Tested on CoreProtect 2.11.

**Status:** alpha.  Developing the web interface.
Skip to the [Setup](#Setup) section to see how you can set this up and test this web application.

This is a _lightweight_ web application that gives you the power to look up anything CoreProtect is capable of logging in the most efficient way.  [CoreProtect, a Minecraft plugin,](http://dev.bukkit.org/bukkit-plugins/coreprotect/) is developed by Intellii.

**Todo:**
- [X] Develop a way to connect to the CP database. [`conn.php`]
 - [X] Make output readable in JavaScript. (JSON)
- [X] Develop a way to cache CP variables to make it more efficient. [`cachectrl.php`]
 - [ ] Make a way to purge the cache.
- [ ] **Make the lookup interface.** [`index.php`]
 - [X] Develop JavaScript code to retrieve database information. (in JSON) (might be separated into `res/conn.js`)
 - [X] Develop lookup (input) form.
 - [ ] Develop output lookup table.
- [ ] Design the interface. [`res/main.css`]
- [ ] Beta testing!

If you would like to contribute to the code, you should wait until this goes into beta mode.  If there is any issues, please tell me/us about it!

## Setup
This web add-on requires a server with PHP 5.4+.  It also requires a connection from the webserver to a MySQL database the CoreProtect plugin is using.  Webserver that can read ".htaccess" file, such as Apache, is recommended for security purposes.

After getting the web app, the *first thing* to do is configure `settings.php` with your MySQL server information.  After that, rest should be automatic.

If you would like to see if you configured this correctly, you should visit the `last50.php` page and see if it successfully returns last fifty chats, commands, and block history.  The main page, `index.php`, will be released before the end of this month! (If I did not make it by September, probably by early October.)

## Files
This project consists of three main files:
- `conn.php`, the page used to connect the webserver to the database for lookup data retrieval,
- `cachectrl.php`, the code used for cache management, and
- `index.php`, the page used to make queries.

The webserver must have write permission to the `cache/` folder in order for this web application to work.
