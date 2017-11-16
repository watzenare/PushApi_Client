<?php

use \RequestManagers\RequestManager;

/**
 * PushApi_Client
 * Recommended to have:
 *     - basic knowledge about what the API does and its methods
 *     - required params for each API call
 *
 * This is only a library that sends commands to the PushApi, the API must be running in a server
 * (the API project is here https://github.com/watzenare/PushApi).
 *
 * The Client can use all methods of the API less deleting an app or list all the registered apps.
 *
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 *
 * A PHP standalone client that facilitates to developers the use of all the PushApi methods.
 *
 * Warning: when a call need @param $params, this params must be send in an array and each key name must
 * be the expected request param that the API expects.
 *
 * APP METHODS:
 * getApp($idApp) Gets information about specific $idApp
 * updateApp($idApp, $params) Updates the information of the specific $idApp
 *
 * USER METHODS:
 * getUser($idUser)  /user/$idUser
 * createUser($params)  /user
 * deleteUser($idUser)  /user/$idUser
 * addUserDevice($idUser, $params)  /user/$idUser/device
 * getUserDeviceByReference($idUser, $params)  /user/$idUser/device
 * getUserDevice($idUser, $idDevice)  /user/$idUser/device/$idDevice
 * deleteUserDevice($idUser, $idDevice)  /user/$idUser/device/$idDevice
 * deleteUserDevicesByType($idUser, $type)  /user/$idUser/device/type/$type
 * getUsers($params)  /users
 * createUsers($params)  /users
 * getUserSmartphones($idUser)  /user/$idUser/smartphones
 *
 * USER SUBSCRIPTIONS METHODS:
 * getUserSubscription($idUser, $idChannel)  /user/$idUser/subscribe/$idChannel
 * createUserSubscription($idUser, $idChannel)  /user/$idUser/subscribe/$idChannel
 * deleteUserSubscription($idUser, $idChannel)  /user/$idUser/subscribe/$idChannel
 * getUserSubscriptions($idUser)  /user/$idUser/subscribed
 *
 * USER PREFERENCES METHODS:
 * getUserPreference($idUser, $idTheme)  /user/$idUser/preference/$idTheme
 * createUserPreference($idUser, $idTheme, $params)  /user/$idUser/preference/$idTheme
 * updateUserPreference($idUser, $idTheme, $params)  /user/$idUser/preference/$idTheme
 * deleteUserPreference($idUser, $idTheme)  /user/$idUser/preference/$idTheme
 * getUserPreferences($idUser)  /user/$idUser/preferences
 * updateAllUserPreferences($idUser, $params)  /user/$idUser/preferences
 *
 * CHANNEL METHODS:
 * getChannel($idChannel)  /channel/$idChannel
 * createChannel($params) /channel
 * updateChannel($idChannel, $params)  /channel/$idChannel
 * deleteChannel($idChannel)  /channel/$idChannel
 * getChannels($params)  /channels
 * getChannelByName($params)  /channel_name
 *
 * THEME METHODS:
 * getTheme($idTheme)  /theme/$idTheme
 * createTheme($params)  /theme
 * updateTheme($idTheme, $params)  /theme/$idTheme
 * deleteTheme($idTheme)  /theme/$idTheme
 * getThemes($params)  /themes
 * getThemesByRange($range, $params)  /themes/range/$range
 * getThemeByName($params)  /theme_name
 *
 * SUBJECT METHODS:
 * getSubject($idSubject)  /subject/$idSubject
 * createSubject($params)  /subject
 * updateSubject($idSubject, $params)  /subject/$idSubject
 * deleteSubject($idSubject)  /subject/$idSubject
 * getSubjects($params)  /subjects
 *
 * SEND METHODS:
 * sendNotification($params)  /send
 */
class PushApi_Client
{
    /**
     * Agent app identification.
     * @var integer
     */
    private $appId;

    /**
     * Agent app name.
     * @var string
     */
    private $appName;

    /**
     * Agent app secret.
     * @var string
     */
    private $appSecret;

    /**
     * Agent app authentication.
     * @var string
     */
    private $appAuth;

    /**
     * An instance of any RequestManager class.
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
     * Sets the app identification.
     * @param integer  $appId
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
     * Sets the app name.
     * @param string  $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * Returns the app name.
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Sets the app secret.
     * @param string  $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * Returns the app secret.
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
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
     * Generates the required authentication given the needed data of the agent.
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
     * Sets an instance of a class type RequestManager.
     * @param RequestManager  $requestManager
     */
    private function setRequestManager($requestManager)
    {
        $this->requestManager = $requestManager;
    }

