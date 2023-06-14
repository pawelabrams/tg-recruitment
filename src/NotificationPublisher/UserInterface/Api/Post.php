<?php

declare(strict_types=1);

namespace App\NotificationPublisher\UserInterface\Api;

use App\NotificationPublisher\Application\Command\SendNotification;
use App\NotificationPublisher\Application\CommandHandler\SendNotificationCommandHandler;
use App\SharedKernel\Domain\UserId;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Post
{
    /**
     * NOTE: This obviously be at least a call to Command Bus in a real-life scenario, probably wrapped in a Use Case
     *       for every more complicated piece of logic needing its own integration test.
     * @see /README.md #Caveats for a list of problems with the solution that I didn't have time to straighten out.
     */
    public function __construct(
        private SendNotificationCommandHandler $handler
    )
    {
    }

    #[OA\Post(tags: ['Notifications'])]
    #[Route('/api/notification', name: 'api.notification.post', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(required: ['user_id', 'title'], properties: [
        new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'content', type: 'string')
    ], type: 'object'))]
    #[OA\Response(response: 204, description: 'Notification sent and delivered.')]
    // NOTE: I didn't use all of those, but for example in case of async processing,
    //       I would create a request identifier and use HTTP 202 Accepted in place of 204 No Content.
    #[OA\Response(response: 202, description: '(proposed) Notification was accepted and will be sent asynchronously.')]
    #[OA\Response(response: 400, description: '(proposed) You entered malformed data.')]
    #[OA\Response(response: 422, description: '(proposed) Problem with data you entered or ones already in the database.')]
    public function __invoke(Request $request)
    {
        [
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
        ] = $request->toArray();

        // NOTE: This should be, of course, a call to a Command Bus which would pass the handling to the CommandHandler.
        ($this->handler)(
            new SendNotification(
                new UserId($userId),
                title: $title,
                content: $content
            )
        );

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
