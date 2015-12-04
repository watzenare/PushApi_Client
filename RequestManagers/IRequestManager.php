<?php

namespace RequestManagers;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Contains the basic functionalities that a RequestManager must implement
 * in order to handle a better HTTP message.
 */
interface IRequestManager
{
    /**
     * Basic getters and setters that must implement any RequestManager in order to have a correct management
     * of the connection params. These params are required params that any RequestManager must know because
     * all of them are required for establish the connection.
     */
    public function setBaseUrl($url);
    public function getBaseUrl();
    public function setPort($port);
    public function getPort();
    public function setVerbose($verbose);
    public function getVerbose();
    public function setAppId($appId);
    public function getAppId();
    public function setAppAuth($appAuth);
    public function getAppAuth();
    public function setTransmission($method);
    public function getTransmission();

    /**
     * The main send functionality that connects directly with the PushApi and retrieves the response data
     */
    public function sendRequest($method, $url, $params);
}