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


#########################
# Database configuration
#   type        = 'mysql' or 'sqlite' all lowercase
#   path        = SQLite path to CoreProtect's database.db
#   host        = MySQL database host[:port]
#   database    = MySQL database name
#   username    = MySQL username
#   password    = MySQL password
#   flags       = MySQL flags to put at the end of connection URI
#                 (don't change if you don't need to)
#   prefix      = CoreProtect prefix
'database' => [
    'type'      => 'mysql',
    'path'      => 'path/to/database.db',
    'host'      => 'localhost',
    'database'  => 'minecraft',
    'username'  => 'username',
    'password'  => 'password',
    'flags'     => '',
    'prefix'    => 'co_',
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
    'limit'         => 30,
    'moreLimit'     => 10,
    'max'           => 300,
    'pageInterval'  => 25,
    'timeDivider'   => 300,
    'locale'        => 'en-US',
    'dateFormat'    => 'll',
    'timeFormat'    => 'LTS',
],

# Navigation Bar Customization
#   add more pairs below to add more options to the navbar.
'navbar' => [
    'Home' => '/',
    #'BanManager' => '/banmanager/',
    #'Dynmap' => 'http://127.0.0.1:8123/',
],
# Copyright message
'copyright' => 'SimonOrJ, 2015-%year%',

  # Optional: Configure navigation items here.
  // For additional form configuration, visit the `config.json` file.
  //  form:
  //    dateFormat:     Date format to use based on Moment.js.
  //    timeFormat:     Time format to use based on Moment.js.
  //                      Link:  http://momentjs.com/docs/#/displaying/format/
  //    timeDividor:    Time difference between two results in seconds before
  //                      a time separator comes in.
  //    pageInterval:   Interval before a page separator is made at the bottom.
];