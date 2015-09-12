# CoreProtect-Web-Lookup-Interface
A flexible lookup web interface for CoreProtect 2.11 and up

This is a _lightweight_ web application that gives you the power to look up anything CoreProtect is capable of logging in the most efficient way.  [CoreProtect, a Minecraft plugin,](http://dev.bukkit.org/bukkit-plugins/coreprotect/) is developed by Intellii.

This web add-on requires a server with PHP 5.4+.  It also requires a connection from the webserver to a MySQL database the CoreProtect plugin is using.  Webserver that can read ".htaccess" file, such as Apache, is recommended for security purposes.

## Files
This project consists of three main files:
- `conn.php`, the page used to connect the webserver to the database for lookup data retrieval,
- `cachectrl.php`, the code used for cache management, and
- `index.php`, the page used to make queries.

The webserver must have write permission to the `cache/` folder in order for this web application to work.