    /**
     * Gets an instance of a class type RequestManager.
     * @return RequestManager
     */
    private function getRequestManager()
    {
        return $this->requestManager;
    }

    /**
     * Sets the method of the transmission wanted (synchronous or asynchronous) to the RequestManager.
     * @param string $method
     */
    public function setTransmission($method)
    {
        $this->requestManager->setTransmission($method);
    }

    /**
     * Gets the transmission method by the RequestManager.
     * @return string
     */
    public function getTransmission()
    {
        return $this->requestManager->getTransmission();
    }


    ///////////////////////////////////////////////
    //              PUSH API CALLS               //
    ///////////////////////////////////////////////

    //////////////////////////
    //      APP CALLS       //
    //////////////////////////
    /**
     * Gets information about specific $idApp.
     * @param  integer  $idApp  App identification
     * @return array  Response key => value array
     */
    public function getApp($idApp)
    {
        return $this->app(RequestManager::GET, $idApp);
    }

    /**
     * Updates the information of the specific $idApp.
     * @param  integer  $idApp  App identification
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method)
     * @return array  Response key => value array
     */
    public function updateApp($idApp, $params)
    {
        return $this->app(RequestManager::PUT, $idApp, $params);
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
        return $this->user(RequestManager::GET, $idUser);
    }

    /**
     * Creates an user given $params information.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method)
     * @return array  Response key => value array
     */
    public function createUser($params)
    {
        return $this->user(RequestManager::POST, 0, $params);
    }

    /**
     * Deletes an specific $idUser.
     * @param  integer  $idUser  User identification value
     * @return array  Response key => value array
     */
    public function deleteUser($idUser)
    {
        return $this->user(RequestManager::DELETE, $idUser);
    }

    /**
     * Creates a device given the reference given by params.
     * @param  integer  $idUser  User identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function addUserDevice($idUser, $params)
    {
        return $this->device(RequestManager::POST, $idUser, false, $params);
    }

    /**
     * Retrieves the user device information given its reference by params.
     * @param  integer  $idUser  User identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function getUserDeviceByReference($idUser, $params)
    {
        return $this->device(RequestManager::GET, $idUser, false, $params);
    }

    /**
     * Retrieves the user device information given its $idDevice.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idDevice  Device identification value.
     * @return array  Response key => value array
     */
    public function getUserDevice($idUser, $idDevice)
    {
        return $this->device(RequestManager::GET, $idUser, $idDevice);
    }

    /**
     * Deletes an specific $idDevice.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idDevice  Device identification value.
     * @return array  Response key => value array
     */
    public function deleteUserDevice($idUser, $idDevice)
    {
        return $this->device(RequestManager::DELETE, $idUser, $idDevice);
    }

    /**
     * Deletes an specific type of devices of the $idUser.
     * @param  integer  $idUser  User identification value.
     * @param  string  $type  Device type identification.
     * @return array  Response key => value array
     */
    public function deleteUserDevicesByType($idUser, $type)
    {
        return $this->devicesByType(RequestManager::DELETE, $idUser, $type);
    }

    /**
     * Retrieves information about all registered users.
     * @return array  Response key => value array
     */
    public function getUsers($params = [])
    {
        return $this->users(RequestManager::GET, $params);
    }

    /**
     * Creates multiple users given its emails.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function createUsers($params)
    {
        return $this->users(RequestManager::POST, $params);
    }

    /**
     * Retrieves the smartphones that user has registered.
     * @param  integer  $idUser  User identification value.
     * @return array  Response key => value array
     */
    public function getUserSmartphones($idUser)
    {
        return $this->userSmartphones(RequestManager::GET, $idUser);
    }


    //////////////////////////////
    //      CHANNEL CALLS       //
    //////////////////////////////
    /**
     * Gets the specific $idChannel information.
     * @param  integer  $idChannel  Channel identification value.
     * @return array  Response key => value array
     */
    public function getChannel($idChannel)
    {
        return $this->channel(RequestManager::GET, $idChannel);
    }

