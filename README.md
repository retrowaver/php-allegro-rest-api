# php-allegro-rest-api-v2
Second version of a simple interface for Allegro REST API resources. Built on top of https://github.com/Wiatrogon/php-allegro-rest-api, which was rewritten for the most part.

**Main differences are:**
* uses HTTPlug / PSR-7 for HTTP communication
* contains previously missing features (image upload)
* both `authorization code` and `client credentials` flows are supported (`device flow` could be also easily implemented, as authorization is now separated from main API component)
* is flexible (custom headers, custom middleware)

## 0. Installation
PHP Allegro REST API uses HTTPlug, and...

> If a library depends on HTTPlug, it requires the virtual package `php-http/client-implementation`. A virtual package is used to declare that the library needs an implementation of the HTTPlug interfaces, but does not care which implementation specifically.

I recommend guzzle6 adapter, so run the following command before installing the PHP Allegro REST API library itself:

```
composer require php-http/guzzle6-adapter php-http/message guzzlehttp/psr7
```

Now you're ready to run:

```
composer require retrowaver/allegro-rest-api-v2
```

Note that you don't need to inject HTTP client and message factory into API client - HTTPlug uses `discovery` to find implementations automatically.

If you want to pass client / message factory explicitly to the API client, you can still do that (see `Api.php` constructor method).

