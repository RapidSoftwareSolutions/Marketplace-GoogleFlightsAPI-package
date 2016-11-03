<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

//Guzzle HTTP client
$container['httpClient'] = function() {
    $guzzle = new GuzzleHttp\Client();
    return $guzzle;
};

//Pagination
$container['pager'] = function() {
    $pager = new Models\NextPage();
    return $pager;
};

//Json normalize
$container['toJson'] = function() {
    $toJson = new Models\normilizeJson();
    return $toJson;
};