    /**
     * Creates an channel given $params information.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function createChannel($params)
    {
        return $this->channel(RequestManager::POST, 0, $params);
    }

    /**
     * Updates a specific $idChannel given its $params.
     * @param  integer  $idChannel  Channel identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function updateChannel($idChannel, $params)
    {
        return $this->channel(RequestManager::PUT, $idChannel, $params);
    }

    /**
     * Deletes an specific $idChannel.
     * @param  integer  $idChannel  Channel identification value.
     * @return array  Response key => value array
     */
    public function deleteChannel($idChannel)
    {
        return $this->channel(RequestManager::DELETE, $idChannel);
    }

    /**
     * Retrieves information about all registered channels.
     * @return array  Response key => value array
     */
    public function getChannels($params = [])
    {
        return $this->channels(RequestManager::GET, $params);
    }

    /**
     * Gets the specific channel given its name.
     * @param  array  $params  Channel identification value.
     * @return array  Response key => value array
     */
    public function getChannelByName($params)
    {
        return $this->channelByName(RequestManager::GET, $params);
    }


    ////////////////////////////
    //      THEME CALLS       //
    ////////////////////////////
    /**
     * Gets the specific $idTheme information.
     * @param  integer  $idTheme  Theme identification value.
     * @return array  Response key => value array
     */
    public function getTheme($idTheme)
    {
        return $this->theme(RequestManager::GET, $idTheme);
    }

    /**
     * Creates an theme given $params information.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function createTheme($params)
    {
        return $this->theme(RequestManager::POST, 0, $params);
    }

    /**
     * Updates a specific $idTheme given its $params.
     * @param  integer  $idTheme  Theme identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function updateTheme($idTheme, $params)
    {
        return $this->theme(RequestManager::PUT, $idTheme, $params);
    }

    /**
     * Deletes an specific $idTheme.
     * @param  integer  $idTheme  Theme identification value.
     * @return array  Response key => value array
     */
    public function deleteTheme($idTheme)
    {
        return $this->theme(RequestManager::DELETE, $idTheme);
    }

    /**
     * Retrieves information about all registered themes.
     * @return array  Response key => value array
     */
    public function getThemes($params = [])
    {
        return $this->themes(RequestManager::GET, $params);
    }

    /**
     * Retrieves information about all registered themes by specific $range.
     * @param  string  $range The range that a theme can have.
     * @return array  Response key => value array
     */
    public function getThemesByRange($range, $params = [])
    {
        return $this->themesByRange(RequestManager::GET, $range, $params);
    }

    /**
     * Gets the specific theme given its name.
     * @param  array  $params  Theme identification value.
     * @return array  Response key => value array
     */
    public function getThemeByName($params)
    {
        return $this->themeByName(RequestManager::GET, $params);
    }


    //////////////////////////////
    //      SUBJECT CALLS       //
    //////////////////////////////
    /**
     * Gets the specific $idSubject information.
     * @param  integer  $idSubject  Subject identification value.
     * @return array  Response key => value array
     */
    public function getSubject($idSubject)
    {
        return $this->subject(RequestManager::GET, $idSubject);
    }

    /**
     * Creates an subject given $params information.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function createSubject($params)
    {
        return $this->subject(RequestManager::POST, 0, $params);
    }

    /**
     * Updates a specific $idSubject given its $params.
     * @param  integer  $idSubject  Subject identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function updateSubject($idSubject, $params)
    {
        return $this->subject(RequestManager::PUT, $idSubject, $params);
    }

    /**
     * Deletes an specific $idSubject
     * @param  integer  $idSubject  Subject identification value.
     * @return array  Response key => value array
     */
    public function deleteSubject($idSubject)
    {
        return $this->subject(RequestManager::DELETE, $idSubject);
    }

    /**
     * Retrieves information about all registered subjects.
     * @return array  Response key => value array
     */
    public function getSubjects($params = [])
    {
        return $this->subjects(RequestManager::GET, $params);
    }


