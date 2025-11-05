<?php

namespace App\Tests\Unit;

use App\EventListener\ExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListenerTest extends TestCase
{
    private function handleException(\Throwable $exception, string $env = 'prod'): array
    {
        $listener = new ExceptionListener($env);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, new Request(), HttpKernelInterface::MAIN_REQUEST, $exception);
        
        $listener->onKernelException($event);
        
        return [
            json_decode($event->getResponse()->getContent(), true),
            $event->getResponse()->getStatusCode()
        ];
    }

    public function testGetSubscribedEvents(): void
    {
        $events = ExceptionListener::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $events);
        $this->assertEquals('onKernelException', $events[KernelEvents::EXCEPTION]);
    }

    public function testNotFoundHttpException(): void
    {
        [$content, $status] = $this->handleException(new NotFoundHttpException('Some object not found'));
        $this->assertEquals(404, $status);
        $this->assertEquals('Resource not found', $content['error']);

        [$content, $status] = $this->handleException(new NotFoundHttpException('Driver not found'));
        $this->assertEquals(404, $status);
        $this->assertEquals('Driver not found', $content['error']);
    }

    public function testHttpExceptionDevMode(): void
    {
        [$content, $status] = $this->handleException(new BadRequestHttpException('Invalid input'), 'dev');
        $this->assertEquals(400, $status);
        $this->assertEquals('Invalid input', $content['error']);

        [$content, $status] = $this->handleException(new BadRequestHttpException(''), 'dev');
        $this->assertEquals('An error occurred', $content['error']);
    }

    public function testHttpExceptionProductionMode(): void
    {
        [$content, $status] = $this->handleException(new BadRequestHttpException('Sensitive details'));
        $this->assertEquals(400, $status);
        $this->assertEquals('An error occurred', $content['error']);
    }

    public function testGenericExceptionDevMode(): void
    {
        [$content, $status] = $this->handleException(new \RuntimeException('Database failed'), 'dev');
        $this->assertEquals(500, $status);
        $this->assertEquals('Database failed', $content['error']);
    }

    public function testGenericExceptionProductionMode(): void
    {
        [$content, $status] = $this->handleException(new \RuntimeException('Sensitive error'));
        $this->assertEquals(500, $status);
        $this->assertEquals('Internal server error', $content['error']);
    }

    public function testTestEnvironmentIsNotDebug(): void
    {
        [$content] = $this->handleException(new \RuntimeException('Test error'), 'test');
        $this->assertEquals('Internal server error', $content['error']);
    }
}
