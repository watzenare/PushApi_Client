<?php

namespace RequestManagers;

use \RequestManagers\RequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * A RequestManager class that uses the PHP cURL method in order to establish the connection
 * with the PushApi and retrieves the server response, also handles the errors that could occur
 * during the connection and are thrown as an exception.
 */
class CurlRequestManager extends RequestManager
{
    /**
     * Sends a call to the PushApi and retrieves the result.
     * @param  string $method HTTP method of the request
     * @param  string $path   The path that must be added to the base url in order to get the right API call
     * @param  array $params  Array with the required params as keys (used with PUT && POST method)
     * @return array Response key => value array
     *
     * @throws Exception If connection failed
     */
    public function sendRequest($method, $path, $params = [])
    {
        if (empty($this->getAppId()) || empty($this->getAppAuth())) {
            throw new \Exception("RequestManager has no app params set", -2);
        }

        // Preparing HTTP headers
        $headers = array(
            self::HEADER_APP_ID. ": " . $this->getAppId(),
            self::HEADER_APP_AUTH. ": " . $this->getAppAuth()
        );

        // Preparing HTTP connection
        $ch = curl_init();

        if ($method == self::POST || $method == self::PUT) {
            array_push($headers, self::HEADER_CONTENT_TYPE . self::X_WWW_FORM_URLENCODED);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        if (!empty($params) && $method == self::GET) {
            $path .=  "?" . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . $path);
        curl_setopt($ch, CURLOPT_PORT, $this->getPort());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Setting timeout that throws exception when the PushApi is down
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // We want to retrieve returned information
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->getVerbose());
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Disabling SSL Certificate support temporally
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
     * generates a Exception with the error message received from the PushApi.
     * @param  string $curlResponse The raw output received from the cURL
     * @param  array $curlHeaders  Information about the transfer
     * @return array Response key => value array
     *
     * @throws Exception If PushApi returns fail response
     */
    public function parseCurlResponse($curlResponse, $curlHeaders)
    {
        $curlHeadersSize = $curlHeaders["header_size"];

        // Retrieving the body from the response. If the body is set it will be placed.
        // following the length of the headers
        $responseBody = trim(substr($curlResponse, $curlHeadersSize));
        // Retrieving and sorting the headers from the response
        $headers = explode("\n", trim(substr($curlResponse, 0, $curlHeadersSize)));
        // First header hasn't a valid content to parse
        unset($headers[0]);
        $sortedHeaders = array();
        foreach($headers as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $explodedHeader = explode(':', $line, 2);

            if (sizeof($explodedHeader) > 1) {
                $key = $explodedHeader[0];
                $value = $explodedHeader[1];
                $sortedHeaders[$key] = trim($value);
            } else {
                $value = $explodedHeader[0];
                $sortedHeaders[] = trim($value);
            }
        }

        if ($curlHeaders["http_code"] == self::HTTP_RESPONSE_OK) {
            return json_decode($responseBody, true);
        } else {
            throw new \Exception($sortedHeaders["X-Status-Reason"], $curlHeaders["http_code"]);
        }
    }
}