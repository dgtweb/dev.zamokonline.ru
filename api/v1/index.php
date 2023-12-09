<?php

declare(strict_types=1);

use ALS\Helper\SessionMode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// Объект ответа
$response = new JsonResponse();
$response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
$responseData = ['success' => true, 'data' => null, 'error' => null];

try {
    // Подключаем маршруты
    $routes = require $_SERVER['DOCUMENT_ROOT'] . '/local/config/routes.php';

    // Создаем объект с данными запроса
    $request = Request::createFromGlobals();

    // Создаем контекст запроса из объекта запроса
    $context = new RequestContext();
    $context->fromRequest($request);

    // Объект для проверки маршрута
    $matcher = new UrlMatcher($routes, $context);

    // Проверяем запрошенный путь и получаем параметры
    $request->attributes->add($matcher->match($request->getPathInfo()));

    // Объект для определения контроллера
    $controllerResolver = new ControllerResolver();

    // Объект для определения аттрибутов контроллера
    $argumentResolver = new ArgumentResolver();

    // Определяем контроллер и аргументы
    $controller = $controllerResolver->getController($request);
    $arguments = $argumentResolver->getArguments($request, $controller);

    // Устанавливаем режим работы сессии
    if ($request->attributes->get(SessionMode::KEY) !== null) {
        SessionMode::setMode($request->attributes->get(SessionMode::KEY));
    }

    // Подключение Bitrix
    define('LANGUAGE_ID', 'ru');
    define('STOP_STATISTICS', true);
    define('NO_KEEP_STATISTIC', true);
    define('NO_AGENT_STATISTIC', true);
    // Запрет отправки почтовых уведомлений
    define('DisableEventsCheck', true);
    // Запрет выполнения агентов
    define('NO_AGENT_CHECK', true);

    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

    ob_get_clean();

    // Вызываем контроллер и устанавливаем данные ответа
    $responseData['data'] = call_user_func_array($controller, $arguments);

} catch (HttpException|MethodNotAllowedException|ResourceNotFoundException $exception) {
    // Ошибки клиента
    if ($exception instanceof MethodNotAllowedException) {
        $code = Response::HTTP_METHOD_NOT_ALLOWED;

        $response->headers->set('Allow', implode(',', $exception->getAllowedMethods()));
    } elseif ($exception instanceof ResourceNotFoundException) {
        $code = Response::HTTP_NOT_FOUND;
    } else {
        $code = $exception->getStatusCode();
    }

    $response->setStatusCode($code);
    $responseData['success'] = false;
    $responseData['error'] = $exception->getMessage();
} catch (Throwable $exception) {
    // Ошибки приложения
    $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

    /**
     * @todo добавить логирование
     */
    $responseData['success'] = false;
    $responseData['error'] = 'Error inner';

    //Debug
    $responseData['debug_/api/v1/index.php'] = $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine();
}

// если в ответе есть установка cookie
$response->setData($responseData);
// Отправляем ответ
$response->send();
