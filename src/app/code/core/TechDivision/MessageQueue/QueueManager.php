<?php

/**
 * TechDivision\ServletContainer\QueueManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
    
namespace TechDivision\MessageQueue;

/**
 * The queue manager handles the queues and message beans registered for the application.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Markus Stockbauer <ms@techdivision.com>
 * @author      Tim Wagner <tw@techdivision.com>
 */
class QueueManager {

    /**
     * The path to the web application.
     * @var string
     */
    protected $webappPath;
	
	/**
	 * The array with queue names and the MessageReceiver class names as values
	 * @var array
	 */
	protected $queues = array();
	
	/**
	 * The array with the name of the registered MessageBeans
	 * @var array
	 */
	protected $messageBeans = array();
	
	/**
	 * The path to the application directories.
	 * @var array
	 */
	protected $applicationDirectories = array();
    
    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     * 
     * @return \TechDivision\ServletContainer\Application The connected application
     */
    public function initialize() {

	    // initialize the array with the application directories
	    $this->applicationDirectories = array();
	    
        // deploy the message queues and bean's
        $this->registerMessageQueues();
        $this->registerMessageBeans();
        
        // return the instance itself
        return $this;
    }
    
    protected function extendIncludePath($directory) {
         
        $includePath = explode(PATH_SEPARATOR, ini_get('include_path'));
         
        if (in_array($directory, $includePath) === false) {
            ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . $directory);
        }
    }
    
    /**
     * Deploys the MessageQueue's.
     *
     * @param SimpleXMLElement $sxe The XML node with the MessageBean information
     * @return void
     */
    protected function registerMessageQueues() {
        
        $basePath = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'META-INF';
         
        if (is_file($basePath . DIRECTORY_SEPARATOR . 'dummy-queues.xml') === false) {
            return;
        }
         
        $sxe = new \SimpleXMLElement($basePath . DIRECTORY_SEPARATOR . 'dummy-queues.xml', null, true);
         
        // lookup the MessageQueue's defined in the passed XML node
        foreach ($sxe->xpath("//message-queues/message-queue") as $node) {
    
            // load the nodes attributes
            $attributes = $node->attributes();
             
            // extract the attributes from the XML
            $applicationDirectory = $basePath . DIRECTORY_SEPARATOR . (string) $attributes["directory"];
            $type = (string) $attributes["type"];
             
            // add the application directory if not already added
            if (array_key_exists($applicationDirectory, $this->applicationDirectories) === false) {
    
                // add the deployment directory to the include path
                $this->extendIncludePath($applicationDirectory);
    
                // add the directory to the included directories to avoid double entries
                $this->applicationDirectories[] = $applicationDirectory;
            }
             
            $destination = (string) $node->destination;
            $this->queues[$destination] = $type;
             
            error_log("Successfully initialized queue: " . $destination);
        }
    }
    
    /**
     * Deploys the MessageBean's.
     *
     * @param SimpleXMLElement $sxe The XML node with the MessageBean information
     * @return void
     */
    protected function registerMessageBeans() {
        
        $basePath = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'META-INF';
    
        if (is_file($basePath . DIRECTORY_SEPARATOR . 'dummy-services.xml') === false) {
            return;
        }
         
        $sxe = new \SimpleXMLElement($basePath . DIRECTORY_SEPARATOR . 'dummy-services.xml', null, true);
         
        // lookup the MessageBean's defined in the passed XML node
        foreach ($sxe->xpath("//server/message-bean") as $node) {
    
            // load the nodes attributes
            $attributes = $node->attributes();
             
            // extract the attributes from the XML
            $applicationDirectory = $basePath . DIRECTORY_SEPARATOR . (string) $attributes["directory"];
            $name = (string) $attributes["name"];
             
            // add the application directory if not already added
            if (array_key_exists($applicationDirectory, $this->applicationDirectories) === false) {
    
                // add the deployment directory to the include path
                $this->extendIncludePath($applicationDirectory);
    
                // add the directory to the included directories to avoid double entries
                $this->applicationDirectories[] = $applicationDirectory;
            }
             
            $this->messageBeans[$name] = $this->initAttributes($sxe);
             
            error_log("Successfully initialized service: " . $name);
        }
    }
    
    /**
     * Initializes the attributes necessary to
     * initialize a MessageBean.
     *
     * @param SimpleXMLElement $sxe The attribute data
     * @return MessageBeanAttributesImpl The initialized attributes
     */
    protected function initAttributes(\SimpleXMLElement $sxe) {
    
        // initialize the attributes
        $attributes = array();
    
        // iterate over the abvailable attributes
        foreach (MessageBeanAttributeImpl::getAvailableAttributes() as $attributeName) {
    
            // load the data from the XML file
            foreach ($sxe->xpath("//attribute[@name='$attributeName']") as $node) {
                 
                $attr = MessageBeanAttributeImpl::get();
                $attr->setName($attributeName);
                $attr->setValue((string) $node);
    
                // add the attribute
                $attributes[$attributeName] = $attr;
            }
        }
    
        // return the attributes
        return $attributes;
    }
    
    /**
     * Returns the array with the name of the
     * registered MessageBeans
     *
     * @return array
     */
    public function getMessageBeans() {
        return $this->messageBeans;
    }
    
    /**
     * Returns the array with queue names and the
     * MessageReceiver class names as values.
     *
     * @return array
     */
    public function getQueues() {
        return $this->queues;
    }

    /**
     * @param String $webappPath
     */
    public function setWebappPath($webappPath) {
        $this->webappPath = $webappPath;
    }

    /**
     * @return String
     */
    public function getWebappPath() {
        return $this->webappPath;
    }
}