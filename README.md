# PushApi_Client

[![Analytics](https://ga-beacon.appspot.com/UA-57718174-1/pushapi/readme?pixel)](https://github.com/watzenare/PushApi_Client)

## Introduction

A PHP standalone client that facilitates to developers the use of all the functionalities of the PushApi.


## Install

You can install the PushApi_Client with Composer or Manually.

### Composer

In your composer.json file just add the latest stable version of the client (see versions on [Packagist](https://packagist.org/packages/pushapi/client-php)):

    {
        "require": {
            "pushapi/client-php": "1.*"
        }
    }

### Manually

Clone the project and add it in your project folder:

    $ cd path/to/your/project/pushapi_client
    
Require the Client in your PHP file:

```php
require "PushApi_Client/PushApi_Client.php";
```


## Requirements

- PHP >= 5.5
- Have a basic knowledge about what the PushApi does and its functionalities


## Example Usage

```php
require "vendor/autoload.php";

$test = new PushApi_Client(1, "my_app", "my_secret", "http://my_uri.com/", 8080);
try {
	$user = $test->getUser(1);
	echo $user['result']['email'] . "\n";
} catch (PushApiException $e) {
	echo "PushApiException - " . $e->getMessage() . "\n";
} catch (Exception $e) {
	echo "Exception - " . $e->getMessage() . "\n";
}
```

## Support

If you want to give your opinion, you can send me an [email](mailto:eloi@tviso.com), comment the project directly (if you want to contribute with information or resources) or fork the project and make a pull request.

Also I will be grateful if you want to make a donation, this project hasn't got a death date and it wants to be improved constantly:

[![Website Button](http://www.rahmenversand.com/images/paypal_logo_klein.gif "Donate!")](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eloi.ballara%40gmail%2ecom&lc=US&item_name=PushApi%20Developers&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest&amount=5 "Contribute to the project")


##License

The PushApi_Client is released under the MIT public license.

Thank you!