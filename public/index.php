<?php
/**
* Unwired AA GUI
*
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
*
* Licensed under the terms of the Affero Gnu Public License version 3 
* (AGPLv3 - http://www.gnu.org/licenses/agpl.html) or our proprietory 
* license available at http://www.unwired.at/license.html
*/  

/**
 *
 * @author B. Krastev <bkrastev@web-teh.net>
 */

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    // Define path to public directory
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if (APPLICATION_ENV == 'development') {
    $startTime = microtime(true);
}

define('APPLICATION_START_MICROTIME', microtime(true));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

try {
    $application->bootstrap()
                ->run();
} catch (Exception $e) {
    if (APPLICATION_ENV == 'development') {
        echo '<h2>'.$e->getMessage().'</h2>';
        echo '<pre>'.$e->getTraceAsString().'</pre>';
    } else {
        echo '<h1>Site temporary unavailable</h1>';
    }
}
if (APPLICATION_ENV == 'development') {
    echo '<!-- Page time: ' . (microtime(true) - $startTime) . ' -->';
}
