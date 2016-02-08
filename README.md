CoreProtect Lookup Web Interface (CoLWI)
========================================
A flexible lookup web interface for CoreProtect 2.

**Status:** Beta!

This is a _lightweight_ web application that gives you the power to look up anything CoreProtect is capable of logging in the most efficient way.  [CoreProtect, a Minecraft plugin,](http://dev.bukkit.org/bukkit-plugins/coreprotect/) is developed by Intellii.

This plugin is capable of looking up logged data as if doing it from the game. Some filters are ported to this plugin, such as:

* Lookup by action
* Lookup by username
* Lookup by block name
* Lookup by time

In addition, this plugin makes it possible to:

* Lookup data by coordinates and world
* View more than four results per page
* Filter out rolled back data
* View what was written on the signs
* Search by keywords

This plugin requires a Minecraft server running CoreProtect 2.11+ and a webserver running PHP 5.4+.  It can fetch data logged by CoreProtect through the MySQL server or the sqlite (database.db) file.

If you would like to contribute directly to the code and know how git works, you may do so (and maybe teach me more about collaborating using git, if you will).  If there is any issues, please tell me about it!

## Setup
*Prerequisites:*

- Webserver running PHP 5.4 or above
- Minecraft server running CoreProtect 2.11 or above.
- A MySQL or SQLite database.
 - If using SQLite, you're advised to have the webserver run on the same machine as the Minecraft server.

Download one of the releases from the releases tab or from the plugin page in dev.bukkit.org, and extract the zipped file into the directory accessible by the webserver.

The webserver must have write permission to the `cache/` folder in order for this web application to work.  Do this by running:
`$ chmod 777 cache`
or any equivalent of this from other machines from this plugin directory.

After the initial setup, configure your MySQL server or sqlite `database.db` file path information and other auxiliary settings by editing the `settings.php` file.  After that, rest should be automatic.


## Files
This project consists of five main files:

- `settings.php` - The file made to make all configuration
 - `res/login.php` - PHP script used to open up login page for the php files that includes `settings.php`
- `index.php` - The web page used to make queries and get results
 - `res/out-table.js` - JS script used to populate the result table
 - `res/form-handler.js` - JS script used to modify form data
- `conn.php` - PHP script used to connect the webserver to the database for lookup data retrieval
 - `cachectrl.php` - PHP class used for cache management
 - `co2mc.php` - PHP class used for legacy Minecraft name conversion

# [Changelog](changelog.md)

# [Plugin Page on Bukkit](http://dev.bukkit.org/bukkit-plugins/coreprotect-lwi/)

~Simon
