<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\EventListeners;

use App\Exception\ApiException;
use App\Response\ApiResponse;
use App\Service\ClickHouseService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Validator\ConstraintViolation;

class KernelListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    protected ?\Throwable $exception = null;

    public function __construct(
        private readonly ClickHouseService $clickhouse
    ) {
    }

    #[AsEventListener(event: 'kernel.request', priority: 9998)]
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->get('_route');

		// Если нам прислали json, то преобразуем
		if(!$request->request->all() && $request->getContent()) {
			$request->request->replace($request->toArray());
		}

        $isApiClient = null !== $route && (false !== mb_stripos((string) $route, '/api'));

		// Эмитация бурной деятельности
        if ($isApiClient) {
            $this->clickhouse->insert($request->request->all());
        }
    }

    #[AsEventListener(event: 'kernel.exception')]
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $httpCode = $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

		// Эмитация бурной деятельности
        if ($exception instanceof ApiException) {
            $status = $exception->getCode();
            $message = $exception->getMessage();


            $this->logger->notice(
                'Api exception',
                [
                    'status' => 'error',
                    'message' => $message,
					'code' => $httpCode,
                    'exception' => $this->exception,
                ]
            );



            $event->setResponse(new ApiResponse(
                [],
                [
                    'status' => 'error',
					'code' => $httpCode,
                    'message' => $message,
					'errors' => $this->prepareErrorsArray($exception)
                ],
                $httpCode
            ));
            $event->getResponse()->setStatusCode($httpCode);
        } else {
            $this->logger->critical(
                'Kernel exception',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'http_code' => $httpCode,
                    'exception' => $exception,
                ]
            );
        }
    }

	private function prepareErrorsArray(ApiException $e): array
	{
		$errors = $e->getErrors();
		$uniqErrors = [];
		if ($errors) {
			foreach ($errors as $violations) {
				if (\is_array($violations)) {
					/* @var ConstraintViolation $error */
					foreach ($violations as $error) {
						$errorHash = $error->getMessage();
						if (!\in_array($errorHash, $uniqErrors, true)) {
							$result[] = $error->getPropertyPath().' - '.$error->getMessage()."\n";
							$uniqErrors[] = md5($error->getMessage());
						}
					}
				} else {
					$result[] = $violations;
				}
			}
		}
		$e->setErrors([]);

		return $result;
	}
}
