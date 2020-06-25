<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Log\LoggerInterface;
MercadoPago\SDK::setAccessToken('APP_USR-8058997674329963-062418-89271e2424bb1955bc05b1d7dd0977a8-592190948');

return function (App $app) {
    $container = $app->getContainer();

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->post('/notificationManager', function (Request $request, Response $response) use ($container) {
        
        $parsedBody = $request->getParsedBody();

        switch($parsedBody['type']) {
            case "payment":
                //$payment = MercadoPago\Payment.find_by_id($parsedBody['id']);
                
                /*$handler = curl_init('https://api.mercadopago.com/v1/payments/'.$parsedBody['id'].'?access_token=APP_USR-8058997674329963-062418-89271e2424bb1955bc05b1d7dd0977a8-592190948');
                curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($handler);
                curl_close($handler);*/
                $logger = $container->get(LoggerInterface::class);
                $logger->info(json_encode($parsedBody));
                $response->getBody()->write('okay bro');
                return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
                break;
            case "plan":
                $plan = MercadoPago\Plan.find_by_id($_POST["id"]);
                break;
            case "subscription":
                $plan = MercadoPago\Subscription.find_by_id($_POST["id"]);
                break;
            case "invoice":
                $plan = MercadoPago\Invoice.find_by_id($_POST["id"]);
                break;
        }

        $response->getBody()->write();
        return $response
                ->withHeader('Content-Type', 'application/json');
        
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
