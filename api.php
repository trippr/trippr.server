<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->get('/hello/:name', function ($name) {
        echo "Hello, $name";
});
$app->get('/destinations/search/:text(/:excluded)', function($text, $excluded="") {
    echo "text is: ". $text;
    echo "excluded is: ". $excluded;
});
$app->run();
