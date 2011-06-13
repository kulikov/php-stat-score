<?php

date_default_timezone_set('Europe/Moscow');

ini_set('memory_limit', '1024M');
ini_set('post_max_size', '512M');
ini_set('max_execution_time', '3600');


require_once 'lib/DataLoader.php';
require_once 'lib/ResultHandler.php';
require_once 'lib/ChampionshipBuilder.php';
require_once 'lib/processors/DisplayXlsTable.php';

require_once 'lib/providers/Parimatch.php';
require_once 'lib/providers/Xscores.php';

require_once 'lib/util/threads/ThreadManager.php';
require_once 'lib/util/FileCacher.php';


/* выбираем провайдера исходных данных */
if (stristr($_SERVER['QUERY_STRING'], 'parimatch.com')) {
    $provider = new ParimatchProvider();
} else {
    $provider = new XscoresProvider();
}


$dataLoader = DataLoader::factory($provider);


/**
 * Будем кешировать результаты на час
 */
$dataLoader->setCacher(FileCacher::factory(array(
    'cacheDir' => dirname(__FILE__) . '/cache/',
    'lifetime' => 3600,
)));


@list($requestUrl, $extraParams) = explode('|||', !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '-', 2);


/**
 * После загрузки всех данных будем выводит таблицу для экселя со всеми расчетами
 */
$dataLoader->addCompleteHandler(
    ResultHandler::factory(new DisplayXlsTableStrategy($extraParams), ChampionshipBuilder::getInstance())
);


/**
 * Загрузка данных будет проходить мультипроцесорно — конфигурим тред менеджер
 */
$threadManager = ThreadManager::factory(array(
    'scriptPath' => dirname(__FILE__) . '/worker.php',
    'threadUrl'  => 'http://' . $_SERVER['SERVER_NAME'] .'/worker.php',
    'adapter'    => 'UnixProcess',
));


/**
 * Запускаем расчет
 */
$dataLoader->loadChampionship($requestUrl, $threadManager);