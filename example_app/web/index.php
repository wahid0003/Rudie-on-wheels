<?php

$_start = microtime(1); // The very first thing this request

use app\specs\Dispatcher; // My custom Dispatcher

// Config Vendors, database, ... ?
require(dirname(__DIR__).'/config/bootstrap.php');

// Config Dispatcher
$options = array(

	// I use the fallback several times. I love it. So I have to define it.
	'fallback_controller' => 'app\\controllers\\fallbax',

	// My Action names will be sensible and literal. No use for stupid prefixes or postfixes. PHP >= 5.3.4 ftw!
	'action_name_postfix' => '',

);
$dispatcher = new Dispatcher($options);

// Enable routes (available through config/routes.php through config/bootstrap.php)
$dispatcher->setRouter($router);

// If your web host doesn't do pretty urls (Apache's mod_rewrite), you should
// overwrite this method so that it gets the path from $_GET (or somewhere
// else if you'd like).
// If that's the case, you should probably also change `Output::url()`.
$path = $dispatcher->getRequestPath();

try {

	// `getApplication()` does all of the dispatching (except the actual dispatching).
	$application = $dispatcher->getApplication( $path ); // typeof Controller

	// Everything's checked now... Invalid URI's would've been intercepted (= 'exceptionalized').

	// All there's left to do is push the red button:
	// 1) fire _pre_action, 2) execute action, 3) fire _post_action
	$response = $application->_run();

	// Only during development ofcourse
	// The very last thing this request
	echo "\n\n".number_format(microtime(1) - $_start, 4);

}
catch ( \Exception $ex ) {

	// An extendable exception catch method, so you don't have to change index.php at all
	$dispatcher->caught($ex);

}


