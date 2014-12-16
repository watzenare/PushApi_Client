<?php

/**
 * PushApi_Client
 *
 * A PHP standalone client that facilitates to developers the use of all the PushApi functionalities.
 * The client can use all the functionalites of the API less deleting an app or list all the registered apps.
 * It is recommended to have basic knowledge about what the API does, what are its functionalities, what
 * params are required for each call, etc.
 *
 * Warning: when a call need @param $params, this params must be send in an array and each key name must
 * be the expected request param that the API expects.
 *
 * App
 * @method getApp($idApp) [description]
 * @method updateApp($idApp, $params) [description]
 *
 * User
 * @method getUser($idUser) [description]
 * @method createUser($params) [description]
 * @method updateUser($idUser) [description]
 * @method deleteUser($idUser) [description]
 * @method getUsers() [description]
 * @method createUsers($params) [description]
 *
 * User Subscriptions
 * @method getUserSubscription($idUser, $idChannel) [description]
 * @method createUserSubscription($idUser, $idChannel) [description]
 * @method deleteUserSubscription($idUser, $idChannel) [description]
 * @method getUserSubscriptions($idUser) [description]
 *
 * User Preferences
 * @method getUserPreference($idUser, $idTheme) [description]
 * @method createUserPreference($idUser, $idTheme, $params) [description]
 * @method updateUserPreference($idUser, $idTheme, $params) [description]
 * @method deleteUserPreference($idUser, $idTheme) [description]
 * @method getUserPreferences($idUser) [description]
 *
 * Channel
 * @method getChannel($idChannel) [description]
 * @method createChannel($params) [description]
 * @method updateChannel($idChannel) [description]
 * @method deleteChannel($idChannel) [description]
 * @method getChannels() [description]
 *
 * Theme
 * @method getTheme($idTheme) [description]
 * @method createTheme($params) [description]
 * @method deleteTheme($idTheme) [description]
 * @method getThemes() [description]
 * @method getThemesByRange($range) [description]
 *
 * Subject
 * @method getSubject($idSubject) [description]
 * @method createSubject($params) [description]
 * @method updateSubject($idSubject) [description]
 * @method deleteSubject($idSubject) [description]
 * @method getSubjects() [description]
 *
 * Send
 * @method sendNotification($params) [description]
 *
 *
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 */
class PushApi_Client
{
    /**
     * Main calls that support the PushApi
     */
    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const DELETE = "DELETE";
    
    const HEADER_APP_ID = 'X-App-Id: ';
    const HEADER_APP_AUTH = 'X-App-Auth: ';
    const HEADER_CONTENT_TYPE = 'Content-Type: ';
    const URLENCODED = 'application/x-www-form-urlencoded';

    private $appId;
    private $appName;
    private $appSecret;
    private $appAuth;
    private $baseUrl;
    private $port;

    /**
     * Creates a PushApi client that contains all the necessary calls in order to use
     * easily the API. It is required to have created an app before to use de client
     * because it is needed in order to be authenticated toward the PushApi.
     * It is also needed the $host {@link $baseUrl} and port {@link $port}.
     * @param [integer] $appId     App identification
     * @param [string]  $appName   App name
     * @param [string]  $appSecret App secret
     * @param [string]  $baseUrl   The host where the API is running
     * @param [integer] $port      The port where the API is running
     */
    function __construct($appId, $appName, $appSecret, $baseUrl, $port)
    {
        $this->setAppId($appId);
        $this->setAppName($appName);
        $this->setAppSecret($appSecret);
        $this->setBaseUrl($baseUrl);
        $this->setPort($port);

        $this->generateAuthentication();
    }

