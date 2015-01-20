<?php

/**
 * PushApi_Client
 * Recommended to have:
 *     - basic knowledge about what the API does
 *     - what are the API functionalities
 *     - what params are required for each API call
 *
 * This is only a library that sends commands to the PushApi, the API must be running in a server
 * (the API project is here https://github.com/watzenare/PushApi).
 *
 * The Client can use all functionalities of the API less deleting an app or list all the registered apps.
 *
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 *
 * A PHP standalone client that facilitates to developers the use of all the PushApi functionalities.
 *
 * Warning: when a call need @param $params, this params must be send in an array and each key name must
 * be the expected request param that the API expects.
 *
 * App:
 * @method getApp($idApp) Gets information about specific $idApp
 * @method updateApp($idApp, $params) Updates the information of the specific $idApp
 *
 * User:
 * @method getUser($idUser)  Gets the specific $idUser information
 * @method createUser($params)  Creates an user given $params information
 * @method updateUser($idUser, $params)  Updates a specific $idUser given its $params
 * @method deleteUser($idUser)  Deletes an specific $idUser
 * @method getUsers()  Retrieves information about all registered users
 * @method createUsers($params)  Creates multiple users given its emails
 *
 * User Subscriptions:
 * @method getUserSubscription($idUser, $idChannel)  Gets the specific $idUser subscription given a specific $idChannel
 * @method createUserSubscription($idUser, $idChannel)  Sets a subscription to a specific $idUser from a specific $idChannel given $params information
 * @method deleteUserSubscription($idUser, $idChannel)  Unsubscribes a specific $idUser from a specific $idChannel
 * @method getUserSubscriptions($idUser)  Retrieves information about all $idUser subscriptions
 *
 * User Preferences:
 * @method getUserPreference($idUser, $idTheme)  Gets the specific $idUser preference given a specific $idTheme
 * @method createUserPreference($idUser, $idTheme, $params)  Sets a preference to a specific $idUser from a specific $idTheme given $params information
 * @method updateUserPreference($idUser, $idTheme, $params)  Updates a specific $idTheme preference from a specific $idUser given its $params
 * @method deleteUserPreference($idUser, $idTheme)  Unsets a specific $idUser preference from a specific $idTheme
 * @method getUserPreferences($idUser)  Retrieves information about all $idUser preferences
 *
 * Channel:
 * @method getChannel($idChannel)  Gets the specific $idChannel information
 * @method createChannel($params)  Creates a channel given $params information
 * @method updateChannel($idChannel, $params)  Updates a specific $idChannel given its $params
 * @method deleteChannel($idChannel)  Deletes a specific $idChannel
 * @method getChannels()  Retrieves information about all registered channels
 * @method getChannelByName($params)  Gets the specific channel given its name
 *
 * Theme:
 * @method getTheme($idTheme)  Gets the specific $idTheme information
 * @method createTheme($params)  Creates a theme given $params information
 * @method updateTheme($idTheme, $params)  Updates a specific $idTheme given its $params
 * @method deleteTheme($idTheme)  Deletes a specific $idTheme
 * @method getThemes()  Retrieves information about all registered themes
 * @method getThemesByRange($range)  Retrieves information about all registered themes by specific $range
 * @method getThemeByName($params)  Gets the specific theme given its name
 *
 * Subject:
 * @method getSubject($idSubject)  Gets the specific $idSubject information
 * @method createSubject($params)  Creates a subject given $params information
 * @method updateSubject($idSubject, $params)  Updates a specific $idSubject given its $params
 * @method deleteSubject($idSubject)  Deletes a specific $idSubject
 * @method getSubjects()  Retrieves information about all registered subjects
 *
 * Send:
 * @method sendNotification($params)  Sends a notification to the target specified into the $params
 */
class PushApi_Client
{
    /**
     * Main methods that support the PushApi
     */
    const GET = "GET";
    const PUT = "PUT";
    const POST = "POST";
    const DELETE = "DELETE";

    /**
     * Agent app identification
     * @var integer
     */
    private $appId;

    /**
     * Agent app name
     * @var string
     */
    private $appName;

    /**
     * Agent app secret
     * @var string
     */
    private $appSecret;

    /**
     * Agent app authentication
     * @var string
     */
    private $appAuth;

    /**
     * An instance of any RequestManager class
     * @var RequestManager
     */
    private $requestManager;


    /**
     * Creates a PushApi client that contains all the necessary calls in order to use
     * easily the API. It is required to have created an app before to use de client
     * because it is needed in order to be authenticated toward the PushApi.
     *
     * @param integer  $appId  App identification
     * @param string  $appName  App name
     * @param string  $appSecret  App secret
     * @param RequestManager  $requestManager  An instance of any RequestManager class
     */
    function __construct($appId, $appName, $appSecret, $requestManager)
    {
        // Setting Client private vars
        $this->setAppId($appId);
        $this->setAppName($appName);
        $this->setAppSecret($appSecret);
        $this->setRequestManager($requestManager);
        // Generating the app auth given authentication params
        $this->generateAuth();
        // Setting RequestManager header app params
        $this->requestManager->setAppId($appId);
        $this->requestManager->setAppAuth($this->getAppAuth());
    }


