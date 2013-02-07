<?php

/**
 * TechDivision\PersistenceContainer\Container
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer;

use TechDivision\ApplicationServer\AbstractContainer;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Container extends AbstractContainer {

    /**
     * XPath expression for the application configurations.
     * @var string
     */
    const XPATH_APPLICATIONS = '/datasources/datasource';

    /**
     * Returns an array with available applications.
     *
     * @return \TechDivision\Server The server instance
     * @todo Implement real deployment here
     */
    public function deploy() {
        
        $applications = array();

        // gather all the deployed web applications
        foreach (new \FilesystemIterator(getcwd() . '/webapps') as $folder) {

            // check if file or subdirectory has been found
            if (is_dir($folder)) {

                // initialize the application name
                $name = basename($folder);

                // it's no valid application without at least the appserver-ds.xml file
                if (!file_exists($ds = $folder . DS . 'META-INF' . DS . 'appserver-ds.xml')) {
                    throw new InvalidApplicationArchiveException(sprintf('Folder %s contains no valid webapp.'));
                }

                // add the servlet-specific include path
                set_include_path($folder . PS . get_include_path());

                // load the data source config
                $config = new \SimpleXMLElement(file_get_contents($ds));

                /** @var $mapping \SimpleXMLElement */
                // iterate over the found application nodes
                foreach ($config->xpath(self::XPATH_APPLICATIONS) as $dataSource) {

                    $attributes = $dataSource->attributes();

                    $type = (string) $attributes['type'];

                    if (empty($type)) {
                        $type = 'TechDivision\PersistenceContainer\Application';
                    }

                    // initialize the application instance
                    $application = $this->newInstance($type, array($name));
                    $application->setWebappPath($folder->getPathname());
                    $application->setDataSourceName((string) $dataSource->name);
                    $application->setPathToEntities((string) $dataSource->pathToEntities);

                    // load the database connection information
                    foreach ($dataSource->children() as $database) {
                        $application->setConnectionParameters(
                            array(
                                'driver' => (string) $database->driver,
                                'user' => (string) $database->user,
                                'password' => (string) $database->password,
                                'dbname' => (string) $database->databaseName,
                            )
                        );
                    }
                }

                // set the additional servlet include paths
                set_include_path($folder . DS . 'META-INF' . DS . 'classes' . PS . get_include_path());
                set_include_path($folder . DS . 'META-INF' . DS . 'lib' . PS . get_include_path());

                // add the application to the available applications
                $applications[$application->getDataSourceName()] = $application;
            }
        }

        // return the server instance
        return $applications;
    }
}