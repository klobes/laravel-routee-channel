# Routee notifications channel for Laravel 5.3+

This package makes it easy to send SMS notifications using [routee.net](https://www.routee.net) with Laravel 5.3+.

# Warning!
Only Routee SMS is implemented at the moment, if you want more channels feel free to write them yourself in RouteeApi or point me to an API wrapper that we can switch to.

## Contents

- [Installation](#installation)
    - [Setting up the Routee service](#setting-up-the-routee-service)
- [Usage](#usage)
    - [Available Message methods](#available-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install this package with Composer:

```bash
composer require laravel-notification-channels/routee
```

The service provider gets loaded automatically. Or you can do this manually:
```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\Routee\RouteeServiceProvider::class,
],
```

### Setting up the Routee service

Add your Routee application id, application secret and sender id (from) to your `config/services.php`:

```php
// config/services.php
'routee' => [
    'app_id'  => env('ROUTEE_APP_ID'),
    'secret' => env('ROUTEE_SECRET'),
    'from' => env('ROUTEE_SENDER_ID'),
],
```
## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Routee\RouteeMessage;
use NotificationChannels\Routee\RouteeChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [RouteeChannel::class];
    }

    public function toRoutee($notifiable)
    {
        return (new RouteeMessage)
            ->content("Your message here");
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForRoutee()` method, which returns a phone number
or an array of phone numbers.

```php
public function routeNotificationForRoutee()
{
    return $this->phone;
}
```

### Available methods

`content()`: Set a content of the notification message.

`sendAt()`: Set a time for scheduling the notification message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email leo.stratigakis@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [anheric](https://github.com/anheric)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
