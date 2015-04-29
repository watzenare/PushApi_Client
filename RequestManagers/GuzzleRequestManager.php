<?php

require 'vendor/autoload.php';

namespace RequestManagers;

use GuzzleHttp\Client;
use \RequestManagers\RequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * A RequestManager class that uses the PHP Guzzle Github project in order to establish the connection
 * with the PushApi and retrieves the server response, also handles the errors that could occur
 * during the connection and are thrown as an exception.
 */
class GuzzleRequestManager extends RequestManager
{
    /**
     * The Guzzer Client
     * @var Object
     */
    private $sender;

    /**
     * It is initializated the Guzzer Client with some basic params
     */
    function __construct($baseUrl, $port)
    {
        parent::__construct($baseUrl, $port);

        $baseRequestParams = [
            'base_url' => $baseUrl,
        ];
        $sender = new Client($baseRequestParams);
        $sender->setPort($port);
    }


    /**
     * Sends a call to the PushApi and retrieves the result.
     * @param  string $method HTTP method of the request
     * @param  string $path   The path that must be added to the base url in order to get the right API call
     * @param  array $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array Response key => value array
     *
     * @throws Exception If onnection failed
     */
    public function sendRequest($method, $path, $params = [])
    {
        if (empty($this->getAppId()) || empty($this->getAppAuth())) {
            throw new \Exception("RequestManager has no app params set", -2);
        }

        $requestOptions = [
            'headers' => [
                self::HEADER_APP_ID => $this->getAppId(),
                self::HEADER_APP_AUTH => $this->getAppAuth(),
            ],
        ];

        // Check if the call must be done synchronous or asynchronous
        if ($this->getTransmision() == self::ASYNC) {
            $requestOptions['future'] = true;
        }

        if ($method == self::POST || $method == self::PUT) {
            $headers['headers'][self::HEADER_CONTENT_TYPE] = self::X_WWW_FORM_URLENCODED;
        }

        // Preparing the request
        $request = $this->sender->createRequest($method, $path, $requestOptions);

        // Making the request
        $response = $this->sender->send($request);
        // $response = $this->sender->send($request)->then(function ($response) {
        //     echo 'I completed! ' . $response;
        // });

        return $response;
    }
}