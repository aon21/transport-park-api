<?php

namespace App\Tests\Unit;

use App\EventListener\ExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $events = ExceptionListener::getSubscribedEvents();

        $this->assertIsArray($events);
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $events);
        $this->assertEquals('onKernelException', $events[KernelEvents::EXCEPTION]);
    }

    public function testNotFoundHttpExceptionWithObjectNotFoundMessage(): void
    {
        $listener = new ExceptionListener('prod');
        $exception = new NotFoundHttpException('Some object not found details');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Resource not found', $content['error']);
    }

    public function testNotFoundHttpExceptionWithCustomMessage(): void
    {
        $listener = new ExceptionListener('prod');
        $exception = new NotFoundHttpException('Driver not found');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Driver not found', $content['error']);
    }

    public function testHttpExceptionInDevModeWithMessage(): void
    {
        $listener = new ExceptionListener('dev');
        $exception = new BadRequestHttpException('Invalid input data');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Invalid input data', $content['error']);
    }

    public function testHttpExceptionInDevModeWithEmptyMessage(): void
    {
        $listener = new ExceptionListener('dev');
        $exception = new BadRequestHttpException('');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('An error occurred', $content['error']);
    }

    public function testHttpExceptionInProductionMode(): void
    {
        $listener = new ExceptionListener('prod');
        $exception = new BadRequestHttpException('Sensitive error details');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('An error occurred', $content['error']);
    }

    public function testGenericExceptionInDevMode(): void
    {
        $listener = new ExceptionListener('dev');
        $exception = new \RuntimeException('Database connection failed');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Database connection failed', $content['error']);
    }

    public function testGenericExceptionInProductionMode(): void
    {
        $listener = new ExceptionListener('prod');
        $exception = new \RuntimeException('Sensitive system error');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Internal server error', $content['error']);
    }

    public function testTestEnvironmentIsNotDebug(): void
    {
        $listener = new ExceptionListener('test');
        $exception = new \RuntimeException('Test error message');
        $event = $this->createExceptionEvent($exception);

        $listener->onKernelException($event);

        $response = $event->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals('Internal server error', $content['error']);
    }

    private function createExceptionEvent(\Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        return new ExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }
}

