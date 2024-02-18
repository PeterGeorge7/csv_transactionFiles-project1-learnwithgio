<?php

declare(strict_types=1);

use App\App;
use App\Config;
use App\Controllers\HomeController;
use App\Controllers\TransactionsController;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('STORAGE_PATH', __DIR__ . '/../storage');
define('VIEW_PATH', __DIR__ . '/../views');

$router = new Router();

// $transacitonId = -1;

$router
    ->get('/', [HomeController::class, 'index'])
    ->get('/transactions', [TransactionsController::class, 'index'])
    ->get('/transactions/transactions-details', [TransactionsController::class, 'details'])
    ->post('/transactions', [TransactionsController::class, 'submitFileFromUser']);

(new App(
    $router,
    ['uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD']],
    new Config($_ENV)
))->run();