    ////////////////////////////////////
    // MAIN CLASS GETTERS AND SETTERS //
    ////////////////////////////////////

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    public function getAppName()
    {
        return $this->appName;
    }

    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function getAppSecret()
    {
        return $this->appSecret;
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getAppAuth()
    {
        return $this->appAuth;
    }

    /**
     * Generates the required authentication given the needed data of the agent
     * app that wants to use the PushApi.
     */
    private function generateAuthentication()
    {
        if (!isset($this->appName) && !isset($this->appSecret)) {
            throw new Exception("Basic class data not set", -1);
        }
        $this->appAuth = md5($this->appName . date('Y-m-d') . $this->appSecret);
    }


    ////////////////////
    // PUSH API CALLS //
    ////////////////////

    // APP CALLS

    public function getApp($idApp)
    {
        return $this->app(self::GET, $idApp);
    }

    public function updateApp($idApp, $params)
    {
        return $this->app(self::PUT, $idApp, $params);
    }


    // USER CALLS

    public function getUser($idUser)
    {
        return $this->user(self::GET, $idUser);
    }

    public function createUser($params)
    {
        return $this->user(self::POST, 0, $params);
    }

    public function updateUser($idUser, $params)
    {
        return $this->user(self::PUT, $idUser, $params);
    }

    public function deleteUser($idUser)
    {
        return $this->user(self::DELETE, $idUser);
    }

    public function getUsers()
    {
        return $this->users(self::GET);
    }

    public function createUsers($params)
    {
        return $this->users(self::POST, 0, $params);
    }


    // CHANNEL CALLS

    public function getChannel($idChannel)
    {
        return $this->channel(self::GET, $idChannel);
    }

    public function createChannel($params)
    {
        return $this->channel(self::POST, 0, $params);
    }

    public function updateChannel($idChannel, $params)
    {
        return $this->channel(self::PUT, $idChannel, $params);
    }

    public function deleteChannel($idChannel)
    {
        return $this->channel(self::DELETE, $idChannel);
    }

    public function getChannels()
    {
        return $this->channels(self::GET);
    }


    // THEME CALLS

    public function getTheme($idTheme)
    {
        return $this->theme(self::GET, $idTheme);
    }

    public function createTheme($params)
    {
        return $this->theme(self::POST, 0, $params);
    }

    public function updateTheme($idTheme, $params)
    {
        return $this->theme(self::PUT, $idTheme, $params);
    }

    public function deleteTheme($idTheme)
    {
        return $this->theme(self::DELETE, $idTheme);
    }

    public function getThemes()
    {
        return $this->themes(self::GET);
    }

    public function getThemesByRange($range)
    {
        return $this->themesByRange(self::GET, $range);
    }


    // SUBJECT CALLS

    public function getSubject($idSubject)
    {
        return $this->subject(self::GET, $idSubject);
    }

    public function createSubject($params)
    {
        return $this->subject(self::POST, 0, $params);
    }

    public function updateSubject($idSubject, $params)
    {
        return $this->subject(self::PUT, $idSubject, $params);
    }

    public function deleteSubject($idSubject)
    {
        return $this->subject(self::DELETE, $idSubject);
    }

    public function getSubjects()
    {
        return $this->subjects(self::GET);
    }


    // SUBSCRIPTION CALLS

    public function getUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(self::GET, $idUser, $idChannel);
    }

