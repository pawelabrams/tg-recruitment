
# TransferGo test task

## Background

The company has multiple services including one that provides a user identity and several that require sending notifications.
The goal is to create a new service abstracting the notification part.

## Task

Create a service that accepts the necessary information and sends a notification to customers.

The new service should be capable of the following:

1.  Send notifications via different channels.
    It should provide an abstraction between at least two different messaging service providers.
    It can use different messaging services/technologies for communication (e.g. SMS, email, push notification, Facebook Messenger, etc).
    
    Examples of some messaging providers:
    - Emails: AWS SES (SendEmail - Amazon Simple Email Service)
    - SMS messages: Twilio (Twilio SMS API Overview)
    - Push notifications: Pushy (Pushy - Docs - API - Send Notifications)

2.  If one of the services goes down, your service can quickly failover to a different provider without affecting your customers:
    - It is possible to define several providers for the same type of notification channel. e.g. two providers for SMS.
    - A notification should be delayed and later resent if all providers fail.

3.  The service is Configuration-driven: It is possible to enable/disable different communication channels with configuration.
    It is possible to send the same notification via several different channels.
4.  _(Bonus point)_ Throttling: some messages are expected to trigger a user response. In such a case the service should allow a limited amount of notifications sent to users within an hour. e.g. send up to 300 an hour.
5.  _(Bonus point)_ Usage tracking: we can track what messages were sent, when, and to whom. Recognition is done by a user identifier parameter. The identifier is provided as a parameter of the service.

## Solution chosen

The solution I would go with in my normal workflow would be to use Symfony Notifier
and set up the whole structure using nothing but configuration. The documentation of the Notifier component can be found
[here](https://symfony.com/doc/current/notifier.html).

Instead, I decided to crudely reimplement Notifier component, so that my programming knowledge can be assessed a bit
more easily.

## Running the solution

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Documentation of the Symfony Docker template:

1. [Build options](docs/build.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using a Makefile](docs/makefile.md)
8. [Troubleshooting](docs/troubleshooting.md)

## Credits

Symfony Docker template created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
