<?php
/**
 * Lookup PHP application
 * @author Simon Chuu
 */

require_once 'res/php/StatementPreparer.class.php';

$statement = new StatementPreparer("co2_", $_REQUEST);

var_dump($statement->preparedData());
var_dump($statement->preparedPlaceholders());



// TODO: Get information based on query


// TODO: Send the information



// ACTUAL TODO: idk scrap the above and start all over