    public function createUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(self::POST, $idUser, $idChannel);
    }

    public function deleteUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(self::DELETE, $idUser, $idChannel);
    }

    public function getUserSubscriptions($idUser)
    {
        return $this->subscriptions(self::GET, $idUser);
    }


    // PREFERENCE CALLS

    public function getUserPreference($idUser, $idTheme)
    {
        return $this->preference(self::GET, $idUser, $idTheme);
    }

    public function createUserPreference($idUser, $idTheme, $params)
    {
        return $this->preference(self::POST, $idUser, $params);
    }

    public function updateUserPreference($idUser, $idTheme, $params)
    {
        return $this->preference(self::PUT, $idUser, $idTheme, $params);
    }

    public function deleteUserPreference($idUser, $idTheme)
    {
        return $this->preference(self::DELETE, $idUser, $idTheme);
    }

    public function getUserPreferences($idUser)
    {
        return $this->preferences(self::GET, $idUser);
    }


    // SEND CALL

    public function sendNotification($params)
    {
        return $this->send(self::POST, $params);
    }



    //////////////////////////////////////////////////////
    // PRIVATED FUNCTIONALITIES THAT CONTAINS THE LOGIC //
    //////////////////////////////////////////////////////

    /**
     * [app description]
     * @param  [type] $request [description]
     * @param  [type] $idApp   [description]
     * @param  [type] $params  [description]
     * @return [type]          [description]
     */
    private function app($request, $idApp, $params = [])
    {
        if ($request == self::POST || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        if ($request == self::PUT && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idApp)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "app/$idApp";
        if (empty($params) && $request == self::PUT) {
            if (!isset($params['name'])) {
                throw new Exception("Invalid params values, expected 'name'", 4);
            }
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        return $this->sendRequest($request, $url);
    }

    /**
     * [user description]
     * @param  [type] $request [description]
     * @param  [type] $idUser  [description]
     * @param  [type] $params  [description]
     * @return [type]          [description]
     */
    private function user($request, $idUser, $params = [])
    {
        if (($request == self::POST || $request == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($request != self::POST && !isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user";
        if ($request == self::POST) {
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        $url .= "/$idUser";
        return $this->sendRequest($request, $url, http_build_query($params));
    }

    /**
     * [users description]
     * @param  [type] $request [description]
     * @param  [type] $emails  [description]
     * @return [type]          [description]
     */
    private function users($request, $params = [])
    {
        if ($request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        if ($request == self::POST && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "users";
        if ($request == self::POST) {
            // Preparing valid params string
            $params = "";
            foreach ($params as $key => $value) {
                $params .= $value . ",";
            }
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        return $this->sendRequest($request, $url);
    }

    /**
     * [channel description]
     * @param  [type] $request   [description]
     * @param  [type] $idChannel [description]
     * @param  [type] $params    [description]
     * @return [type]            [description]
     */
    private function channel($request, $idChannel, $params = [])
    {
        if (($request == self::POST || $request == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($request != self::POST && !isset($idChannel)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "channel";
        if ($request == self::POST) {
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        $url .= "/$idChannel";
        return $this->sendRequest($request, $url, http_build_query($params));
    }

    /**
     * [channels description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    private function channels($request)
    {
        if ($request == self::POST || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        $url = "channels";
        return $this->sendRequest($request, $url);
    }

    /**
     * [theme description]
     * @param  [type] $request [description]
     * @param  [type] $idTheme [description]
     * @param  [type] $params  [description]
     * @return [type]          [description]
     */
    private function theme($request, $idTheme, $params = [])
    {
        if (($request == self::POST || $request == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($request != self::POST && !isset($idTheme)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "theme";
        if ($request == self::POST) {
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        $url .= "/$idTheme";
        return $this->sendRequest($request, $url, http_build_query($params));
    }

    /**
     * [themes description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    private function themes($request)
    {
        if ($request == self::POST || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        $url = "themes";
        return $this->sendRequest($request, $url);
    }

    /**
     * [themesByRange description]
     * @param  [type] $request [description]
     * @param  [type] $range   [description]
     * @return [type]          [description]
     */
    private function themesByRange($request, $range)
    {
        if ($request == self::POST || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "theme/range/$range";
        return $this->sendRequest($request, $url);
    }

    /**
     * [subject description]
     * @param  [type] $request   [description]
     * @param  [type] $idSubject [description]
     * @param  [type] $params    [description]
     * @return [type]            [description]
     */
    private function subject($request, $idSubject, $params = [])
    {
        if (($request == self::POST || $request == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($request != self::POST && !isset($idSubject)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "subject";
        if ($request == self::POST) {
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        $url .= "/$idSubject";
        return $this->sendRequest($request, $url, http_build_query($params));
    }

    /**
     * [subjects description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    private function subjects($request)
    {
        if ($request == self::POST || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        $url = "subjects";
        return $this->sendRequest($request, $url);
    }

    /**
     * [subscription description]
     * @param  [type] $idUser    [description]
     * @param  [type] $idChannel [description]
     * @return [type]            [description]
     */
    private function subscription($request, $idUser, $idChannel)
    {
        if ($request == self::PUT) {
            throw new Exception("Invalid call request", 1);
        }

        if (!isset($idUser) || !isset($idChannel)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/subscribe/$idChannel";
        if ($request == self::POST) {
            return $this->sendRequest($request, $url);
        }
        $url = "user/$idUser/subscribed/$idChannel";
        var_dump($url);
        return $this->sendRequest($request, $url);
    }

    /**
     * [subscriptions description]
     * @param  [type] $idUser [description]
     * @return [type]         [description]
     */
    private function subscriptions($request, $idUser)
    {
        if ($request == self::POST || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/subscribed";
        return $this->sendRequest($request, $url);
    }

    /**
     * [preference description]
     * @param  [type] $request [description]
     * @param  [type] $idUser  [description]
     * @param  [type] $idTheme [description]
     * @param  [type] $params  [description]
     * @return [type]          [description]
     */
    private function preference($request, $idUser, $idTheme, $params = [])
    {
        if (($request == self::POST || $request == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idUser) || !isset($idTheme)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/preference/$idTheme";
        if ($request == self::POST) {
            return $this->sendRequest($request, $url, http_build_query($params));
        }
        return $this->sendRequest($request, $url);
    }

    /**
     * [preferences description]
     * @param  [type] $request [description]
     * @param  [type] $idUser  [description]
     * @return [type]          [description]
     */
    private function preferences($request, $idUser)
    {
        if ($request == self::POST || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/preferences";
        return $this->sendRequest($request, $url);
    }

    /**
     * [send description]
     * @param  [type] $request [description]
     * @param  [type] $params  [description]
     * @return [type]          [description]
     */
    private function send($request, $params)
    {
        if ($request == self::GET || $request == self::PUT || $request == self::DELETE) {
            throw new Exception("Invalid call request", 1);
        }

        if (empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "send";
        return $this->sendRequest($request, $url, http_build_query($params));
    }

    /**
     * [sendRequest description]
     * @param  string  $request [description]
     * @param  [type]  $url     [description]
     * @param  boolean $params  [description]
     * @return [type]           [description]
     */
    private function sendRequest($request, $path, $params = false)
    {
        // Preparing HTTP headers
        $headers = array(
            self::HEADER_APP_ID . $this->getAppId(),
            self::HEADER_APP_AUTH . $this->getAppAuth()
        );

        // Preparing HTTP connection
        $ch = curl_init();
 
        if ($request == self::POST || $request == self::PUT) {
            array_push($headers, self::HEADER_CONTENT_TYPE . self::URLENCODED);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . $path);
        curl_setopt($ch, CURLOPT_PORT, $this->getPort());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $curlResponse = curl_exec($ch);
        $curlHeaders = curl_getinfo($ch);

        // Fetching results or failing if doesn't work
        if ($curlResponse === false) {
            throw new Exception("Connection failed: " . curl_error($ch), -2);
        }
 
        // Closing the HTTP connection
        curl_close($ch);

        return $this->parseCurlResponse($curlResponse, $curlHeaders);
    }

    /**
     * [parseCurlResponse description]
     * @param  [type] $curlResponse [description]
     * @param  [type] $curlHeaders  [description]
     * @return [type]               [description]
     */
    private function parseCurlResponse($curlResponse, $curlHeaders)
    {
        $curlHeadersSize = $curlHeaders['header_size'];

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
        
        if ($curlHeaders['http_code'] != 200) {
            throw new Exception($sortedHeaders['X-Status-Reason'], $curlHeaders['http_code']);
        } else {
            return json_decode($responseBody, true);
        }
    }

    // private function maskErrorResponse($code)
    // {

    // }
}
