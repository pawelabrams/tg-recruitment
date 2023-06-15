
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

### What is lacking

First of all, I didn't implement points 2b, 4 or 5; here is how I would implement them in the current structure:

2b. Resending messages when transports aren't ready: I would add `DeadletterTransport` which would `support` all channels.
    Deadletters would be added to a queue with exponential back-off. After a configurable number of retries, their failure
    would be logged and the number of deadletters + ratio dead/normal deliveries would be counted in a monitor (eg. in Datadog).
4.  Throttling can be done in analogous way, with a decorator `Transport` wrapping the normal transports.
    It would fail in a normal recoverable way if the threshold was topped.
5.  Usage tracking would be trivial in Symfony Notifier case, as the component dispatched events when delivering notifications.
    I would follow the same way, injecting EventDispatcher into the NotificationPublisher.
    Infrastructure-layer class called `UserRecipient` could be the place to add GDPR-compliant tracking ID.
    Alternatively, we could just insert the tracking code into SendNotificationCommandHandler instead of relying on events.

There are also no integration tests. I've planned to create a test to check if issuing a SendNotification command
would lead to transports being sent a Message, but without CommandBus it's not much more than the existing unit test
of the CommandHandler.

### Caveats

I didn't implement logic to find users in a repository, there is only a mock one to enable solution presentation.

I didn't implement the CommandBus nor UseCases, so the CommandHandler is called directly in the controller.

I didn't install AWS SDK, instead I used Symfony Mailer which can be configured to send e-mails through SES easily:

https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport

I'd recommend splitting exceptions in transports into recoverable (timeouts, payment required) and irrecoverable
(bad API keys, illegal characters in from field), so that the endpoint doesn't return 2xx happily when none of the
transports are working.

## Running the solution

1. Add the following vars in the .env.local file to enable mocking. If you got this solution by zip-ball, 
   you probably have author's personal data there, so you can contact me. If you commit it anywhere, I will personally
   find you and give you a credentials security course.
    ```bash
    MOCK_USER_PHONE='+15555555555'
    MOCK_USER_EMAIL='user@user.example'
    MOCK_USER_PUSHY_TOKEN='pushy_token_of_device'
    ```
   You will probably need to change other variables defined in .env file, like `MAILER_DSN`, `TWILIO_SID`,
   `TWILIO_AUTH_TOKEN` or `PUSHY_SECRET_TOKEN`.
2. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
3. Run `docker compose build --pull --no-cache` to build fresh images
4. Run `docker compose up` (the logs will be displayed in the current shell)
5. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
6. Navigate to `https://localhost/api/docs` to see the NelmioApiDocsBundle-generated Swagger documentation.
7. Click on `POST /api/notification` endpoint to check how the endpoint contract looks like.
8. Use any UUID (UserRepository is mocked, so the user selected will be the same) and a non-empty title to send a notification.
9. Run `docker compose down --remove-orphans` to stop the Docker containers.

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
