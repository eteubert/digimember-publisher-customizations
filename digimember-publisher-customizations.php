<?php
/**
 * Plugin Name: DigiMember Publisher Customizations
 * Plugin URI: http://www.digimember.net
 * Description: Shortcodes to extend DigiMember functionality.
 * Author: Eric Teubert
 * Version: 1.0
 * Author URI: http://www.digimember.net
 * License: MIT
**/

namespace Podlove\DigiMember;

if (!file_exists(plugin_dir_path(__FILE__) . 'config.php'))
	die('DigiMember Publisher Customizations: you need to create a config.php for the PODLOVE_DIGIMEMBER_API_KEY constant.');

require_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'lib/api.php';
require_once plugin_dir_path(__FILE__) . 'lib/base.php';
require_once plugin_dir_path(__FILE__) . 'lib/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'lib/ajax.php';
