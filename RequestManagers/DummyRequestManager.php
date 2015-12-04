<?php

namespace RequestManagers;

use \RequestManagers\RequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Dummy class that extends the RequestManager format but does not do any call to the PushApi server.
 * It is used in order to see how the Client communicates with the RequestManager classes.
 */
class DummyRequestManager extends RequestManager
{
    /**
     * Retrieves the request params received
     * @param  string $method HTTP method of the request
     * @param  string $path   The path that must be added to the base url in order to get the right API call
     * @param  array $params  Array with the required params as keys (used with PUT && POST method).
     *                          If it is set a key param 'exception' and its value is true
     * @return array Response key => value array
     */
    public function sendRequest($method, $path, $params = [])
    {
        if (isset($params["exception"]) && $params["exception"]) {
            throw new \Exception("I'm a Dummy Exception", 0);
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