    /////////////////////////////////////////////////////////////////
    //               MAIN CLASS GETTERS AND SETTERS                //
    /////////////////////////////////////////////////////////////////

    /**
     * Sets the app identification
     * @param integer  $appId
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
     * Sets the app name
     * @param string  $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * Returns the app name
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Sets the app secret
     * @param string  $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * Returns the app secret
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * Returns the app auth
     * @return string
     */
    public function getAppAuth()
    {
        return $this->appAuth;
    }

    /**
     * Generates the required authentication given the needed data of the agent
     * app that wants to use the PushApi.
     *
     * @throws Exception  If [this condition is met]
     */
    private function generateAuth()
    {
        if (!isset($this->appName) && !isset($this->appSecret)) {
            throw new Exception("Basic class data not set, expected appName and appSecret", -1);
        }
        $this->appAuth = md5($this->appName . date("Y-m-d") . $this->appSecret);
    }

    /**
     * Sets an instance of a class type RequestManager
     * @param RequestManager  $requestManager
     */
    private function setRequestManager($requestManager)
    {
        $this->requestManager = $requestManager;
    }

    /**
     * Gets an instance of a class type RequestManager
     * @return RequestManager
     */
    private function getRequestManager()
    {
        return $this->requestManager;
    }


    ///////////////////////////////////////////////
    //              PUSH API CALLS               //
    ///////////////////////////////////////////////

    //////////////////////////
    //      APP CALLS       //
    //////////////////////////
    /**
     * Gets information about specific $idApp
     * @param  integer  $idApp  App identification
     * @return array  Response key => value array
     */
    public function getApp($idApp)
    {
        return $this->app(self::GET, $idApp);
    }

    /**
     * Updates the information of the specific $idApp
     * @param  integer  idApp  App identification
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function updateApp($idApp, $params)
    {
        return $this->app(self::PUT, $idApp, $params);
    }


    ///////////////////////////
    //      USER CALLS       //
    ///////////////////////////
    /**
     * Gets the specific $idUser information
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     */
    public function getUser($idUser)
    {
        return $this->user(self::GET, $idUser);
    }

    /**
     * Creates an user given $params information
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function createUser($params)
    {
        return $this->user(self::POST, 0, $params);
    }

    /**
     * Updates a specific $idUser given its $params
     * @param  integer  $idUser  User identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function updateUser($idUser, $params)
    {
        return $this->user(self::PUT, $idUser, $params);
    }

    /**
     * Deletes an specific $idUser
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     */
    public function deleteUser($idUser)
    {
        return $this->user(self::DELETE, $idUser);
    }

    /**
     * Retrieves information about all registered users
     * @return array  Response key => value array
     */
    public function getUsers()
    {
        return $this->users(self::GET);
    }

    /**
     * Creates multiple users given its emails
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function createUsers($params)
    {
        return $this->users(self::POST, $params);
    }


    //////////////////////////////
    //      CHANNEL CALLS       //
    //////////////////////////////
    /**
     * Gets the specific $idChannel information
     * @param  integer  $idChannel  Channel identification value
     * @return array  Response key => value array
     */
    public function getChannel($idChannel)
    {
        return $this->channel(self::GET, $idChannel);
    }

    /**
     * Creates an channel given $params information
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function createChannel($params)
    {
        return $this->channel(self::POST, 0, $params);
    }

    /**
     * Updates a specific $idChannel given its $params
     * @param  integer  $idChannel  Channel identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function updateChannel($idChannel, $params)
    {
        return $this->channel(self::PUT, $idChannel, $params);
    }

    /**
     * Deletes an specific $idChannel
     * @param  integer  $idChannel  Channel identification value
     * @return array  Response key => value array
     */
    public function deleteChannel($idChannel)
    {
        return $this->channel(self::DELETE, $idChannel);
    }

    /**
     * Retrieves information about all registered channels
     * @return array  Response key => value array
     */
    public function getChannels()
    {
        return $this->channels(self::GET);
    }

    /**
     * Gets the specific channel given its name
     * @param  array  $params  Channel identification value
     * @return array  Response key => value array
     */
    public function getChannelByName($params)
    {
        return $this->channel(self::GET, 0, $params);
    }


    ////////////////////////////
    //      THEME CALLS       //
    ////////////////////////////
    /**
     * Gets the specific $idTheme information
     * @param  integer  $idTheme  Theme identification value
     * @return array  Response key => value array
     */
    public function getTheme($idTheme)
    {
        return $this->theme(self::GET, $idTheme);
    }

