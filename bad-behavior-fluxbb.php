<?php
/*
http://www.bad-behavior.ioerror.us/

Bad Behavior - detects and blocks unwanted Web accesses
Copyright (C) 2005 Michael Hampton

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
*/

// This file is the entry point for Bad Behavior.

if (!defined('PUN_ROOT')) exit;

define('BB2_CWD', dirname(__FILE__));

// Settings you can adjust for Bad Behavior.
// Most of these are unused in non-database mode.
$bb2_settings_defaults = array(
	'log_table' => $db->prefix.'bad_behavior',
	'display_stats' => true,
	'strict' => false,
	'verbose' => false,
	'logging' => true,
	'httpbl_key' => '',
	'httpbl_threat' => '25',
	'httpbl_maxage' => '30',
	'offsite_forms' => false,
);

// Bad Behavior callback functions.
require_once("bad-behavior-mysql.php");

// Return current time in the format preferred by your database.
function bb2_db_date() {
	return gmdate('Y-m-d H:i:s');	// Example is MySQL format
}

// Return affected rows from most recent query.
function bb2_db_affected_rows($result) {
	global $db;
	return $db->affected_rows();
}

// Escape a string for database usage
function bb2_db_escape($string) {
	global $db;
	return $db->escape($string);
}

// Return the number of rows in a particular query.
function bb2_db_num_rows($result) {
	global $db;
	return $db->num_rows($result);
}

// Run a query and return the results, if any.
// Should return FALSE if an error occurred.
// Bad Behavior will use the return value here in other callbacks.
function bb2_db_query($query) {
	global $db;
	return $db->query($query);
}

// Return all rows in a particular query.
// Should contain an array of all rows generated by calling mysql_fetch_assoc()
// or equivalent and appending the result of each call to an array.
function bb2_db_rows($result) {
	global $db;
	$return = array();
	while ($data = $db->fetch_assoc($result))
		$return[] = $data;
		
	return $return;
}

// Return emergency contact email address.
function bb2_email() {
	global $pun_config;
	return $pun_config['o_webmaster_email'];
}

// retrieve settings from database
// Settings are hard-coded for non-database use
function bb2_read_settings() {
	global $bb2_settings_defaults, $pun_config, $db;

	// It's installed
	if (isset($pun_config['o_badbehavior_display_stats']))
	{
		return array_merge($bb2_settings_defaults, array(
			'log_table' => $db->prefix.'bad_behavior', 
			'display_stats' => (bool)$pun_config['o_badbehavior_display_stats'],
			'verbose' => (bool)$pun_config['o_badbehavior_verbose'],
			'strict' => (bool)$pun_config['o_badbehavior_strict'],
			'is_installed' => true,
		));
	}
	else
		return $bb2_settings_defaults;
}

// write settings to database
function bb2_write_settings($settings, $install = false) {
	global $db;

	while (list($key, $input) = @each($settings))
	{
		if ($key == 'log_table')
			continue;

		$input = intval($input);
	
		if ($install)
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_badbehavior_'.$db->escape($key).'\', '.$input.')') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		else
			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$input.' WHERE conf_name=\'o_badbehavior_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
	}
	
	// Regenerate the config cache
	require_once PUN_ROOT.'include/cache.php';
	generate_config_cache();
}

// installation
function bb2_install() {
	$settings = bb2_read_settings();
	if (!isset($settings['is_installed']))
	{
		bb2_db_query(bb2_table_structure($settings['log_table']));
		bb2_write_settings($settings, true);
	}
}

// Display stats?
function bb2_insert_stats($force = false) {
	global $bb2_stats;

	$settings = bb2_read_settings();

	if ($force || $settings['display_stats']) {
		$blocked = bb2_db_rows(bb2_db_query("SELECT COUNT(*) FROM " . $settings['log_table'] . " WHERE `key` NOT LIKE '00000000'"));
		if ($blocked !== FALSE) {
			echo sprintf('<p><a href="http://www.bad-behavior.ioerror.us/">%1$s</a> %2$s <strong>%3$s</strong> %4$s</p>', 'Bad Behavior', 'has blocked', $blocked[0]["COUNT(*)"], 'access attempt(s) in the last 7 days.');
		}
	}
}

// Return the top-level relative path of wherever we are (for cookies)
// You should provide in $url the top-level URL for your site.
function bb2_relative_path() {
	global $cookie_path;
	return $cookie_path;
}

// Calls inward to Bad Behavor itself.
require_once(BB2_CWD . "/bad-behavior/version.inc.php");
require_once(BB2_CWD . "/bad-behavior/core.inc.php");
bb2_install();

bb2_start(bb2_read_settings());
