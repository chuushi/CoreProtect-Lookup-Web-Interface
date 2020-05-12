<?php return [
/*
 * NOTICE: Configuration file will be overwritten if you save any
 * configuration changes from the web dashboard.  Web configuration is
 * accessible through administrator account only via local or HTTPS
 * connection.
 */

########################
# Account Configuration
# Administrator account can manage configuration from the web.
# Username: administrator
# set password to enable access.
'administrator' => '',

# User account to access lookup.
# Username: user
# set password to require log in to use the lookup.
'user' => '',


################################
# Database/Server configuration
# If you have multiple databases, configure each database source here by
# copying 'server' array right under and renaming 'server' to a different
# server name.
#   type        = 'mysql' or 'sqlite' all lowercase
#   path        = SQLite path to CoreProtect's database.db
#   host        = MySQL database host[:port]
#   database    = MySQL database name
#   username    = MySQL username
#   password    = MySQL password
#   flags       = MySQL flags to put at the end of connection URI
#                 (don't change if you don't need to)
#   prefix      = CoreProtect prefix
#   mapLink     = Link to view on the map. (Dynmap, Overviewer, etc.)
#                 %world% for world; %x% %y% %z% for xyz coordinates
'database' => [
    'server' => [
        'type'      => 'mysql',
        'path'      => 'path/to/database.db',
        'host'      => 'localhost:3306',
        'database'  => 'minecraft',
        'username'  => 'username',
        'password'  => 'password',
        'flags'     => '',
        'prefix'    => 'co_',
        'mapLink'   => 'https://localhost:8123/?worldname=%world%&mapname=surface&zoom=3&x=%x%&y=%y%&z=%z%'
    ],
],

########################
# Website Configuration

# Form Configuration
#   limit           = default lookup query limit
#   moreLimit       = default "load more" query limit
#   max             = maximum limit for single query
#   pageInterval    = how many entries to divide the pagination by
#   timeDivider     = how many entries the table displays before the interval shows up
#   locale          = Date locale (website locale coming soon?)
#   dateFormat      = Date format (see https://momentjs.com/docs/#/parsing/string-format/)
#   timeFormat      = Time format (see ^)
'form' => [
    'count'         => 30,
    'moreCount'     => 10,
    'max'           => 300,
    'pageInterval'  => 25,
    'timeDivider'   => 300,
    'dateTimeFormat'=> 'll LTS'
],

# Webpage name and style configuration
#   bootstrap = Link to a bootstrap swatch, local or from CDN.
#               If from a CDN, using the HTML <link> with
#               'integrity' and 'crossorigin' is recommended!
#               (you can get link to theme from:)
#               https://www.bootstrapcdn.com/bootswatch/
#   darkInput = if the bootstrap theme is a dark theme
#               (Affects the color of input fields)
#   name      = Page name
#   href      = link where the page name leads to
'page' => [
    'bootstrap' => '<link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-G9YbB4o4U6WS4wCthMOpAeweY4gQJyyx0P3nZbEBHyz+AtNoeasfRChmek1C2iqV" crossorigin="anonymous">',
    'darkInput' => true,
    'name'      => 'CoreProtect Lookup Web Interface',
    'href'      => '/'
],

# Navigation Bar Customization
#   add more pairs below to add more links to the navbar.
'navbar' => [
    'Home' => 'index.php',
    #'BanManager' => '/banmanager/',
    #'Dynmap' => 'http://127.0.0.1:8123/',
]
];