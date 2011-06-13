<?php

parse_str(@$_SERVER['argv'][1], $request);
$request = array_merge((array) $request, @$_REQUEST);

require_once $request['provider']['file'];
require_once 'lib/DataLoader.php';

$providerClass = $request['provider']['class'];
$dataLoader = DataLoader::factory(new $providerClass());

$response = array(
    'number'    => $request['number'],
    'gamesData' => $dataLoader->loadTeamGames($request['url']),
);

print serialize($response);
