<?php
/*
	Plugin Name: competition
	Plugin URI: http://wordpress.org/plugins/competition/
	Description: compare meta information of all Plugins containing specific tags
	Author: ManFisher Medien ManuFaktur
	Version: 1.0.5
	Author URI: http://manfisher.net/
	Text Domain: competition
	Domain Path: /languages
*/
/*
	Copyright 2014  Mark Cheret, Stefan Herndler (email : info@manfisher.eu | support@herndler.org)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 12.08.14 14:59
 * Version: 1.0.2
 * Since: 0.0.1
 */

// Get all common class and functions
require_once(dirname(__FILE__) . "/includes.php");

// add Plugin Links to the "installed plugins" page
$l_str_plugin_file = 'competition/competition.php';
add_filter("plugin_action_links_{$l_str_plugin_file}", array("MCI_Competition_Hooks", "PluginLinks"), 10, 2);

// initialize the Plugin
$g_obj_MCI_Competition = new MCI_Competition();
// run the Plugin
$g_obj_MCI_Competition->run();