    ///////////////////////////////////
    //      SUBSCRIPTION CALLS       //
    ///////////////////////////////////
    /**
     * Gets the specific $idUser subscription given a specific $idChannel.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idChannel  Channel identification value.
     * @return array  Response key => value array
     */
    public function getUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(RequestManager::GET, $idUser, $idChannel);
    }

    /**
     * Sets a subscription to a specific $idUser from a specific $idChannel given $params information.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idChannel  Channel identification value.
     * @return array  Response key => value array
     */
    public function createUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(RequestManager::POST, $idUser, $idChannel);
    }

    /**
     * Unsubscribes a specific $idUser from a specific $idChannel.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idChannel  Channel identification value.
     * @return array  Response key => value array
     */
    public function deleteUserSubscription($idUser, $idChannel)
    {
        return $this->subscription(RequestManager::DELETE, $idUser, $idChannel);
    }

    /**
     * Retrieves information about all $idUser subscriptions.
     * @param  integer  $idUser  User identification value.
     * @return array  Response key => value array
     */
    public function getUserSubscriptions($idUser)
    {
        return $this->subscriptions(RequestManager::GET, $idUser);
    }


    /////////////////////////////////
    //      PREFERENCE CALLS       //
    /////////////////////////////////
    /**
     * Gets the specific $idUser preference given a specific $idTheme.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idTheme  Theme identification value.
     * @return array  Response key => value array
     */
    public function getUserPreference($idUser, $idTheme)
    {
        return $this->preference(RequestManager::GET, $idUser, $idTheme);
    }

    /**
     * Sets a preference to a specific $idUser from a specific $idTheme given $params information.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idTheme  Theme identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function createUserPreference($idUser, $idTheme, $params)
    {
        return $this->preference(RequestManager::POST, $idUser, $idTheme, $params);
    }

    /**
     * Updates a specific $idTheme preference from a specific $idUser given its $params.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idTheme  Theme identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function updateUserPreference($idUser, $idTheme, $params)
    {
        return $this->preference(RequestManager::PUT, $idUser, $idTheme, $params);
    }

    /**
     * Unset a specific $idUser preference from a specific $idTheme.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idTheme  Theme identification value.
     * @return array  Response key => value array
     */
    public function deleteUserPreference($idUser, $idTheme)
    {
        return $this->preference(RequestManager::DELETE, $idUser, $idTheme);
    }

    /**
     * Retrieves information about all $idUser preferences.
     * @param  integer  $idUser  User identification value.
     * @return array  Response key => value array
     */
    public function getUserPreferences($idUser)
    {
        return $this->preferences(RequestManager::GET, $idUser);
    }

    /**
     * Updates all preferences of the $idUser with the same value (even preferences of themes that has not been set yet).
     * @param  integer  $idUser  User identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function updateAllUserPreferences($idUser, $params)
    {
        return $this->preferences(RequestManager::PUT, $idUser, $params);
    }


    //////////////////////////
    //      SEND CALL       //
    //////////////////////////
    /**
     * Sends a notification to the target specified into the $params.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     */
    public function sendNotification($params = [])
    {
        return $this->send(RequestManager::POST, $params);
    }



    ///////////////////////////////////////////////////////////////////////////////
    //              PRIVATE FUNCTIONALITIES WITH THE CLIENT LOGIC               //
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idApp   App identification.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
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
        if ($method == RequestManager::POST || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if ($method == RequestManager::PUT && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idApp)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "app/$idApp";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idUser  User identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
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
        if (($method == RequestManager::POST) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != RequestManager::POST && !isset($idUser)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "user";
        $request = $this->getRequestManager();
        try {
            if ($method != RequestManager::POST) {
                $url .= "/$idUser";
            }
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idUser  User identification value.
     * @param  integer|false $idDevice  Device identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params POST content: ["android" => "XXXX-XXX-XXX-XXXX-X", "ios" => "Z-ZZ-ZZZ-ZZ-Z"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function device($method, $idUser, $idDevice = false, $params = [])
    {
        if (($method == RequestManager::POST) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting user referrer id", 3);
        }

        if (($method == RequestManager::GET && $idDevice == false) && empty($params)) {
            throw new Exception("Search cannot be done without params", 4);
        }

        if ($method == RequestManager::DELETE && $idDevice == false) {
            throw new Exception("Url can't be created, expecting device referrer id", 3);
        }

        $url = "user/$idUser/device";
        $request = $this->getRequestManager();
        try {
            if (($method == RequestManager::GET || $method == RequestManager::DELETE) && $idDevice) {
                $url .= "/$idDevice";
            }
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idUser  User identification value.
     * @param  string  $type  Device type identification value.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Method is invalid]
     */
    private function devicesByType($method, $idUser, $type)
    {
        if ($method == RequestManager::GET || $method == RequestManager::POST || $method == RequestManager::PUT) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "user/$idUser/device/type/$type";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idUser  User identification value.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Method is invalid]
     */
    private function userSmartphones($method, $idUser)
    {
        if ($method == RequestManager::POST || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "user/$idUser/smartphones";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["emails" => "a@a.com,b@b.com,c@c.com"]
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [No @param $params are set]
     */
    private function users($method, $params = [])
    {
        if ($method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if ($method == RequestManager::POST && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "users";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idChannel  Channel identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params POST & PUT content: ["name" => "channel_name"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function channel($method, $idChannel, $params = [])
    {
        if (($method == RequestManager::POST || $method == RequestManager::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != RequestManager::POST && !isset($idChannel)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "channel";
        $request = $this->getRequestManager();
        try {
            if ($method != RequestManager::POST) {
                $url .= "/$idChannel";
            }
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     */
    private function channels($method, $params = [])
    {
        if ($method == RequestManager::POST || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "channels";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function channelByName($method, $params = [])
    {
        if ($method != RequestManager::GET) {
            throw new Exception("Invalid call method", 1);
        }

        if (empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "channel_name?" . http_build_query($params);
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idTheme  Theme identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params POST & PUT content: ["name" => "theme_name", "range" => "unicast"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function theme($method, $idTheme, $params = [])
    {
        if (($method == RequestManager::POST || $method == RequestManager::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != RequestManager::POST && !isset($idTheme)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "theme";
        $request = $this->getRequestManager();
        try {
            if ($method != RequestManager::POST) {
                $url .= "/$idTheme";
            }
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     */
    private function themes($method, $params = [])
    {
        if ($method == RequestManager::POST || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "themes";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  string  $range The range value that a theme can have.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function themesByRange($method, $range, $params = [])
    {
        if ($method == RequestManager::POST || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($range)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "themes/range/$range";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function themeByName($method, $params = [])
    {
        if ($method != RequestManager::GET) {
            throw new Exception("Invalid call method", 1);
        }

        if (empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        $url = "theme_name?" . http_build_query($params);
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idSubject  Subject identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params POST & PUT content: ["theme_name" => "name_theme", "description" => "this is a description example"]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function subject($method, $idSubject, $params = [])
    {
        if (($method == RequestManager::POST || $method == RequestManager::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if ($method != RequestManager::POST && !isset($idSubject)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "subject";
        $request = $this->getRequestManager();
        try {
            if ($method != RequestManager::POST) {
                $url .= "/$idSubject";
            }
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     */
    private function subjects($method, $params = [])
    {
        if ($method == RequestManager::POST || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        $url = "subjects";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idChannel  Channel identification value.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function subscription($method, $idUser, $idChannel)
    {
        if ($method == RequestManager::PUT) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($idUser) || !isset($idChannel)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "user/$idUser/subscribe/$idChannel";
        $request = $this->getRequestManager();
        try {
            if ($method != RequestManager::POST) {
                $url = "user/$idUser/subscribed/$idChannel";
            }
            return $request->sendRequest($method, $url);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  integer  $idUser  User identification value.
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function subscriptions($method, $idUser)
    {
        if ($method == RequestManager::POST || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
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
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idUser  User identification value.
     * @param  integer  $idTheme  Theme identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["option" => 2]
     *
     * @throws Exception  If [No @param $params are set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function preference($method, $idUser, $idTheme, $params = [])
    {
        if (($method == RequestManager::POST || $method == RequestManager::PUT) && empty($params)) {
            throw new Exception("Trying to add data without params", 2);
        }

        if (!isset($idUser) || !isset($idTheme)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "user/$idUser/preference/$idTheme";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  integer  $idUser  User identification value.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [There aren't required ids set]
     */
    private function preferences($method, $idUser, $params = [])
    {
        if ($method == RequestManager::POST || $method == RequestManager::DELETE) {
            throw new Exception("Invalid call method", 1);
        }

        if ($method == RequestManager::PUT && empty($params)) {
            throw new Exception("Trying to update data without params", 2);
        }

        if (!isset($idUser)) {
            throw new Exception("Url can't be created, expecting referrer id", 3);
        }

        $url = "user/$idUser/preferences";
        $request = $this->getRequestManager();
        try {
            return $request->sendRequest($method, $url, $params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Prepares the API call given the different possibilities (depending on the $method).
     * @param  string  $method  HTTP method of the request.
     * @param  array  $params  Array with the required params as keys (used with PUT && POST method).
     * @return array  Response key => value array
     *
     * @example $params  POST  content: ["theme" => "theme_name", "message" => "Message notification"]
     *
     * @throws Exception  If [Invalid @param $method set]
     * @throws Exception  If [No @param $params are set]
     */
    private function send($method, $params)
    {
        if ($method == RequestManager::GET || $method == RequestManager::PUT || $method == RequestManager::DELETE) {
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
