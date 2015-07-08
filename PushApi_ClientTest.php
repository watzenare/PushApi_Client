<?php

use \RequestManagers\DummyRequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Client tester that checks if the requests done by the Client contain the right values. It simulates the calls
 * that the Client can do and cheks the fake response. Also it is checked if the Client throws exceptions when
 * the RequestManager throw.
 *
 * phpunit --bootstrap vendor/autoload.php PushApi_ClientTest.php
 */
class PushApi_ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * Main calls that support the PushApi
     */
    const GET = "GET";
    const PUT = "PUT";
    const POST = "POST";
    const DELETE = "DELETE";

    protected static $appId = 1;
    protected static $appName = "Test";
    protected static $appSecret = "secret_test";
    protected static $baseUrl = "http://test.com/";
    protected static $port = 9090;
    protected static $requestManager;
    protected static $client;

    protected static $id = 54;
    protected static $idTheme = 65;
    protected static $idSubscription = 42;
    protected static $idRange = "unicast";
    protected static $key = "name";
    protected static $params = ["name" => "app_name_test"];

    public static function setUpBeforeClass()
    {
        self::$requestManager = new DummyRequestManager(self::$baseUrl, self::$port);
        self::$client = new PushApi_Client(self::$appId, self::$appName, self::$appSecret, self::$requestManager);
    }

    public static function tearDownAfterClass()
    {
        self::$requestManager = NULL;
        self::$client = NULL;
    }

    private static function getAuth()
    {
        return md5(self::$appName . date('Y-m-d') . self::$appSecret);
    }

    public function testClientConstructor()
    {
        $this->assertEquals(self::$appId, self::$client->getAppId());
        $this->assertEquals(self::$appName, self::$client->getAppName());
        $this->assertEquals(self::$appSecret, self::$client->getAppSecret());
        $this->assertEquals(self::getAuth(), self::$client->getAppAuth());
    }

    public function testRequestManagerConstructor()
    {
        $this->assertEquals(self::$baseUrl, self::$requestManager->getBaseUrl());
        $this->assertEquals(self::$port, self::$requestManager->getPort());
        $this->assertEquals(self::$appId, self::$requestManager->getAppId());
        $this->assertEquals(self::getAuth(), self::$requestManager->getAppAuth());
    }

    public function testGetAppRequest()
    {
        $url = "app/" . self::$id;

        $result = self::$client->getApp(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateAppRequest()
    {
        $url = "app/" . self::$id;

        $result = self::$client->updateApp(self::$id, self::$params);
        $this->assertPutRequest($result, $url, self::$key);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testAppForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->updateApp(self::$id, $params);
    }

    public function testCreateUserRequests()
    {
        $url = "user";

        $result = self::$client->createUser(self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    public function testGetUserRequests()
    {
        $url = "user/" . self::$id;

        $result = self::$client->getUser(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateUserRequests()
    {
        $url = "user/" . self::$id;

        $result = self::$client->updateUser(self::$id, self::$params);
        $this->assertPutRequest($result, $url, self::$key);
    }

    public function testDeleteUserRequests()
    {
        $url = "user/" . self::$id;

        $result = self::$client->deleteUser(self::$id);
        $this->assertDeleteRequest($result, $url);
    }

    public function testGetUserSmartphonesRequests()
    {
        $url = "user/" . self::$id . "/smartphones";

        $result = self::$client->getUserSmartphones(self::$id);
        $this->assertGetRequest($result, $url);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testUserForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->createUser($params);
    }

    public function testCreateUsersRequests()
    {
        $url = "users";

        $result = self::$client->createUsers(self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    public function testGetUsersRequests()
    {
        $url = "users";

        $result = self::$client->getUsers();
        $this->assertGetRequest($result, $url);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testUsersForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->createUsers($params);
    }

    public function testCreateChannelRequests()
    {
        $url = "channel";

        $result = self::$client->createChannel(self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    public function testGetChannelRequests()
    {
        $url = "channel/" . self::$id;

        $result = self::$client->getChannel(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateChannelRequests()
    {
        $url = "channel/" . self::$id;

        $result = self::$client->updateChannel(self::$id, self::$params);
        $this->assertPutRequest($result, $url, self::$key);
    }

    public function testDeleteChannelRequests()
    {
        $url = "channel/" . self::$id;

        $result = self::$client->deleteChannel(self::$id);
        $this->assertDeleteRequest($result, $url);
    }

    public function testByNameChannelRequests()
    {
         $url = "channel_name";

        $result = self::$client->getChannelByName(self::$params);
        $this->assertGetNameRequest($result, $url, self::$params);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testChannelForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $channel = self::$client->createChannel($params);
    }

    public function testChannelsRequest()
    {
        $url = "channels";

        $result = self::$client->getChannels();
        $this->assertGetRequest($result, $url);
    }

    public function testCreateThemeRequests()
    {
        $url = "theme";

        $result = self::$client->createTheme(self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    public function testGetThemeRequests()
    {
        $url = "theme/" . self::$id;

        $result = self::$client->getTheme(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateThemeRequests()
    {
        $url = "theme/" . self::$id;

        $result = self::$client->updateTheme(self::$id, self::$params);
        $this->assertPutRequest($result, $url, self::$key);
    }

    public function testDeleteThemeRequests()
    {
        $url = "theme/" . self::$id;

        $result = self::$client->deleteTheme(self::$id);
        $this->assertDeleteRequest($result, $url);
    }

    public function testByNameThemeRequests()
    {
        $url = "theme_name";

        $result = self::$client->getThemeByName(self::$params);
        $this->assertGetNameRequest($result, $url, self::$params);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testThemeForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $theme = self::$client->createTheme($params);
    }

    public function testThemesRequest()
    {
        $url = "themes";

        // Get themes
        $result = self::$client->getThemes();
        $this->assertGetRequest($result, $url);
    }

    public function testThemesByRange()
    {
        $url = "themes/range/" . self::$idRange;

        $result = self::$client->getThemesByRange(self::$idRange);
        $this->assertGetRequest($result, $url);
    }

    public function testCreateUserPreferenceRequests()
    {
        $url = "user/" . self::$id . "/preference/" . self::$idTheme;

        $result = self::$client->createUserPreference(self::$id, self::$idTheme, self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    public function testGetUserPreferenceRequests()
    {
        $url = "user/" . self::$id . "/preference/" . self::$idTheme;

        $result = self::$client->getUserPreference(self::$id, self::$idTheme);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateUserPreferenceRequests()
    {
        $url = "user/" . self::$id . "/preference/" . self::$idTheme;

        $result = self::$client->updateUserPreference(self::$id, self::$idTheme, self::$params);
        $this->assertPutRequest($result, $url, self::$key);
    }

    public function testDeleteUserPreferenceRequests()
    {
        $url = "user/" . self::$id . "/preference/" . self::$idTheme;

        $result = self::$client->deleteUserPreference(self::$id, self::$idTheme);
        $this->assertDeleteRequest($result, $url);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testUserPreferenceForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->updateUserPreference(self::$id, self::$idTheme, $params);
    }

    public function testUserPreferencesRequest()
    {
        $url = "user/" . self::$id . "/preferences";

        // Get themes
        $result = self::$client->getUserPreferences(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testCreateUserSubscriptionRequests()
    {
        $url = "user/" . self::$id . "/subscribe/" . self::$idSubscription;

        $result = self::$client->createUserSubscription(self::$id, self::$idSubscription);
        $this->assertPostRequest($result, $url);
    }

    public function testGetUserSubscriptionRequests()
    {
        $url = "user/" . self::$id . "/subscribed/" . self::$idSubscription;

        $result = self::$client->getUserSubscription(self::$id, self::$idSubscription);
        $this->assertGetRequest($result, $url);
    }

    public function testDeleteUserSubscriptionRequests()
    {
        $url = "user/" . self::$id . "/subscribed/" . self::$idSubscription;

        $result = self::$client->deleteUserSubscription(self::$id, self::$idSubscription);
        $this->assertDeleteRequest($result, $url);
    }

    public function testUserSubscriptionsRequest()
    {
        $url = "user/" . self::$id . "/subscribed";

        $result = self::$client->getUserSubscriptions(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testCreateSubjectRequests()
    {
        $url = "subject";

        $result = self::$client->createSubject(self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    public function testGetSubjectRequests()
    {
        $url = "subject/" . self::$id;

        $result = self::$client->getSubject(self::$id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateSubjectRequests()
    {
        $url = "subject/" . self::$id;

        $result = self::$client->updateSubject(self::$id, self::$params);
        $this->assertPutRequest($result, $url, self::$key);
    }

    public function testDeleteSubjectRequests()
    {
        $url = "subject/" . self::$id;

        $result = self::$client->deleteSubject(self::$id);
        $this->assertDeleteRequest($result, $url);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testSubjectForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $subject = self::$client->createSubject($params);
    }

    public function testSubjectsRequest()
    {
        $url = "subjects";

        $result = self::$client->getSubjects();
        $this->assertGetRequest($result, $url);
    }

    public function testSendRequest()
    {
        $url = "send";

        // Send notification
        $result = self::$client->sendNotification(self::$params);
        $this->assertPostRequest($result, $url, self::$key);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testSendForceException()
    {
        // Recive an exception
        $params['exception'] = true;
        $send = self::$client->sendNotification($params);
    }

    private function assertGetRequest($result, $url)
    {
        $this->assertTrue(isset($result["result"]));
        $this->assertEquals(self::GET, $result["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $result["result"]["path"]);
        $this->assertTrue(empty($result["result"]["params"]));
    }

    private function assertDeleteRequest($result, $url)
    {
        $this->assertTrue(isset($result["result"]));
        $this->assertEquals(self::DELETE, $result["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $result["result"]["path"]);
        $this->assertTrue(empty($result["result"]["params"]));
    }

    private function assertPutRequest($result, $url, $key)
    {
        $this->assertTrue(isset($result["result"]));
        $this->assertEquals(self::PUT, $result["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $result["result"]["path"]);
        $this->assertTrue(!empty($result["result"]["params"]));
        $this->assertArrayHasKey($key, $result["result"]["params"]);
    }

    private function assertPostRequest($result, $url, $key = null)
    {
        $this->assertTrue(isset($result["result"]));
        $this->assertEquals(self::POST, $result["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $result["result"]["path"]);
        if (isset($key)) {
            $this->assertTrue(!empty($result["result"]["params"]));
            $this->assertArrayHasKey($key, $result["result"]["params"]);
        } else {
            $this->assertTrue(empty($result["result"]["params"]));
        }
    }

    private function assertGetNameRequest($result, $url, $params)
    {
        $this->assertTrue(isset($result["result"]));
        $this->assertEquals(self::GET, $result["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url . "?" . http_build_query($params)), $result["result"]["path"]);
    }
}