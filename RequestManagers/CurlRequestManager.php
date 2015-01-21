<?php

namespace RequestManagers;

use \RequestManagers\IRequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * A RequestManager class that uses the PHP cURL method in order to establish the connection
 * with the PushApi and retrieves the server response, also handles the errors that could occur
 * during the connection and are thrown as an exception.
 */
class CurlRequestManager implements IRequestManager
{
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
    const HEADER_APP_ID = "X-App-Id: ";
    const HEADER_APP_AUTH = "X-App-Auth: ";
    const HEADER_CONTENT_TYPE = "Content-Type: ";
    const X_WWW_FORM_URLENCODED = "application/x-www-form-urlencoded";

    /**
     * HTTP response codes
     */
    const HTTP_RESPONSE_OK = 200;

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
     * Displays the data sent/received to/from the server
     * @var bool
     */
    private $verbose = false;

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
     * Sets the base url
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Returns the base url
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the port
     * @param integer $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Returns the port
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the verbose
     * @param boolean $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * Returns the app auth
     * @return boolean
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     * Sets the app identification
     * @param integer $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * Returns the app identification
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Sets the app auth
     * @param string $appAuth
     */
    public function setAppAuth($appAuth)
    {
        $this->appAuth = $appAuth;
    }

    /**
     * Returns the app auth
     * @return string
     */
    public function getAppAuth()
    {
        return $this->appAuth;
    }


    ///////////////////////////////////////////////////////
    //               MAIN FUNCTIONALITIES                //
    ///////////////////////////////////////////////////////

    /**
     * Sends a call to the PushApi and retrieves the result.
     * @param  string $method HTTP method of the request
     * @param  string $path   The path that must be added to the base url in order to get the right API call
     * @param  array $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array Response key => value array
     *
     * @throws Exception If onnection failed
     */    public function sendRequest($method, $path, $params = [])
    {
        if (empty($this->getAppId()) || empty($this->getAppAuth())) {
            throw new \Exception("RequestManager has no app params set", -2);
        }

        // Preparing HTTP headers
        $headers = array(
            self::HEADER_APP_ID . $this->getAppId(),
            self::HEADER_APP_AUTH . $this->getAppAuth()
        );

        // Preparing HTTP connection
        $ch = curl_init();
 
        if ($method == self::POST || $method == self::PUT) {
            array_push($headers, self::HEADER_CONTENT_TYPE . self::X_WWW_FORM_URLENCODED);
        }

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . $path);
        curl_setopt($ch, CURLOPT_PORT, $this->getPort());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // We want to retrieve returned information
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->getVerbose());
        curl_setopt($ch, CURLOPT_HEADER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Getting the raw output
        $curlResponse = curl_exec($ch);
        // Getting information about the transfer
        $curlHeaders = curl_getinfo($ch);

        // Fetching results or failing if doesn't work
        if ($curlResponse === false) {
            throw new \Exception("Connection failed: " . curl_error($ch), -2);
        }
 
        // Closing the HTTP connection
        curl_close($ch);

        return $this->parseCurlResponse($curlResponse, $curlHeaders);
    }

    /**
     * Parses the HTTP response.
     * When API throws an error, the description is send via special X HTTP headers and it is displayed into
     * the raw output. This output must be transformed (string to array) and then it is easier to get the information.
     * When response is readable, it is searched the body of the response and returned or if an error is returned, it
     * generates a Exception with the error message recieved from the PushApi.
     * @param  string $curlResponse The raw output recived from the cURL
     * @param  array $curlHeaders  Information about the transfer
     * @return array Response key => value array
     *
     * @throws Exception If PushApi returns fail response
     */
    public function parseCurlResponse($curlResponse, $curlHeaders)
    {
        $curlHeadersSize = $curlHeaders["header_size"];

        // Retriving the body from the response. If the body is set it will be placed
        // following the length of the headers
        $responseBody = trim(substr($curlResponse, $curlHeadersSize));
        // Retriving and sorting the headers from the response
        $headers = explode("\n", trim(substr($curlResponse, 0, $curlHeadersSize)));
        // First header hasn't a valid content to parse
        unset($headers[0]);
        $sortedHeaders = array();
        foreach($headers as $line) {
            list($key, $val) = explode(':', $line, 2);
            $sortedHeaders[$key] = trim($val);
        }
        
        if ($curlHeaders["http_code"] != self::HTTP_RESPONSE_OK) {
            throw new \Exception($sortedHeaders["X-Status-Reason"], $curlHeaders["http_code"]);
        } else {
            return json_decode($responseBody, true);
        }
    }
}