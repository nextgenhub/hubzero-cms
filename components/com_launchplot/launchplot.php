<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//----------------------------------------------------------

$config = JFactory::getConfig();

//if ($config->getValue('config.debug')) {
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
//}

jimport('joomla.application.component.helper');

require_once( JPATH_COMPONENT.DS.'launchplot.html.php' );
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'launch.php' );

// Instantiate controller
$controller = new Controller();
$controller->mainframe = $mainframe;
$controller->execute();
$controller->redirect();
?>
