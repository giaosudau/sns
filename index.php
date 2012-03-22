<?php
session_start();

define("FRAMEWORK_PATH", dirname(__FILE__)."/registry/");
echo FRAMEWORK_PATH;

require 'registry/registry.class.php';
$registry = new Registry();
// Setup 
$registry->createAndStoreObject('template', 'template');
$registry->createAndStoreObject('mysqldb', 'db');
$registry->createAndStoreObject('authenticate', 'authenticate');
$registry->createAndStoreObject('urlprocessor', 'url');

// Database settings
include(FRAMEWORK_PATH . 'config.php');
// Create a connection 
$registry->getObject('db')->newConnection($configs['db_host_sn'], $configs['db_user_sn'], 
										  $configs['db_pass_sn'], $configs['db_name_sn']);
								  
// store and setting 			  
$settingsSQL = "SELECT `key`, `value` FROM  settings";
$registry->getObject('db')->executeQuery($settingsSQL);
while($setting = $registry->getObject('db')->getRows()){
	$registry->storeSetting($setting['value'], $setting[$key]);
}
/* Authenticate
$registry->getObject('template')->parseOutput();
print $registry->getObject('template')->getPage()->getContentToPrint();
*/
?>