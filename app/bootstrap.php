<?php 
	// Load Config
	require_once 'config/config.php';


	// Load Helpers
	require_once 'helpers/url_helper.php';
	require_once 'helpers/session_helper.php';
	require_once 'helpers/imgsrc_helper.php';
	require_once 'helpers/messages.php';

	// Load Libraries
	// require_once 'libraries/Controller.php';
	// require_once 'libraries/Core.php';
	// require_once 'libraries/Database.php';

	// Autoload Core Libraries
	spl_autoload_register(function($className){
		require_once 'libraries/' . $className . '.php';
	});

    function exception_handler($e) {
        require_once 'controllers/ExceptionController.php';
        new ExceptionController($e);
    }

    set_exception_handler('exception_handler');


?>