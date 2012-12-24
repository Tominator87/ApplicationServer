<?php



namespace TechDivision\Example;

use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainerClient\SplClassLoader;
use TechDivision\Example\Services\SampleProcessor;
use TechDivision\PersistenceContainer\Application;

// set the session timeout to unlimited
ini_set('session.gc_maxlifetime', 0);
ini_set('zend.enable_gc', 0);
ini_set('max_execution_time', 0);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(__FILE__));

$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'lib';

// set the new include path
set_include_path(implode(PS, $paths) . PS . get_include_path());

require_once 'TechDivision/PersistenceContainerClient/SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();

session_start();

// load the database connection information
$connectionParameters = array(
    'driver' => 'pdo_mysql',
    'user' => 'appserver',
    'password' => 'eraZor',
    'dbname' => 'appserver_ApplicationServer',
);

// initialize the application instance
$application = new Application('TechDivision\Example');
$application->setConnectionParameters($connectionParameters);
$application->setPathToEntities(array('TechDivision/Example/Entities'));
$application->connect();

$processor = new SampleProcessor($application);

if (array_key_exists('action', $_REQUEST)) {
    $action = $_REQUEST['action'];
} else {
    $action = 'findAll';
}

$sampleId = '';
$name = '';

switch ($action) {
    case 'load':
        $entity = $processor->load($_REQUEST['sampleId']);
        $name = $entity->getName();
        $sampleId = $entity->getSampleId();
        $entities = $processor->findAll();
        break;
    case 'persist':
        $entity = new Sample();
        $entity->setSampleId((integer) $_POST['sampleId']);
        $entity->setName($_POST['name']);
        $processor->persist($entity);
        $entities = $processor->findAll();
        break;
    case 'createSchema':
        $processor->createSchema();
        $entities = $processor->findAll();
        break;
    default:
        $entities = $processor->findAll();
        error_log("Found " . sizeof($entities) . " entities");
        break;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Sample Test</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div>
            <form action="index-script.php" method="post">
                <input type="hidden" name="action" value="persist" />
                <fieldset>
                    <legend>Sample</legend>
                    <table><tr>
                            <td>Id:</td>
                            <td><input type="text" size="40" maxlength="40" name="sampleId" value="<?php echo $sampleId ?>"></td>
                        </tr><tr>
                            <td>Name:</td>
                            <td><input type="text" size="40" maxlength="40" name="name" value="<?php echo $name ?>"></td>
                        </tr><tr>
                            <td colspan="2"><input type="submit" value="Save"></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <td>Id</td>
                        <td>Name</td>
                    </tr>
                </thead>
                <?php foreach ($entities as $sampleId => $entity) { ?><tr>
                        <td><a href="index-script.php?action=load&sampleId=<?php echo $entity->getSampleId() ?>"><?php echo $entity->getSampleId() ?></a></td>
                        <td><?php echo $entity->getName() ?></td>
                    </tr><?php } ?> 
            </table>
        </div>
    </body>
</html>