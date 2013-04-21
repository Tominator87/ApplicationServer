<?php

/**
 * TechDivision\ServletContainer\Http\Request
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer\Http;

/**
 * A web request implementation.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Philipp Dittert <pd@techdivision.com>
 */
class Request {

    protected $_inputStream;
    protected $_transformedInputStream;
    protected $_method;
    protected $_protocol;
    protected $_uri;
    protected $_pathInfo;
    protected $_queryString;
    protected $_headers = array();
    protected $_content = '';
    protected $_parameter;
    protected $_parameterMap;
    protected $_contentStartId;
    protected $_isValid = FALSE;


    public function __construct(){

    }

    static function parse($inputStream)
    {
        $method = strstr($inputStream, " ", true);

        $req = Request::factory($method);
        $req->transform($inputStream)
            ->ParseRequestInformation()
            ->ParseUriInformation()
            ->setHeaders()
            ->setContent()
            ->setParameter()
            ->validate();

        return $req;
    }

    public static function _parsePath($path)
    {
        $regs = array();
        if (!preg_match("'([^?]*)(?:\?([^#]*))?(?:#.*)? *'", $path, $regs)) {
            return FALSE;
        }

        return array(
            'path_info'    => $regs[1],
            'query_string' => isset($regs[2]) ? $regs[2] : NULL
        );
    }

    public function isValid(){
        return $this->_isValid;
    }

    public static function factory($method){
        $className =  __NAMESPACE__ . '\\' . ucfirst(strtolower($method))."Request";
        return new $className;
    }

    public function getPathInfo(){
        return $this->_pathInfo;
    }

    public function setPathInfo($pathInfo){
        $this->_pathInfo = $pathInfo;
        return $this;
    }

    public function getQueryString()
    {
        return $this->_queryString;
    }

    public function setQueryString($queryString)
    {
        $this->_queryString = $queryString;
        return $this;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    public function setInputStream($inputStream){
        $this->_inputStream = $inputStream;
        return $this;
    }

    public function getInputStream(){
        return $this->_inputStream;
    }

    public function transform($inputStream){
        $this->_transformedInputStream = explode("\r\n", $inputStream);
        return $this;
    }

    public function getTransformedInputStream(){
        return $this->_transformedInputStream;
    }

    public function getUri(){
        return $this->_uri;
    }

    public function setUri($uri){
        $this->_uri = $uri;
        return $this;
    }

    public function getProtocol(){
        return $this->_protocol;
    }

    public function setProtocol($protocol){
        $this->_protocol = $protocol;
        return $this;
    }

    public function getHeaders(){
        return $this->_headers;
    }

    public function ParseUriInformation(){
        return $this;
    }

    public function parseHeaders(){
        $transformedInputStream = $this->getTransformedInputStream();
        $headers = array();
        for ($i = 1; $i < count($transformedInputStream); $i++) {
            if (trim($transformedInputStream[$i]) == '') {
                //empty line, after this the content should follow

                $i++;
                $this->setContentHelper($i);
                break;
            }
            $regs = array();
            if (preg_match("'([^: ]+): (.+)'", $transformedInputStream[$i], $regs)) {
                $headers[(strtolower($regs[1]))] = $regs[2];
            }
        }
        return $headers;
    }

    public function setHeaders(){
        $this->_headers = $this->parseHeaders();
        return $this;
    }

    public function parseRequestInformation(){
        $transformedInputStream = $this->getTransformedInputStream();
        $requestInfo = explode(" ", $transformedInputStream[0]);

        $this->setMethod( $requestInfo[0] );
        $this->setUri( $requestInfo[1] );
        $this->setProtocol( $requestInfo[2] );

        return $this;
    }

    public function getContentHelper(){
        return $this->_contentStartId;
    }

    public function setContentHelper($contentStartId){
        $this->_contentStartId = $contentStartId;
        return $this;
    }

    public function getContent(){
        return $this->_content;
    }

    public function setContent(){
        $content = $this->parseContent();
        $this->_content = $content;
        return $this;
    }

    protected function parseContent(){
        $tis = $this->getTransformedInputStream();
        $id = $this->getContentHelper();
        $content = '';

        for ($id; $id < count($tis); $id++) {
            $content .= $tis[$id] . "\r\n";
        }

        return trim($content);
    }

    public function getParameterMap(){
        return $this->_parameterMap;
    }

    public function getParameter(){
        return $this->_parameter;
    }

    public function setParameterMap(){
        $this->_parameterMap = $this->parseParameter( $this->getParameter() );
        return $this;
    }

    public function parseParameter($queryString){
        parse_str($queryString, $paramMap);
        return $paramMap;
    }

    protected function validate(){
        $this->_isValid = TRUE;
    }

}
