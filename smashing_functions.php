<?php

/*
 * A function library to interface to Smashing using PHP instead of Ruby.
 * This file must be placed in the "jobs" folder of a Smashing
 * installation.
 */

// Set the auth token as a superglobal
$auth_token = "YOUR_TOKEN_HERE";

// Set the Smashing URL to POST the data to.
$smashing_url = "http://YOUR_SMASHING_SERVER_HERE:3030/widgets/";

// Set up a cache database table.
$cachedb = mysqli_connect('localhost', 'smashing', '', 'smashing');
mysqli_set_charset($cachedb, 'utf8mb4');

// Create this table using:
/* CREATE TABLE `cache_table` (
 * `widget` varchar(255) NOT NULL,
 * `json` mediumtext NOT NULL
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 * ALTER TABLE `cache_table`
 * ADD PRIMARY KEY (`widget`)
 */

// A function to send the data to Smashing (comes in as JSON formatted string)
function send_event($widget, $json_data, $force=false, $bypass=false) {
  global $cachedb, $auth_token, $smashing_url;

  $json_obj = json_decode($json_data);
  // Check to make sure it decodes
  if ( $json_obj === null ) {
    die('JSON did not decode properly');
  }
  // Add the auth token to the object
  $json_obj->auth_token = $auth_token;
  // Re-encode it, replacing % with a more appropriate sequence.
  $postfields = str_replace('%','\u0025', json_encode($json_obj));

  // Check to see if this data is exactly the same as what was sent last interval.
  if ( isset($cachedb) && ! $bypass ) {
    $result=mysqli_query($cachedb, "SELECT json FROM cache_table WHERE widget = '" . $widget . "'");
    if ( mysqli_fetch_assoc($result)['json'] != $postfields || $force ) {
      mysqli_query($cachedb, "REPLACE INTO cache_table VALUES ('" . $widget . "','" . mysqli_real_escape_string($cachedb, $postfields) . "')");
    } else {
      return;
    }
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $smashing_url . $widget);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
  if (!($response = curl_exec($ch))) {
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
  }
  curl_close($ch);
}

// A function to pull the JSON data from the database and return it.
function get_cached_json($widget) {
  global $cachedb;

  if ( isset($cachedb) ) {
    $result=mysqli_query($cachedb, "SELECT json FROM cache_table WHERE widget = '$widget'");
    if ( $result ) {
      return mysqli_fetch_row($result)[0];
    } else {
      return false;
    }
  } else {
    return false;
  }
}

?>
