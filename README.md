[CoreProtect Lookup Web Interface (CoLWI)](https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface)
===============================================================================
A flexible lookup web interface for CoreProtect 2.

**Version:**
[v0.9.2 - Beta](https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface/releases/latest)!

This is a _feature-packed_ web application that gives you the power to look up
anything CoreProtect is capable of logging in the most efficient way.
[CoreProtect, a Minecraft plugin,](http://dev.bukkit.org/bukkit-plugins/coreprotect/)
is developed by Intellii.

This plugin is capable of looking up logged data as if doing it from the game.
Some filters are ported to this plugin, such as:

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

This plugin requires a Minecraft server running CoreProtect 2.11.0+ and a
webserver running PHP 5.3.0+.  It can fetch data logged by CoreProtect through
the MySQL server or the sqlite (database.db) file.

# Setup

## Prerequisites

- Webserver running PHP 5.3.0 or above
- A MySQL or SQLite database used by a Minecraft server running
  CoreProtect 2.11 or above.
  - If using SQLite in real-time, you're advised to have the webserver run on
    the same machine as the Minecraft server.

Download the
[latest release from the releases tab](https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface/releases/latest)
or from the plugin page in dev.bukkit.org, and extract the `web/` directory
into the directory accessible by the webserver.  You may rename the directory
into something you find more useful (such as to `coreprotect/`).

If you are an advanced user, you can just do
```sh
git clone https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface.git
```
and clone this repository.

## Write Permissions

The webserver should have write permission to the `cache/` folder in order for
this web application to work efficiently.  Do this by running:
```sh
chmod 777 cache
```

If you want to be able to make configuration changes from web UI (via
`web/setup.php`):
```sh
chmod 777 config.php config.json server
```

(If you're an advanced user, you can just find a way for the webserver to have
write access to the files, or make configuration directly from those files.)

## Configuration

You **must** edit `config.php` and make account changes before you can do
anything else.  Follow the instructions in the file.  If you decided to make
all configuration manually (by editing the configuration files), then configure
the rest of the file and `config.json`.

If you are an advanced user and want to set up server information manually, you
should do so now using the `server/sample.php` and `server/sample.json` files.
The two files may be copied or renamed to better suit your needs.

# Files

## Web files
These are the main files found in the `web/` directory:

- `config.php` and `config.json` - The file made to make all general
  configuration
- `server/` - The directory that stores (multiple) server-specific information.
- `index.php` - The web page used to make queries and get results
  - `res/out-table.js` - JS script used to populate the result table
  - `res/form-handler.js` - JS script used to modify form data
- `conn.php` - PHP script used to connect the webserver to the database for
  lookup data retrieval
  - `cachectrl.php` - PHP class used for cache management
  - `bukkittominecraft.php` - PHP class used for legacy Minecraft name
    conversion

# Future Plans

There are plans to make a server-side plugin that lets players get the link to
the database based on the CoreProtect's Lookup parameters.  There also may be a
feature and permission node that allows account creation and modification as
well.

#[Changelog](changelog.md)

#[Contributing](CONTRIBUTING.md)

#[Plugin Page on Bukkit](http://dev.bukkit.org/bukkit-plugins/coreprotect-lwi/)

~Simon
