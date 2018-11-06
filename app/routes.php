<?php

require 'config.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
    return $pdo;
};
// $this->db (or) $this->container->db to access the container object

$container['view'] = new \Slim\Views\PhpRenderer(__DIR__.'/../app/Views/');

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->post('/time-entries', '\App\Controllers\TimeEntryController:store');
$app->get('/time-entries', '\App\Controllers\TimeEntryController:index');
$app->get('/stats', '\App\Controllers\TimeEntryController:stats');
$app->get('/graph', '\App\Controllers\TimeEntryController:graph');
$app->get('/projects', '\App\Controllers\ProjectController:index');
$app->get('/requirements', '\App\Controllers\RequirementController:index');
$app->get('/', '\App\Controllers\TimeEntryController:index');
$app->get('/home', '\App\Controllers\TimeEntryController:home');

$app->run();
