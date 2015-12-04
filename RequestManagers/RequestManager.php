<?php

namespace RequestManagers;

use \RequestManagers\IRequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Contains the basic attributes and functionalities implemented required by the RequestManager interface,
 * it means that every dedicated RequestManager will must only implement the send functionality that it is
 * the most important one.
 */
abstract class RequestManager implements IRequestManager
{
    ////////////////////////////////////////////////////////////
    //              MAIN CONSTANTS AND VARIABLES              //
    ////////////////////////////////////////////////////////////

    /**
     * Main calls that support the PushApi
     */
    const GET = "GET";
    const PUT = "PUT";
    const POST = "POST";
    const DELETE = "DELETE";

    /**
     * HTTP headers and content
     */
    const HEADER_APP_ID = "X-App-Id";
    const HEADER_APP_AUTH = "X-App-Auth";
    const HEADER_CONTENT_TYPE = "Content-Type";
    const X_WWW_FORM_URLENCODED = "application/x-www-form-urlencoded";

    /**
     * HTTP response codes
     */
    const HTTP_RESPONSE_OK = 200;

    /**
     * Type of the transmission
     */
    const SYNC = "sync"; // synchronous
    const ASYNC = "async"; // asynchronous

    /**
     * The host where the API is running.
     * @var string
     */
    protected $baseUrl;

    /**
     * The port where the API is running.
     * @var integer
     */
    protected $port;

    /**
     * Displays the data sent/received to/from the server.
     * @var bool
     */
    protected $verbose = false;

    /**
     * Agent app identification.
     * @var integer
     */
    protected $appId;

    /**
     * Agent app authentication.
     * @var string
     */
    protected $appAuth;

    /**
     * The transmission method used by the request manager.
     * It can be 'sync' or 'async' (synchronous or asynchronous)
     * @var string
     */
    protected $transmission = self::SYNC;


    ////////////////////////////////////////////////
    //              MAIN CONSTRUCTOR              //
    ////////////////////////////////////////////////

    /**
     * It is needed the $host {@link $baseUrl} and port {@link $port} of the PushApi
     * in order to establish the connection successfully when needed.
     *
     * @param string  $baseUrl   The host where the API is running
     * @param integer $port      The port where the API is running
     */
    function __construct($baseUrl, $port)
    {
        $this->setBaseUrl($baseUrl);
        $this->setPort($port);
    }


    /////////////////////////////////////////////////////////////////
    //               MAIN CLASS GETTERS AND SETTERS                //
    /////////////////////////////////////////////////////////////////

    /**
     * Sets the base url.
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Returns the base url.
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the port.
     * @param integer $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Returns the port.
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the verbose.
     * @param boolean $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * Returns the app auth.
     * @return boolean
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     * Sets the app identification.
     * @param integer $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * Returns the app identification.
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Sets the app auth.
     * @param string $appAuth
     */
    public function setAppAuth($appAuth)
    {
        $this->appAuth = $appAuth;
    }

    /**
     * Returns the app auth.
     * @return string
     */
    public function getAppAuth()
    {
        return $this->appAuth;
    }

    /**
     * Sets the transmission method that will be used.
     * @param string $method
     */
    public function setTransmission($method)
    {
        $this->transmission = $method;
    }

    /**
     * Gets the transmission method.
     * @return string
     */
    public function getTransmission()
    {
        return $this->transmission;
    }

    /**
     * Sends a call to the PushApi and retrieves the result.
     * @param  string $method HTTP method of the request
     * @param  string $path   The path that must be added to the base url in order to get the right API call
     * @param  array $params  Array with the required params as keys (used with PUT && POST method)
     * @return array Response key => value array
     *
     * @throws Exception If connection failed
     */
    abstract public function sendRequest($method, $path, $params = []);
}