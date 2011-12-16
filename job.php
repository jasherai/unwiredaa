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

if ($_SERVER['argc'] < 2) {
    die("Usage: php job.php module:job");
}

$job = $_SERVER['argv'][1];

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

    // Define path to public directory
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/public'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if (APPLICATION_ENV == 'development') {
    $startTime = microtime(true);
}

define('APPLICATION_START_MICROTIME', microtime(true));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
try {
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );


    $application->bootstrap();

    $filter = new Zend_Filter_Word_DashToCamelCase();
    $jobClassParts = explode(':', $job);

    $class = ucfirst($filter->filter($jobClassParts[0])) . '_Job_' . ucfirst($filter->filter($jobClassParts[1]));

    if (!class_exists($class, true)) {
        die($class);
        echo 'Cannot find job ' . $job;
        exit;
    }
    $jobInstance = new $class;

    $jobInstance->run();

} catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
}