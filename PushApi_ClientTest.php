<?php

use \RequestManagers\DummyRequestManager;

/**
 * @author Eloi Ballarà Madrid <eloi@tviso.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * Client tester that checks if the requests done by the Client contain the right values. It simulates the calls
 * that the Client can do and cheks the fake response. Also it is checked if the Client throws exceptions when
 * the RequestManager throw.
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
        $id = 52;
        $url = "app/$id";

        $result = self::$client->getApp($id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateAppRequest()
    {
        $id = 54;
        $key = "name";
        $params = array(
            $key => "app_name_test",
        );
        $url = "app/$id";

        $result = self::$client->updateApp($id, $params);
        $this->assertPutRequest($result, $url, $key);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testAppForceException()
    {
        $id = 32;

        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->updateApp($id, $params);
    }

    public function testCreateUserRequests()
    {
        $id = 5;
        $key = "email";
        $params = array(
            $key => "email@test.com"
        );
        $url = "user";

        $result = self::$client->createUser($params);
        $this->assertPostRequest($result, $url, $key);
    }

    public function testGetUserRequests()
    {
        $id = 1;
        $url = "user/$id";

        $result = self::$client->getUser($id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateUserRequests()
    {
        $id = 2;
        $key = "email";
        $params = array(
            $key => "email@test.com"
        );
        $url = "user/$id";

        $result = self::$client->updateUser($id, $params);
        $this->assertPutRequest($result, $url, $key);
    }

    public function testDeleteUserRequests()
    {
        $id = 3;
        $url = "user/$id";

        $result = self::$client->deleteUser($id);
        $this->assertDeleteRequest($result, $url);
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
        $key = "email";
        $params = array(
            $key => "email@test.com,email1@test.com,email2@test.com,email3@test.com"
        );
        $url = "users";

        $result = self::$client->createUsers($params);
        $this->assertPostRequest($result, $url, $key);
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
        $id = 21;
        $key = "name";
        $params = array(
            $key => "channel_test"
        );
        $url = "channel";

        $result = self::$client->createChannel($params);
        $this->assertPostRequest($result, $url, $key);
    }

    public function testGetChannelRequests()
    {
        $id = 31;
        $url = "channel/$id";

        $result = self::$client->getChannel($id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateChannelRequests()
    {
        $id = 24;
        $key = "name";
        $params = array(
            $key => "channel_test"
        );
        $url = "channel/$id";

        $result = self::$client->updateChannel($id, $params);
        $this->assertPutRequest($result, $url, $key);
    }

    public function testDeleteChannelRequests()
    {
        $id = 35;
        $url = "channel/$id";

        $result = self::$client->deleteChannel($id);
        $this->assertDeleteRequest($result, $url);
    }

    public function testByNameChannelRequests()
    {
        $id = 46;
        $key = "name";
        $params = array(
            $key => "channel_test"
        );
        $url = "channel_name";

        $result = self::$client->getChannelByName($params);
        $this->assertGetNameRequest($result, $url, $params);
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
        $id = 75;
        $key = "name";
        $params = array(
            $key => "theme_test",
        );
        $url = "theme";

        $result = self::$client->createTheme($params);
        $this->assertPostRequest($result, $url, $key);
    }

    public function testGetThemeRequests()
    {
        $id = 86;
        $url = "theme/$id";

        $result = self::$client->getTheme($id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateThemeRequests()
    {
        $id = 78;
        $key = "name";
        $params = array(
            $key => "theme_test",
        );
        $url = "theme/$id";

        $result = self::$client->updateTheme($id, $params);
        $this->assertPutRequest($result, $url, $key);
    }

    public function testDeleteThemeRequests()
    {
        $id = 67;
        $url = "theme/$id";

        $result = self::$client->deleteTheme($id);
        $this->assertDeleteRequest($result, $url);
    }

    public function testByNameThemeRequests()
    {
        $id = 54;
        $key = "name";
        $params = array(
            $key => "theme_test",
        );
        $url = "theme_name";

        $result = self::$client->getThemeByName($params);
        $this->assertGetNameRequest($result, $url, $params);
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
        $idRange = "unicast";
        $url = "themes/range/$idRange";

        $result = self::$client->getThemesByRange($idRange);
        $this->assertGetRequest($result, $url);
    }

    public function testCreateUserPreferenceRequests()
    {
        $idUser = 28;
        $idTheme = 27;
        $key = "option";
        $params = array(
            $key => 3,
        );
        $url = "user/$idUser/preference/$idTheme";

        $result = self::$client->createUserPreference($idUser, $idTheme, $params);
        $this->assertPostRequest($result, $url, $key);
    }

    public function testGetUserPreferenceRequests()
    {
        $idUser = 52;
        $idTheme = 71;
        $url = "user/$idUser/preference/$idTheme";

        $result = self::$client->getUserPreference($idUser, $idTheme);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateUserPreferenceRequests()
    {
        $idUser = 32;
        $idTheme = 54;
        $key = "option";
        $params = array(
            $key => 3,
        );
        $url = "user/$idUser/preference/$idTheme";

        $result = self::$client->updateUserPreference($idUser, $idTheme, $params);
        $this->assertPutRequest($result, $url, $key);
    }

    public function testDeleteUserPreferenceRequests()
    {
        $idUser = 63;
        $idTheme = 12;
        $url = "user/$idUser/preference/$idTheme";

        $result = self::$client->deleteUserPreference($idUser, $idTheme);
        $this->assertDeleteRequest($result, $url);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testUserPreferenceForceException()
    {
        $idUser = 12;
        $idTheme = 78;

        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->updateUserPreference($idUser, $idTheme, $params);
    }

    public function testUserPreferencesRequest()
    {
        $idUser = 234;
        $url = "user/$idUser/preferences";

        // Get themes
        $result = self::$client->getUserPreferences($idUser);
        $this->assertGetRequest($result, $url);
    }

    public function testCreateUserSubscriptionRequests()
    {
        $idUser = 876;
        $idSubscription = 32;
        $url = "user/$idUser/subscribe/$idSubscription";

        $result = self::$client->createUserSubscription($idUser, $idSubscription);
        $this->assertPostRequest($result, $url);
    }

    public function testGetUserSubscriptionRequests()
    {
        $idUser = 453;
        $idSubscription = 54;
        $url = "user/$idUser/subscribed/$idSubscription";

        $result = self::$client->getUserSubscription($idUser, $idSubscription);
        $this->assertGetRequest($result, $url);
    }

    public function testDeleteUserSubscriptionRequests()
    {
        $idUser = 23;
        $idSubscription = 32;
        $url = "user/$idUser/subscribed/$idSubscription";

        $result = self::$client->deleteUserSubscription($idUser, $idSubscription);
        $this->assertDeleteRequest($result, $url);
    }

    public function testUserSubscriptionsRequest()
    {
        $idUser = 76;
        $url = "user/$idUser/subscribed";

        $result = self::$client->getUserSubscriptions($idUser);
        $this->assertGetRequest($result, $url);
    }

    public function testCreateSubjectRequests()
    {
        $id = 45;
        $key = "name";
        $params = array(
            $key => "name_test",
        );
        $url = "subject";

        $result = self::$client->createSubject($params);
        $this->assertPostRequest($result, $url, $key);
    }

    public function testGetSubjectRequests()
    {
        $id = 34;
        $url = "subject/$id";

        $result = self::$client->getSubject($id);
        $this->assertGetRequest($result, $url);
    }

    public function testUpdateSubjectRequests()
    {
        $id = 67;
        $key = "name";
        $params = array(
            $key => "name_test",
        );
        $url = "subject/$id";

        $result = self::$client->updateSubject($id, $params);
        $this->assertPutRequest($result, $url, $key);
    }

    public function testDeleteSubjectRequests()
    {
        $id = 65;
        $url = "subject/$id";

        $result = self::$client->deleteSubject($id);
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
        $idUser = 41;
        $url = "subjects";

        $result = self::$client->getSubjects();
        $this->assertGetRequest($result, $url);
    }

    public function testSendRequest()
    {
        $key = "theme";
        $params = array(
            $key => "newsletter_test",
        );
        $url = "send";

        // Send notification
        $result = self::$client->sendNotification($params);
        $this->assertPostRequest($result, $url, $key);
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