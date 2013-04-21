<?php

/**
 * TechDivision\ServletContainer\Interfaces\ServletResponse
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer\Interfaces;

/**
 * Interface for the servlet response.
 *
 * @package        TechDivision\ServletContainer
 * @copyright      Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license        http://opensource.org/licenses/osl-3.0.php
 *                 Open Software License (OSL 3.0)
 * @author         Markus Stockbauer <ms@techdivision.com>
 */
<<<<<<< HEAD:src/app/code/core/TechDivision/ServletContainer/Interfaces/ServletResponse.php
interface ServletResponse {

    /**
     * @abstract
     * @return string
     */
    public function getContent();
=======
class HttpServletResponse implements ServletResponse
{

    const HEADER_NAME_STATUS = 'status';
    const SESSION_NAME = 'PHPSESSID';

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $headers = array();

    public function __construct()
    {

        // prepare the headers
        $this->setHeaders(
            array(
                self::HEADER_NAME_STATUS => "HTTP/1.1 200 OK",
                "Date"                   => gmdate('D, d M Y H:i:s \G\M\T', time()),
                "Last-Modified"          => gmdate('D, d M Y H:i:s \G\M\T', time()),
                "Expires"                => gmdate('D, d M Y H:i:s \G\M\T', time() - 3600),
                "Server"                 => "Apache/1.3.29 (Unix) PHP/5.4.10",
                "Content-Language"       => "de",
                "Connection"             => "close",
                "Content-Type"           => "text/html",
            )
        );

        // if session has already been started in request
        if($sessionId = session_id()) {
            $this->addHeader('Set-Cookie', self::SESSION_NAME . '="' . $sessionId . '";');
        }
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * @return string
     */
    public function getHeadersAsString()
    {
        $headers = "";

        foreach ($this->getHeaders() as $header => $value) {

            if ($header === self::HEADER_NAME_STATUS) {
                $headers .= $value . "\r\n";
            } else {
                $headers .= $header . ': ' . $value . "\r\n";
            }
        }

        return $headers;
    }

    /**
     * Removes one single header from the headers array.
     *
     * @param string $header
     * @return void
     */
    public function removeHeader($header)
    {
        unset($this->headers[$header]);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
>>>>>>> some experimental approaches on header management:src/app/code/core/TechDivision/ServletContainer/Http/HttpServletResponse.php

    /**
     * @abstract
     * @param string $content
     * @return void
     */
<<<<<<< HEAD:src/app/code/core/TechDivision/ServletContainer/Interfaces/ServletResponse.php
    public function setContent($content);
}
=======
    public function setContent($content)
    {
        $this->content = $content;
    }

}
>>>>>>> some experimental approaches on header management:src/app/code/core/TechDivision/ServletContainer/Http/HttpServletResponse.php