## 1. Registering your app
In order to use Allegro REST API, you have to register your application (if you haven't done that already - see https://developer.allegro.pl/auth/#app).

## 2. Authorization
First, you need to get an authorization token. There are two different *token managers* provided for different types of authorization flows. Use one of them to get a token.

### 2.1. Authorization code flow
Learn how it works here: https://developer.allegro.pl/auth/#user

To use it with PHP Allegro REST API, do the following:
```php
use Retrowaver\Allegro\REST\Token\TokenManager\AuthorizationCodeTokenManager;

$tokenManager = new AuthorizationCodeTokenManager;
$tokenManager->getUri(); // show this URI to your user
```

```php
use Retrowaver\Allegro\REST\Token\Credentials;
$token = $tokenManager->getAuthorizationCodeToken(
    new Credentials([
        'clientId' => '...',
        'clientSecret' => '...',
        'redirectUri' => '...'
    ]),
    $code // code from $_GET
);
```

### 2.2. Client credentials flow
Client credentials flow doesn't require a user's permission. It's mostly used for accessing public data (searching offers, getting categories tree). Read more here: https://developer.allegro.pl/auth/#clientCredentialsFlow
```php
use Retrowaver\Allegro\REST\Token\TokenManager\ClientCredentialsTokenManager;
use Retrowaver\Allegro\REST\Token\Credentials;

$tokenManager = new ClientCredentialsTokenManager;
$token = $tokenManager->getClientCredentialsToken(
    new Credentials([
        'clientId' => '...',
        'clientSecret' => '...',
        'redirectUri' => '...'
    ])
);
```

### 2.3. Device flow
Device flow isn't supported right now. But you could always write your token manager and use the received token with API as usual. Read more here: https://developer.allegro.pl/auth/#DeviceFlow

## 3. Refreshing tokens
Authorization code tokens can be refreshed (see https://developer.allegro.pl/auth/#refresh-token). 

```php
use Retrowaver\Allegro\REST\Token\TokenManager\AuthorizationCodeTokenManager;
use Retrowaver\Allegro\REST\Token\Credentials;

$tokenManager = new AuthorizationCodeTokenManager;
$tokenManager->refreshToken(
    new Credentials([
        'clientId' => '...',
        'clientSecret' => '...',
        'redirectUri' => '...'
    ]),
    $token
);
```

## 4. Basic usage

### 4.1. Initializing
```php
use Retrowaver\Allegro\REST\Api;

$api = new Api;
$api->setToken($token); // token received from token manager
```

### 4.2. GET method
```php
// GET https://api.allegro.pl/offers/listing?phrase=dell
$response = $api->offers->listing->get(['phrase' => 'dell']);
```

### 4.3. POST method
```php
// POST https://api.allegro.pl/sale/offers
$response = $api->sale->offers->post($data);
```

### 4.4. PUT method
```php
// PUT https://api.allegro.pl/sale/offers/12345678
$response = $api->sale->offers(12345678)->put($data);
```

### 4.5. DELETE method
```php
// DELETE https://api.allegro.pl/sale/offers/12345678
$response = $api->sale->offers(12345678)->delete();
```

### 4.6. Command
Some resources in API are only accesible using `command pattern` (read more here https://developer.allegro.pl/command/).

```php
// PUT https://api.allegro.pl/offers/12345678/change-price-commands/00b8837d-b47e-4f28-9930-29a5cdb10e15
$response = $api->offers(12345678)->{'change-price-commands'}()->put($data);
```
In the example above, UUID is generated automatically. If you want, you can generate it yourself, and pass it as an argument:

```php
$response = $api->offers(12345678)->{'change-price-commands'}('some-randomly-generated-uuid')->put($data);
```

## 5. Headers
You can alter headers being sent with your requests. You can do it on request basis, or once for all subsequent requests.

### 5.1. Why alter headers?
You may want to alter headers for several different reasons, mainly:
* if you want messages from API returned in Polish (add `Accept-Language: pl-PL`)
* if you want to access beta methods (read more here https://developer.allegro.pl/about/#beta)

### 5.2. Send a single request with custom headers
```php
$headers = [
    'Content-Type' => 'application/vnd.allegro.beta.v1+json',
    'Accept' => 'application/vnd.allegro.beta.v1+json'
];

$response = $api->categories->get(null, $headers);
```

### 5.3. Set custom headers for subsequent requests
#### 5.3.1. Replacing headers
Note that there are some custom headers set by default (`Content-Type: application/vnd.allegro.public.v1+json` and `Accept: application/vnd.allegro.public.v1+json`). If you want to replace them, use `setCustomHeaders()`:

```php
$headers = [
    'Content-Type' => 'application/vnd.allegro.beta.v1+json',
    'Accept' => 'application/vnd.allegro.beta.v1+json'
];

$api->setCustomHeaders($headers);
```

#### 5.3.2. Adding headers
If you want to add one or more custom headers, use `addCustomHeaders()`:

```php
$headers = [
    'Accept-Language' => 'pl-PL'
];

$api->addCustomHeaders($headers);
```

Note that existing custom headers won't be replaced when using this method.

## 6. Middleware
PHP Allegro REST API has a middleware feature, that allows you to alter requests and responses any way you like.

At this point, there's only one 'real' built-in middleware - `ImageUploadMiddleware` that alters request's URI in case of image upload request (from standard `api.allegro.pl` to image upload-specific `upload.allegro.pl`).

### 6.1. Create your own middleware
You can create your own middleware by creating a class implementing `Allegro\REST\Middleware\MiddlewareInterface` and passing it into API constructor:

```php
$middleware = [
    new CustomMiddleware,
    new AnotherCustomMiddleware
];

$api = new Api(null, null, $middleware);
```

## 7. Other stuff
### 7.1. Sandbox
If you want to use API with sandbox environment, use `Sandbox` instead of `Api`, `SandboxAuthorizationCodeTokenManager` instead of `AuthorizationCodeTokenManager` and `SandboxClientCredentialsTokenManager` instead of `ClientCredentialsTokenManager`.

## 7.2. Tests
### 7.2.1. Unit tests
Run unit tests with `vendor/bin/phpunit tests --color`.
### 7.2.2. Sandbox tests
`tests-sandbox` contains tests sending real HTTP requests to sandbox environment. If you want to run those, rename `tests-sandbox/config.php.dist` to `tests-sandbox/config.php`, insert your sandbox credentials, and then run `vendor/bin/phpunit tests-sandbox --color`.