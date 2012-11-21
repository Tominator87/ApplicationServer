<?php

namespace TechDivision\Example;

use TechDivision\Example\Entities\Sample;
use TechDivision\Example\Services\SampleRemoteProcessor;
use TechDivision\ApplicationServer\SplClassLoader;

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

require_once 'TechDivision/ApplicationServer/SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();

$processor = new SampleRemoteProcessor();

if (array_key_exists('action', $_REQUEST)) {
    $action = $_REQUEST['action'];
} else {
    $action = 'findAll';
}

$id = '';
$name = '';

switch ($action) {
    case 'load':
        $entity = $processor->load($_REQUEST['id']);
        $name = $entity->getName();
        $id = $entity->getId();
        break;
    case 'persist':
        $entity = new Sample();
        $entity->setId($_POST['id']);
        $entity->setName($_POST['name']);
        $processor->persist($entity);
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
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="persist" />
                <fieldset>
                    <legend>Sample</legend>
                    <table><tr>
                            <td>Id:</td>
                            <td><input type="text" size="40" maxlength="40" name="id" value="<?php echo $id ?>"></td>
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
                <?php foreach ($entities as $id => $entity) { ?><tr>
                        <td><a href="index.php?action=load&id=<?php echo $entity->getId() ?>"><?php echo $entity->getId() ?></a></td>
                        <td><?php echo $entity->getName() ?></td>
                    </tr><?php } ?> 
            </table>
        </div>
    </body>
</html>