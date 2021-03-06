<?php

// Allow Sessions on the page
session_start();

if( !isset($_SESSION['borealis_flash']) )
	$_SESSION['borealis_flash'] = array();

// Setup our locations
define('BASE_PATH', realpath(dirname('../../')));
define('LIBRARIES', BASE_PATH . '/libraries');
define('APP_PATH', BASE_PATH . '/app');


// Common variables
$params 		= array();
$config 		= array();
$variables 		= array();
$helpers 		= array();
$connections 	= array();
$flash 			= array();


// Get the Base class
include_once BASE_PATH . '/system/classes/base.php';

// Prepare for Routes
include_once BASE_PATH . '/system/classes/routes.php';

// Load spyc ( yaml to array)
include_once LIBRARIES . '/spyc.php';

// ActiveRecord
include_once LIBRARIES . '/activerecord/ActiveRecord.php';

// Setup for configurations
include_once BASE_PATH . '/config/config.php';

// Get the Application Base
include_once BASE_PATH . '/system/classes/ApplicationBase.php';

// Get the Helper Base
include_once BASE_PATH . '/system/classes/HelperBase.php';




// Load database information
$db_info = spyc_load_file(BASE_PATH . '/config/database.yml');

if( isset($db_info['default']) ) {
	if( isset($db_info[ENVIRONMENT]) )
		$db_info[ENVIRONMENT] = array_merge($db_info['default'], $db_info[ENVIRONMENT]);
				
	unset($db_info['default']);
}

$config[ENVIRONMENT]['DB_HOST'] = $db_info[ENVIRONMENT]['host'];
$config[ENVIRONMENT]['DB_USER'] = $db_info[ENVIRONMENT]['user'];
$config[ENVIRONMENT]['DB_PASS'] = $db_info[ENVIRONMENT]['password'];
$config[ENVIRONMENT]['DB_NAME'] = $db_info[ENVIRONMENT]['database_name'];
unset($db_info);
// Done loading database info and putting it into $config array




// Connect to the database
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory(APP_PATH . '/models');
    $cfg->set_connections(array(ENVIRONMENT => 'mysql://' . Base::config('DB_USER') . ':' . Base::config('DB_PASS') . '@' . Base::config('DB_HOST') . '/' . Base::config('DB_NAME') . ''));
});


// Load the routes
include_once BASE_PATH . '/config/routes.php';




// Include the ApplicationController
include_once APP_PATH . '/controllers/application_controller.php';



// Store the flashes
$flash = $_SESSION['borealis_flash'];
$_SESSION['borealis_flash'] = array();


// Start including the controller and views
$Map->load();
?>