    /**
     * Creates an theme given $params information
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function createTheme($params)
    {
        return $this->theme(self::POST, 0, $params);
    }

    /**
     * Updates a specific $idTheme given its $params
     * @param  integer  $idTheme  Theme identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function updateTheme($idTheme, $params)
    {
        return $this->theme(self::PUT, $idTheme, $params);
    }

    /**
     * Deletes an specific $idTheme
     * @param  integer  $idTheme  Theme identification value
     * @return array  Response key => value array
     */
    public function deleteTheme($idTheme)
    {
        return $this->theme(self::DELETE, $idTheme);
    }

    /**
     * Retrieves information about all registered themes
     * @return array  Response key => value array
     */
    public function getThemes()
    {
        return $this->themes(self::GET);
    }

    /**
     * Retrieves information about all registered themes by specific $range
     * @param  string  $range The range that a theme can have
     * @return array  Response key => value array
     */
    public function getThemesByRange($range)
    {
        return $this->themesByRange(self::GET, $range);
    }

    /**
     * Gets the specific theme given its name
     * @param  array  $params  Theme identification value
     * @return array  Response key => value array
     */
    public function getThemeByName($params)
    {
        return $this->theme(self::GET, 0, $params);
    }


    //////////////////////////////
    //      SUBJECT CALLS       //
    //////////////////////////////
    /**
     * Gets the specific $idSubject information
     * @param  integer  $idSubject  Subject identification value
     * @return array  Response key => value array
     */
    public function getSubject($idSubject)
    {
        return $this->subject(self::GET, $idSubject);
    }

    /**
     * Creates an subject given $params information
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function createSubject($params)
    {
        return $this->subject(self::POST, 0, $params);
    }

    /**
     * Updates a specific $idSubject given its $params
     * @param  integer  $idSubject  Subject identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function updateSubject($idSubject, $params)
    {
        return $this->subject(self::PUT, $idSubject, $params);
    }

    /**
     * Deletes an specific $idSubject
     * @param  integer  $idSubject  Subject identification value
     * @return array  Response key => value array
     */
    public function deleteSubject($idSubject)
    {
        return $this->subject(self::DELETE, $idSubject);
    }

    /**
     * Retrieves information about all registered subjects
     * @return array  Response key => value array
     */
    public function getSubjects()
    {
        return $this->subjects(self::GET);
    }


