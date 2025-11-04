<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private string $environment
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $message = str_contains($exception->getMessage(), 'object not found')
                ? 'Resource not found'
                : $exception->getMessage();

            $response = new JsonResponse(
                ['error' => $message],
                Response::HTTP_NOT_FOUND
            );
            $event->setResponse($response);
            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $message = $this->isDebug()
                ? ($exception->getMessage() ?: 'An error occurred')
                : 'An error occurred';

            $response = new JsonResponse(
                ['error' => $message],
                $exception->getStatusCode()
            );
            $event->setResponse($response);
            return;
        }

        $message = $this->isDebug()
            ? $exception->getMessage()
            : 'Internal server error';

        $response = new JsonResponse(
            ['error' => $message],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
        $event->setResponse($response);
    }

    private function isDebug(): bool
    {
        return $this->environment === 'dev';
    }
}

