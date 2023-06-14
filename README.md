
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

### Caveats

I didn't implement logic to find users in a repository, there is only a mock one to enable solution presentation.

I didn't install AWS SDK, instead I used Symfony Mailer which can be configured to send e-mails through SES easily:

https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport

## Running the solution

1. Add the following vars in the .env.local file to enable mocking. If you got this solution by zip-ball, 
   you probably have author's personal data there, so you can contact me. If you commit it anywhere, I will personally
   find you and give you a credentials security course.
    ```bash
    MOCK_USER_PHONE='+15555555555'
    MOCK_USER_EMAIL='user@user.example'
    MOCK_USER_PUSHY_TOKEN='pushy_token_of_device'
    ```
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
