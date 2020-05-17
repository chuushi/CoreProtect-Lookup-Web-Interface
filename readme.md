[CoreProtect Lookup Web Interface (CoLWI)](https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface)
===============================================================================
*A flexible lookup web interface for CoreProtect 2.*

![Imgur](https://i.imgur.com/gre6LpC.png)

**Version:** [v1.0.0-pre1](//github.com/chuu_shi/CoreProtect-Lookup-Web-Interface/releases/latest)

This is a _feature-packed_ web application that gives you the power to look up
anything CoreProtect is capable of logging in the most efficient way.
[CoreProtect, a Minecraft plugin,](https://www.spigotmc.org/resources/8631/)
is developed by Intellii.

This web app is capable of looking up logged data as if doing it from the game.
Some filters are ported to this plugin, such as:

* Lookup by action
* Lookup by username
* Lookup by block name
* Lookup by time

In addition, this plugin makes it possible to:

* Lookup data by coordinates and world
* View more than four results per page
* Filter out rolled back data
* ~~View what was written on the signs~~ (TBD)
* Search by keywords

# Setup

## Prerequisites

- A web server with **PHP 5.6** or above
    - Required extensions: PDO, PDO-SQLITE or PDO-MYSQL
- A MySQL (or MariaDB) or SQLite database used by a Minecraft server running
  CoreProtect 2.12 or above. (under testing)
    - If using SQLite in real-time, the web server must be on the same machine
      as the Minecraft server.

## Download

- **Option 1:** `git clone`
    - This option makes it easier to update the web app.
    - Run the following command in somewhere on the web server.
```sh
git clone https://github.com/chuushi/CoreProtect-Lookup-Web-Interface.git
```

- **Option 2:** Download
    - Download the
      [latest release `.zip` file](https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface/releases/latest).
    - Extract the .zip file somewhere on the web server.

## Configuration

Edit all the necessary configuration from `config.php`.  All fields are
documented in the configuration file.

## Updating

If you used the **option 1** to download the web app, you can run:
```sh
git stash
git pull
git stash pop
```

- `git stash` stashes uncommitted changes
- `git pull` downloads and updates the repository with the latest changes
- `git stash pop` applies the stashed changes into the repository.

If you see this error when running `git stash pop`:
```
CONFLICT (content): Merge conflict in config.php
```

then you must edit the file manually (look for `<<<<<<<`, `=======`, and
`>>>>>>>`) then run:
```sh
git add config.php
```

If you used the **option 2**, then you must re-download the `.zip` file and
manually migrate the `config.php` file over.

#[Changelog](changelog.md)

#[Contributing](contributing.md)

# Plugin Links

* [BukkitDev](//dev.bukkit.org/bukkit-plugins/coreprotect-lwi/)
* [Spigot](//www.spigotmc.org/resources/coreprotect-lookup-web-interface.28033/)

~Chuu
