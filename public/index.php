<?php

require '../vendor/autoload.php';

// Setup custom Twig view
$twigView = new \Slim\Extras\Views\Twig();

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => $twigView,
    'templates.path' => '../templates/',
));

// Make a new connection
$app->db = Capsule\Database\Connection::make('default', array(
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'port'      => 3306,
    'database'  => 'bowling',
    'username'  => 'root',
    'password'  => '',
    'prefix'    => '',
    'charset'   => "utf8",
    'collation' => 'utf8_general_ci'    
), true);


$app->get('/', function () use ($app) {
    $users = User::all();

    foreach ($users as $user) {
        $user->score = Score::where('user_id', '=', $user->id)->sum('points');
    }
    
    $app->render('players.html', array('users' => $users));
});

$app->get('/users', function () {
    $users = User::all();
    echo $users->toJson();
});

$app->get('/team/:team', function ($team) {   
    $users = User::where('team', '=', $team)->get();    
    echo $users->toJson();
});

$app->get('/user/:id', function ($id) {
    $user = User::with('scores')->where('id', '=', $id)->get();
    echo $user->toJson();    
        
    echo Score::where('user_id', '=', $id)->sum('points');
});

$app->run();