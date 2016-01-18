# CoreProtect-Web-Lookup-Interface
Next update v0.5.0-alpha coming by January 20, 2016.
A flexible lookup web interface for CoreProtect 2.
Tested on CoreProtect 2.12.

**Status:** alpha.  Developing the web interface.

This is a _lightweight_ web application that gives you the power to look up anything CoreProtect is capable of logging in the most efficient way.  [CoreProtect, a Minecraft plugin,](http://dev.bukkit.org/bukkit-plugins/coreprotect/) is developed by Intellii.

This plugin requires a Minecraft server running CoreProtect plugin.  It can fetch data logged by CoreProtect through the MySQL server or the sqlite (database.db) file.

Skip to the [setup](#setup) section to see how you can set this up and test this web application.

**Todo:**
- [X] Develop a way to connect to the CP MySQL database. [`conn.php`]
 - [X] Rewrite the entire code.
- [X] Develop a way to cache CP variables to make it more efficient. [`cachectrl.php`]
- [ ] **Make the lookup interface.** [`index.php`]
 - [X] Develop JavaScript code to retrieve database information. (in JSON) (might be separated into `res/conn.js`)
 - [X] Develop lookup (input) form.
 - [ ] Develop output lookup table.
- [ ] Design the interface. [`res/main.css`]
- [ ] Beta testing!

If you would like to contribute directly to the code, you should wait until this goes into beta mode.  You may post issues if you run into any problems with the plugin.  If there is any issues, please tell me/us about it!

## Setup
This plugin requires a webserver with PHP 5.4+ and a Minecraft server running CoreProtect 2.  If the CoreProtect records data to the If the CoreProtect is set to log data to a MySQL server, this plugin needs access to the database CoreProtect is using.  Webserver that can read ".htaccess" file, such as Apache, is recommended for security purposes.

The webserver must have write permission to the `cache/` folder in order for this web application to work.

After getting the web app, the *first thing* to do is configure `settings.php` with your MySQL server information.  After that, rest should be automatic.

If you would like to see if you configured this correctly, you should visit the `last50.php` page and see if it successfully returns last fifty chats, commands, and block history.  The main page, `index.php`, will be released before the end of this month! (If I did not make it by September, probably by early October.)

## Files
This project consists of three main files:
- `conn.php`, the page used to connect the webserver to the database for lookup data retrieval,
- `cachectrl.php`, the code used for cache management, and
- `index.php`, the web page used to make queries and get results.
