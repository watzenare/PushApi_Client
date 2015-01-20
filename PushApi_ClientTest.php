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

    public function testAppRequest()
    {
        $id = 22;
        $key = "name";
        $params = array(
            $key => "app_name_test",
        );
        $url = "app/$id";

        // Get app
        $app = self::$client->getApp($id);
        $this->assertTrue(isset($app["result"]));
        $this->assertEquals(self::GET, $app["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $app["result"]["path"]);
        $this->assertTrue(empty($app["result"]["params"]));

        // Update app
        $app = self::$client->updateApp($id, $params);
        $this->assertTrue(isset($app["result"]));
        $this->assertEquals(self::PUT, $app["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $app["result"]["path"]);
        $this->assertTrue(!empty($app["result"]["params"]));
        $this->assertArrayHasKey($key, $app["result"]["params"]);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testAppForceException()
    {
        $id = 23;

        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->updateApp($id, $params);
    }

    public function testUserRequests()
    {
        $id = 3;
        $key = "email";
        $params = array(
            $key => "email@test.com"
        );
        $url = "user/$id";
        $urlPost = "user";

        // Create an user
        $user = self::$client->createUser($params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::POST, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $urlPost), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Get an user
        $user = self::$client->getUser($id);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::GET, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));

        // Update an user
        $user = self::$client->updateUser($id, $params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::PUT, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Delete an user
        $user = self::$client->deleteUser($id);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::DELETE, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));
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

    public function testUsersRequests()
    {
        $id = 3;
        $key = "email";
        $params = array(
            $key => "email@test.com,email1@test.com,email2@test.com,email3@test.com"
        );
        $url = "users";

        // Create users
        $user = self::$client->createUsers($params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::POST, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Get users
        $user = self::$client->getUsers();
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::GET, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));
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

    public function testChannelRequests()
    {
        $id = 54;
        $key = "name";
        $params = array(
            $key => "channel_test"
        );
        $url = "channel/$id";
        $urlPost = "channel";
        $urlByName = "channel_name";

        // Create channel
        $channel = self::$client->createChannel($params);
        $this->assertTrue(isset($channel["result"]));
        $this->assertEquals(self::POST, $channel["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $urlPost), $channel["result"]["path"]);
        $this->assertTrue(!empty($channel["result"]["params"]));
        $this->assertArrayHasKey($key, $channel["result"]["params"]);

        // Get channel
        $channel = self::$client->getChannel($id);
        $this->assertTrue(isset($channel["result"]));
        $this->assertEquals(self::GET, $channel["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $channel["result"]["path"]);
        $this->assertTrue(empty($channel["result"]["params"]));

        // Update channel
        $channel = self::$client->updateChannel($id, $params);
        $this->assertTrue(isset($channel["result"]));
        $this->assertEquals(self::PUT, $channel["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $channel["result"]["path"]);
        $this->assertTrue(!empty($channel["result"]["params"]));
        $this->assertArrayHasKey($key, $channel["result"]["params"]);

        // Delete channel
        $channel = self::$client->deleteChannel($id);
        $this->assertTrue(isset($channel["result"]));
        $this->assertEquals(self::DELETE, $channel["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $channel["result"]["path"]);
        $this->assertTrue(empty($channel["result"]["params"]));

        // Get channel by name
        $channel = self::$client->getChannelByName($params);
        $this->assertTrue(isset($channel["result"]));
        $this->assertEquals(self::GET, $channel["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $urlByName), $channel["result"]["path"]);
        $this->assertTrue(!empty($channel["result"]["params"]));
        $this->assertArrayHasKey($key, $channel["result"]["params"]);
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

        // Get users
        $channels = self::$client->getChannels();
        $this->assertTrue(isset($channels["result"]));
        $this->assertEquals(self::GET, $channels["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $channels["result"]["path"]);
        $this->assertTrue(empty($channels["result"]["params"]));
    }

    public function testThemeRequests()
    {
        $id = 32;
        $key1 = "name";
        $key2 = "range";
        $params = array(
            $key1 => "theme_test",
            $key2 => "unicast",
        );
        $url = "theme/$id";
        $urlPost = "theme";
        $urlByName = "theme_name";

        // Create theme
        $theme = self::$client->createTheme($params);
        $this->assertTrue(isset($theme["result"]));
        $this->assertEquals(self::POST, $theme["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $urlPost), $theme["result"]["path"]);
        $this->assertTrue(!empty($theme["result"]["params"]));
        $this->assertArrayHasKey($key1, $theme["result"]["params"]);
        $this->assertArrayHasKey($key2, $theme["result"]["params"]);

        // Get theme
        $theme = self::$client->getTheme($id);
        $this->assertTrue(isset($theme["result"]));
        $this->assertEquals(self::GET, $theme["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $theme["result"]["path"]);
        $this->assertTrue(empty($theme["result"]["params"]));

        // Update theme
        $theme = self::$client->updateTheme($id, $params);
        $this->assertTrue(isset($theme["result"]));
        $this->assertEquals(self::PUT, $theme["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $theme["result"]["path"]);
        $this->assertTrue(!empty($theme["result"]["params"]));
        $this->assertArrayHasKey($key1, $theme["result"]["params"]);
        $this->assertArrayHasKey($key2, $theme["result"]["params"]);

        // Delete theme
        $theme = self::$client->deleteTheme($id);
        $this->assertTrue(isset($theme["result"]));
        $this->assertEquals(self::DELETE, $theme["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $theme["result"]["path"]);
        $this->assertTrue(empty($theme["result"]["params"]));

        // Get theme by name
        $theme = self::$client->getThemeByName($params);
        $this->assertTrue(isset($theme["result"]));
        $this->assertEquals(self::GET, $theme["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $urlByName), $theme["result"]["path"]);
        $this->assertTrue(!empty($theme["result"]["params"]));
        $this->assertArrayHasKey($key1, $theme["result"]["params"]);
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
        $themes = self::$client->getThemes();
        $this->assertTrue(isset($themes["result"]));
        $this->assertEquals(self::GET, $themes["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $themes["result"]["path"]);
        $this->assertTrue(empty($themes["result"]["params"]));
    }

    public function testThemesByRange()
    {
        $idRange = "unicast";
        $url = "themes/range/$idRange";

        // Get themes by range
        $themes = self::$client->getThemesByRange($idRange);
        $this->assertTrue(isset($themes["result"]));
        $this->assertEquals(self::GET, $themes["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $themes["result"]["path"]);
        $this->assertTrue(empty($themes["result"]["params"]));
    }

    public function testUserPreferenceRequests()
    {
        $idUser = 63;
        $idTheme = 12;
        $key = "option";
        $params = array(
            $key => 3,
        );
        $url = "user/$idUser/preference/$idTheme";

        // Create preference
        $preference = self::$client->createUserPreference($idUser, $idTheme, $params);
        $this->assertTrue(isset($preference["result"]));
        $this->assertEquals(self::POST, $preference["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $preference["result"]["path"]);
        $this->assertTrue(!empty($preference["result"]["params"]));
        $this->assertArrayHasKey($key, $preference["result"]["params"]);

        // Get preference
        $preference = self::$client->getUserPreference($idUser, $idTheme);
        $this->assertTrue(isset($preference["result"]));
        $this->assertEquals(self::GET, $preference["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $preference["result"]["path"]);
        $this->assertTrue(empty($preference["result"]["params"]));

        // Update preference
        $preference = self::$client->updateUserPreference($idUser, $idTheme, $params);
        $this->assertTrue(isset($preference["result"]));
        $this->assertEquals(self::PUT, $preference["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $preference["result"]["path"]);
        $this->assertTrue(!empty($preference["result"]["params"]));
        $this->assertArrayHasKey($key, $preference["result"]["params"]);

        // Delete preference
        $preference = self::$client->deleteUserPreference($idUser, $idTheme);
        $this->assertTrue(isset($preference["result"]));
        $this->assertEquals(self::DELETE, $preference["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $preference["result"]["path"]);
        $this->assertTrue(empty($preference["result"]["params"]));
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    I'm a Dummmy Exception
     * @expectedExceptionCode       0
     */
    public function testUserPreferenceForceException()
    {
        $idUser = 63;
        $idTheme = 12;

        // Recive an exception
        $params['exception'] = true;
        $user = self::$client->updateUserPreference($idUser, $idTheme, $params);
    }

    public function testUserPreferencesRequest()
    {
        $idUser = 12;
        $url = "user/$idUser/preferences";

        // Get themes
        $preferences = self::$client->getUserPreferences($idUser);
        $this->assertTrue(isset($preferences["result"]));
        $this->assertEquals(self::GET, $preferences["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $preferences["result"]["path"]);
        $this->assertTrue(empty($preferences["result"]["params"]));
    }

    public function testUserSubscriptionRequests()
    {
        $idUser = 876;
        $idSubscription = 32;
        $url = "user/$idUser/subscribed/$idSubscription";
        $urlPost = "user/$idUser/subscribe/$idSubscription";

        // Create subscription
        $subscription = self::$client->createUserSubscription($idUser, $idSubscription);
        $this->assertTrue(isset($subscription["result"]));
        $this->assertEquals(self::POST, $subscription["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $urlPost), $subscription["result"]["path"]);
        $this->assertTrue(empty($subscription["result"]["params"]));

        // Get subscription
        $subscription = self::$client->getUserSubscription($idUser, $idSubscription);
        $this->assertTrue(isset($subscription["result"]));
        $this->assertEquals(self::GET, $subscription["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subscription["result"]["path"]);
        $this->assertTrue(empty($subscription["result"]["params"]));

        // Delete subscription
        $subscription = self::$client->deleteUserSubscription($idUser, $idSubscription);
        $this->assertTrue(isset($subscription["result"]));
        $this->assertEquals(self::DELETE, $subscription["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subscription["result"]["path"]);
        $this->assertTrue(empty($subscription["result"]["params"]));
    }

    public function testUserSubscriptionsRequest()
    {
        $idUser = 64;
        $url = "user/$idUser/subscribed";

        $subsctiptions = self::$client->getUserSubscriptions($idUser);
        $this->assertTrue(isset($subsctiptions["result"]));
        $this->assertEquals(self::GET, $subsctiptions["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subsctiptions["result"]["path"]);
        $this->assertTrue(empty($subsctiptions["result"]["params"]));
    }

    public function testSubjectRequests()
    {
        $id = 54;
        $key1 = "name";
        $key2 = "description";
        $params = array(
            $key1 => "name_test",
            $key2 => "description_test"
        );
        $url = "subject/$id";
        $postUrl = "subject";

        // Create subject
        $subject = self::$client->createSubject($params);
        $this->assertTrue(isset($subject["result"]));
        $this->assertEquals(self::POST, $subject["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $postUrl), $subject["result"]["path"]);
        $this->assertTrue(!empty($subject["result"]["params"]));
        $this->assertArrayHasKey($key1, $subject["result"]["params"]);
        $this->assertArrayHasKey($key2, $subject["result"]["params"]);

        // Get subject
        $subject = self::$client->getSubject($id);
        $this->assertTrue(isset($subject["result"]));
        $this->assertEquals(self::GET, $subject["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subject["result"]["path"]);
        $this->assertTrue(empty($subject["result"]["params"]));

        // Update subject
        $subject = self::$client->updateSubject($id, $params);
        $this->assertTrue(isset($subject["result"]));
        $this->assertEquals(self::PUT, $subject["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subject["result"]["path"]);
        $this->assertTrue(!empty($subject["result"]["params"]));
        $this->assertArrayHasKey($key1, $subject["result"]["params"]);
        $this->assertArrayHasKey($key2, $subject["result"]["params"]);

        // Delete subject
        $subject = self::$client->deleteSubject($id);
        $this->assertTrue(isset($subject["result"]));
        $this->assertEquals(self::DELETE, $subject["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subject["result"]["path"]);
        $this->assertTrue(empty($subject["result"]["params"]));
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
        $idUser = 64;
        $url = "subjects";

        $subjects = self::$client->getSubjects();
        $this->assertTrue(isset($subjects["result"]));
        $this->assertEquals(self::GET, $subjects["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $subjects["result"]["path"]);
        $this->assertTrue(empty($subjects["result"]["params"]));
    }

    public function testSendRequest()
    {
        $key1 = "theme";
        $key2 = "message";
        $params = array(
            $key1 => "newsletter_test",
            $key2 => "Test_new_message",
        );
        $url = "send";

        // Send notification
        $send = self::$client->sendNotification($params);
        $this->assertTrue(isset($send["result"]));
        $this->assertEquals(self::POST, $send["result"]["method"]);
        $this->assertEquals((self::$baseUrl . $url), $send["result"]["path"]);
        $this->assertTrue(!empty($send["result"]["params"]));
        $this->assertArrayHasKey($key1, $send["result"]["params"]);
        $this->assertArrayHasKey($key2, $send["result"]["params"]);
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
}