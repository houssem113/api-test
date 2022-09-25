<?php

namespace App\EventSubscriber;

use App\Exception\AppExceptionInterface;
use ErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpExceptionSubscriber implements EventSubscriberInterface
{

    public function onKernelException(ExceptionEvent $event): void
    {

        $exception = $event->getThrowable();

        $isAppException = $exception instanceof AppExceptionInterface;        

        if ($isAppException) {

            $message = $exception->getMessage();
        } elseif ($exception instanceof NotFoundHttpException) {

            $message = "This endpoint does not exist";
        } else {

            $message = "Uknown error";
        }


        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : ($exception instanceof AppExceptionInterface ? 400 : 500);

        $response = new JsonResponse(["error" => $message], $status);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
