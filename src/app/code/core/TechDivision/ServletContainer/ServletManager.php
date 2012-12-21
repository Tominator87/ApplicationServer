<?php

namespace TechDivision\ServletContainer\Servlet;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Exceptions\InvalidApplicationArchiveException;

use TechDivision_Collections_Dictionary as Dictionary;
use TechDivision_Lang_String as String;

class ServletManager
{
    /**
     * @var Dictionary
     */
    protected $servlets = NULL;

    /**
     * @var String
     */
    protected $webappPath = NULL;

    /**
     * @var ServletManager
     */
    protected static $instance = NULL;

    /**
     * Singleton Accessor
     *
     * @static
     * @return ServletManager
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            // instanciate the singleton
            self::$instance = new self;
            // build the actual web application path
            self::$instance->setWebappPath(new String(realpath(BP . DS . 'webapps')));
            // initialize the servlets Dictionary
            self::$instance->setServlets(new Dictionary());
        }

        return self::$instance;
    }

    /**
     * @param $archive
     */
    protected function deployArchive($archive)
    {
        die(__METHOD__ . ' is not implemented!');
    }

    /**
     * Gathers all available archived webapps and deploys them for usage.
     *
     * @param void
     * @return void
     */
    protected function deployWebapps()
    {

        // gather all the available web application archives and deploy them
        foreach (new \RegexIterator(new \FilesystemIterator($this->getWebappPath()), '/^.*\.phar$/') as $archive) {
            $this->deployArchive($archive);
        }

    }

    /**
     * Finds all servlets which are provided by the webapps and initializes them.
     *
     * @param void
     * @return void
     */
    protected function registerServlets()
    {
        // gather all the deployed web applications
        foreach (new \FilesystemIterator($this->getWebappPath()) as $folder) {
            // the phar files have been deployed into folders
            if (is_dir($folder)) {
                // it's no valid application without at least the web.xml file
                if (!file_exists($web = $folder . DS . 'WEB-INF' . DS . 'web.xml')) {
                    throw new InvalidApplicationArchiveException(sprintf('Folder %s contains no valid webapp.'));
                }

                // add the servlet-specific inclue path
                set_include_path($folder . PATH_SEPARATOR . get_include_path());

                // load the application config
                $config = new \SimpleXMLElement(file_get_contents($web));

                /** @var $mapping \SimpleXMLElement */
                foreach ($config->xpath('/web-app/servlet-mapping') as $mapping) {

                    // try to resolve the mapped servlet class
                    $classname = $config->xpath(
                        '/web-app/servlet[servlet-name="' . $mapping->{'servlet-name'} . '"]/servlet-class');

                    if (!count($classname)) {
                        throw new InvalidApplicationArchiveException(sprintf(
                            'No servlet class defined for servlet %s', $mapping->{'servlet-name'}));
                    }

                    // get the string classname
                    $classname = (string)array_shift($classname);

                    // set the additional servlet include paths
                    set_include_path($folder . DS . 'WEB-INF' . DS . 'classes' . PS . get_include_path());
                    set_include_path($folder . DS . 'WEB-INF' . DS . 'lib' . PS . get_include_path());

                    // instanciate the servlet
                    /** @var $servlet Servlet */
                    $servlet = new $classname();

                    // initialize the servlet
                    $servlet->init();

                    // the servlet is added to the dictionary using the complete request path as the key
                    $this->addServlet(
                        new String('/' . basename($folder) . (string)$mapping->{'url-pattern'}),
                        $servlet
                    );
                }
            }
        }
    }

    /**
     * @param \TechDivision_Collections_Dictionary $servlets
     */
    protected function setServlets($servlets)
    {
        $this->servlets = $servlets;
    }

    /**
     * @return \TechDivision_Collections_Dictionary
     */
    public function getServlets()
    {
        return $this->servlets;
    }

    /**
     * @param \TechDivision_Lang_String              $key
     * @param \TechDivision\ServletContainer\Servlet $servlet
     */
    protected function addServlet(String $key, Servlet $servlet)
    {
        $this->getServlets()->add($key, $servlet);
    }

    /**
     * @param String $webappPath
     */
    protected function setWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * @return String
     */
    protected function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Initialize all the servlets.
     */
    public function initialize()
    {
        $this->deployWebapps();
        $this->registerServlets();
    }
}

