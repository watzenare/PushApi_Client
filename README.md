# PushApi_Client

[![Latest Stable Version](https://poser.pugx.org/pushapi/client-php/v/stable.svg)](https://packagist.org/packages/pushapi/client-php)
[![License](https://poser.pugx.org/pushapi/client-php/license.svg)](https://packagist.org/packages/pushapi/client-php)

## Install [![Analytics](https://ga-beacon.appspot.com/UA-57718174-1/pushapi/readme?pixel)](https://github.com/watzenare/PushApi_Client)

You can easily install the PushApi_Client using Composer.

In your composer.json file just add the latest stable version of the Client (see versions on [Packagist](https://packagist.org/packages/pushapi/client-php)):

    {
        "require": {
            "pushapi/client-php": "1.*"
        }
    }


## Requirements

- Have a basic knowledge about what [PushApi](https://github.com/watzenare/PushApi) does and its functionalities
- [PushApi](https://github.com/watzenare/PushApi) running on server
- PHP >= 5.5


## Example Usage

```php
require "vendor/autoload.php";

use \RequestManagers\CurlRequestManager;

$requestManager = new CurlRequestManager("http://my_uri.com/", 8080);
$test = new PushApi_Client(1, "my_app", "my_secret", $requestManager);

try {
	$user = $test->getUser(1);
	echo $user['result']['email'] . "\n";
} catch (Exception $e) {
	echo "Exception - " . $e->getMessage() . "\n";
}
```

## Request Managers

The Request Managers are objects that implement sending functions that lets the Client to send calls and receive responses. Currently
there are two Request Managers but only one can be used for this use because the other one is used for tests:

- Curl Request Manager, it uses the PHP Curl method in order to send an receive the Client necesities.
- Dummy Request Manager, it is used in order to get the Client calls, check if it is working correctly and it simulates a request response with the client information.

Pending:

- [Guzzle](https://github.com/guzzle/guzzle) Request Manager, it will use Guzzle functionalities.


## Support

If you want to give your opinion, you can send me an [email](mailto:eloi@tviso.com), comment the project directly (if you want to contribute with information or resources) or fork the project and make a pull request.

Also I will be grateful if you want to make a donation, this project hasn't got a death date and it wants to be improved constantly:

[![Website Button](http://www.rahmenversand.com/images/paypal_logo_klein.gif "Donate!")](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eloi.ballara%40gmail%2ecom&lc=US&item_name=PushApi%20Developers&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest&amount=5 "Contribute to the project")


##License

The PushApi_Client is released under the MIT public license.


## Acknowledgements

I want to thank the collaboration of those GitHub users that are supporting me during the project.

- [jmartin82](https://github.com/jmartin82)
- [paumoreno](https://github.com/paumoreno)
- [muertet](https://github.com/muertet)
- [marcmascarell](https://github.com/marcmascarell)