<?php

use \RequestManagers\DummyRequestManager;

/**
 * 
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
        // $this->assertEquals(self::$baseUrl, self::$requestManager->getBaseUrl());
        $this->assertEquals(self::$port, self::$requestManager->getPort());
        $this->assertEquals(self::$appId, self::$requestManager->getAppId());
        $this->assertEquals(self::getAuth(), self::$requestManager->getAppAuth());
    }

    public function testUserRequests()
    {
        $id = 3;
        $key = "email";
        $params = array(
            $key => "email@test.com"
        );

        // Create an user
        $user = self::$client->createUser($params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::POST, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "user"), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Get an user
        $user = self::$client->getUser($id);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::GET, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "user/$id"), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));

        // Update an user
        $user = self::$client->updateUser($id, $params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::PUT, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "user/$id"), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Delete an user
        $user = self::$client->deleteUser($id);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::DELETE, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "user/$id"), $user["result"]["path"]);
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
        $this->assertTrue(isset($user["result"]));
    }

    public function testUsersRequests()
    {
        $id = 3;
        $key = "email";
        $params = array(
            $key => "email@test.com,email1@test.com,email2@test.com,email3@test.com"
        );

        // Create users
        $user = self::$client->createUsers($params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::POST, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "users"), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Get users
        $user = self::$client->getUsers();
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::GET, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "users"), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));
    }

    public function testChannelRequests()
    {
        $id = 54;
        $key = "name";
        $params = array(
            $key => "channel_test"
        );

        // Create channel
        $user = self::$client->createChannel($params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::POST, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "channel"), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Get channel
        $user = self::$client->getChannel($id);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::GET, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "channel/$id"), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));

        // Update channel
        $user = self::$client->updateChannel($id, $params);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::PUT, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "channel/$id"), $user["result"]["path"]);
        $this->assertTrue(!empty($user["result"]["params"]));
        $this->assertArrayHasKey($key, $user["result"]["params"]);

        // Delete channel
        $user = self::$client->deleteChannel($id);
        $this->assertTrue(isset($user["result"]));
        $this->assertEquals(self::DELETE, $user["result"]["method"]);
        $this->assertEquals((self::$baseUrl . "channel/$id"), $user["result"]["path"]);
        $this->assertTrue(empty($user["result"]["params"]));
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
        $user = self::$client->createChannel($params);
    }

    public function testThemeRequests()
    {
        
    }

    public function testUserPreferencesRequests()
    {
        
    }

    public function testUserSubscriptionsRequests()
    {
        
    }

    public function testSubjectRequests()
    {
        
    }

    public function testSendRequest()
    {
        
    }
}

// $this->assertArrayHasKey($key, $array);
// $this->assertEquals($expected, $actual);
// $this->assertFalse($condition);
// $this->assertEmpty($array);