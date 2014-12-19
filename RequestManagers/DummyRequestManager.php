<?php

namespace RequestManagers;

use \RequestManagers\IRequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Dummy class that implements the RequestManager format but doesn't do any call to the PushApi server.
 * It is used in order to see how the Client communicates with the RequestManager classes.
 */
class DummyRequestManager implements IRequestManager
{
    /**
     * HTTP headers and content
     */
    const HEADER_APP_ID = "X-App-Id: ";
    const HEADER_APP_AUTH = "X-App-Auth: ";

    /**
     * The host where the API is running
     * @var string
     */
    private $baseUrl;

    /**
     * The port where the API is running
     * @var integer
     */
    private $port;

    /**
     * Agent app identification
     * @var integer
     */
    private $appId;

    /**
     * Agent app authentication
     * @var string
     */
    private $appAuth;


    /**
     * It is needed the $host {@link $baseUrl} and port {@link $port} of the PushApi
     * in order to establish the connection successfully when needed.
     *
     * @param [string]  $baseUrl   The host where the API is running
     * @param [integer] $port      The port where the API is running
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
     * Sets the base url
     * @param [string] $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Returns the base url
     * @return [string]
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the port
     * @param [integer] $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Returns the port
     * @return [integer]
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the app identification
     * @param [integer] $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * Returns the app identification
     * @return [integer]
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Sets the app auth
     * @param [string] $appAuth
     */
    public function setAppAuth($appAuth)
    {
        $this->appAuth = $appAuth;
    }

    /**
     * Returns the app auth
     * @return [string]
     */
    public function getAppAuth()
    {
        return $this->appAuth;
    }

    /**
     * Retrives the request params received
     * @param  [string] $method HTTP method of the request
     * @param  [string] $path   The path that must be added to the base url in order to get the right API call
     * @param  [array] $params  Array with the required params as keys (used with PUT && POST mothod).
     *                          If it is set a key param 'exception' and its value is true
     * @return [array] Response key => value array
     */
    public function sendRequest($method, $path, $params = [])
    {
        if (isset($params["exception"]) && $params["exception"]) {
            throw new \Exception("I'm a Dummmy Exception", 0);
        }
        return array(
            "result" => array(
                "headers" => array(
                    self::HEADER_APP_ID => $this->appId,
                    self::HEADER_APP_AUTH => $this->appAuth,
                ),
                "method" => $method,
                "path" => $this->baseUrl . $path,
                "params" => $params
            )
        );
    }
}