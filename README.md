# Anper\Mailer

[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coverage]][link-coverage]

A simple mailer, allows you to create and send emails by key. Messages can be stored in different storages. Third-party mailers act as transport.

## Install

``` bash
$ composer require anper/mailer
```

## Usage
```php
use Anper\Mailer\Transport\NullTransport;
use Anper\Mailer\Storage\MemoryStorage
use Anper\Mailer\Mailer;

$storage = new MemoryStorage([
    'hello' => [
        'subject' => 'Hello',
        'body'    => 'Hello World!',
        'from'    => 'from@example.com',
        'to'      => 'user@example.com',
    ],
]);

$mailer = new Mailer(new NullTransport(), $storage);

$mailer->send('hello');

// or you can modify message

$mailer->get('hello')
    ->addTo('foo@example.com')
    ->send();
```

## Supports
* subject
* body
* from
* to
* cc
* bcc
* reply_to
* sender
* return_path
* attachments
* headers
* priority
* content_type
* charset

## Packages
* Storages
    * [anper/twig-storage](https://github.com/perevoshchikov/twig-storage)
    * [anper/php-storage](https://github.com/perevoshchikov/php-storage)
    * [anper/yaml-storage](https://github.com/perevoshchikov/yaml-storage)
* Transports
    * [anper/swiftmailer-transport](https://github.com/perevoshchikov/swiftmailer-transport)

## Context
You can pass context to the storage, for example, variables for the template in twig storage.

```php
$context = [
    'foo' => 'bar'
];

$mailer->send('hello', $context);

// or

$message = $mailer->get('hello', $context);
```

## Defaults
```php
use Anper\Mailer\Subscriber\Defaults;

$defaultMessageParameters = [
    'from' => 'admin@example.com',
    'content_type' => 'text/plain',
];

$defaultContext = [
    'teem' => 'Example Team',
];

$subscriber = new Defaults($defaultMessageParameters, $defaultContext);

$mailer->getDispatcher()
    ->addSubscriber($subscriber);
```

## Test

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/anper/mailer.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/perevoshchikov/mailer/master.svg?style=flat-square
[ico-coverage]: https://img.shields.io/coveralls/github/perevoshchikov/mailer/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/anper/mailer
[link-travis]: https://travis-ci.org/perevoshchikov/mailer
[link-coverage]: https://coveralls.io/github/perevoshchikov/mailer?branch=master