    ///////////////////////////////////
    //      SUBSCRIPTION CALLS       //
    ///////////////////////////////////
    /**
     * Gets the specific $idUser subscription given a specific $idChannel
     * @param  integer  $idUser  User identification value
     * @param  integer  $idChannel  Channel identification value
     * @return array  Response key => value array
     */
    public function getUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(self::GET, $idUser, $idChannel);
    }

    /**
     * Sets a subscription to a specific $idUser from a specific $idChannel given $params information
     * @param  integer  $idUser  User identification value
     * @param  integer  $idChannel  Channel identification value
     * @return array  Response key => value array
     */
    public function createUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(self::POST, $idUser, $idChannel);
    }

    /**
     * Unsubscribes a specific $idUser from a specific $idChannel
     * @param  integer  $idUser  User identification value
     * @param  integer  $idChannel  Channel identification value
     * @return array  Response key => value array
     */
    public function deleteUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(self::DELETE, $idUser, $idChannel);
    }

    /**
     * Retrieves information about all $idUser subscriptions
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     */
    public function getUserSubscriptions($idUser)
    {
        return $this->subscriptions(self::GET, $idUser);
    }


    /////////////////////////////////
    //      PREFERENCE CALLS       //
    /////////////////////////////////
    /**
     * Gets the specific $idUser preference given a specific $idTheme
     * @param  integer  $idUser  User identification value
     * @param  integer  $idTheme  Theme identification value
     * @return array  Response key => value array
     */
    public function getUserPreference($idUser, $idTheme)
    {
        return $this->preference(self::GET, $idUser, $idTheme);
    }

    /**
     * Sets a preference to a specific $idUser from a specific $idTheme given $params information
     * @param  integer  $idUser  User identification value
     * @param  integer  $idTheme  Theme identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function createUserPreference($idUser, $idTheme, $params)
    {
        return $this->preference(self::POST, $idUser, $idTheme, $params);
    }

    /**
     * Updates a specific $idTheme preference from a specific $idUser given its $params
     * @param  integer  $idUser  User identification value
     * @param  integer  $idTheme  Theme identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function updateUserPreference($idUser, $idTheme, $params)
    {
        return $this->preference(self::PUT, $idUser, $idTheme, $params);
    }

    /**
     * Unsets a specific $idUser preference from a specific $idTheme
     * @param  integer  $idUser  User identification value
     * @param  integer  $idTheme  Theme identification value
     * @return array  Response key => value array
     */
    public function deleteUserPreference($idUser, $idTheme)
    {
        return $this->preference(self::DELETE, $idUser, $idTheme);
    }

    /**
     * Retrieves information about all $idUser preferences
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     */
    public function getUserPreferences($idUser)
    {
        return $this->preferences(self::GET, $idUser);
    }


    //////////////////////////
    //      SEND CALL       //
    //////////////////////////
    /**
     * Sends a notification to the target specified into the $params
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     */
    public function sendNotification($params)
    {
        return $this->send(self::POST, $params);
    }



    ///////////////////////////////////////////////////////////////////////////////
    //              PRIVATED FUNCTIONALITIES WITH THE CLIENT LOGIC               //
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idApp   App identification
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params PUT content: ["name" => "new_name"]
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function app($method, $idApp, $params = [])
    {
        if ($method == self::POST || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if ($method == self::PUT && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idApp)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "app/$idApp";
        $request = $this->getRequestManager();
        try {
            if ($method == self::PUT) {
                return $request->sendRequest($method, $url, $params);
            }
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idUser  User identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["email" => "a@a.com"]
     * @example $params PUT content: ["email" => "new@a.com", "android_id" => "12345"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function user($method, $idUser, $params = [])
    {
        if (($method == self::POST || $method == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != self::POST && !isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST) {
                return $request->sendRequest($method, $url, $params);
            }
            $url .= "/$idUser";
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["emails" => "a@a.com,b@b.com,c@c.com"]
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [No @param $params are set]
     */
    private function users($method, $params = [])
    {
        if ($method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if ($method == self::POST && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "users";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST) {
                return $request->sendRequest($method, $url, $params);
            }
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idChannel  Channel identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params POST & PUT content: ["name" => "channel_name"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function channel($method, $idChannel, $params = [])
    {
        if (($method == self::POST || $method == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != self::POST && !isset($idChannel)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "channel";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST) {
                return $request->sendRequest($method, $url, $params);
            }
            if (!empty($params) && $method == self::GET) {
                $url = "channel_name";
                return $request->sendRequest($method, $url, $params);
            }
            $url .= "/$idChannel";
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     */
    private function channels($method)
    {
        if ($method == self::POST || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "channels";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idTheme  Theme identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params POST & PUT content: ["name" => "theme_name", "range" => "unicast"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function theme($method, $idTheme, $params = [])
    {
        if (($method == self::POST || $method == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != self::POST && !isset($idTheme)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "theme";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST) {
                return $request->sendRequest($method, $url, $params);
            }
            if (!empty($params) && $method == self::GET) {
                $url = "theme_name";
                return $request->sendRequest($method, $url, $params);
            }
            $url .= "/$idTheme";
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     */
    private function themes($method)
    {
        if ($method == self::POST || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "themes";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  string  $range The range value that a theme can have
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function themesByRange($method, $range)
    {
        if ($method == self::POST || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($range)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "themes/range/$range";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idSubject  Subject identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params POST & PUT content: ["theme_name" => "name_theme", "description" => "this is a description example"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function subject($method, $idSubject, $params = [])
    {
        if (($method == self::POST || $method == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != self::POST && !isset($idSubject)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "subject";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST) {
                return $request->sendRequest($method, $url, $params);
            }
            $url .= "/$idSubject";
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     */
    private function subjects($method)
    {
        if ($method == self::POST || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "subjects";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  integer  $idUser  User identification value
     * @param  integer  $idChannel  Channel identification value
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function subscription($method, $idUser, $idChannel)
    {
        if ($method == self::PUT) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($idUser) || !isset($idChannel)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/subscribe/$idChannel";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST) {
                return $request->sendRequest($method, $url);
            }
            $url = "user/$idUser/subscribed/$idChannel";
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function subscriptions($method, $idUser)
    {
        if ($method == self::POST || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/subscribed";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idUser  User identification value
     * @param  integer  $idTheme  Theme identification value
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["option" => 2]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function preference($method, $idUser, $idTheme, $params = [])
    {
        if (($method == self::POST || $method == self::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idUser) || !isset($idTheme)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/preference/$idTheme";
        $request = $this->getRequestManager();
        try {
            if ($method == self::POST || $method == self::PUT) {
                return $request->sendRequest($method, $url, $params);
            }
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function preferences($method, $idUser)
    {
        if ($method == self::POST || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referer id", 3);
        }

        $url = "user/$idUser/preferences";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method)
     * @param  string  $method  HTTP method of the request
     * @param  array  $params  Array with the required params as keys (used with PUT && POST mothod)
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["theme" => "theme_name", "message" => "Message notification"]
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [No @param $params are set]
     */
    private function send($method, $params)
    {
        if ($method == self::GET || $method == self::PUT || $method == self::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if (empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "send";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}