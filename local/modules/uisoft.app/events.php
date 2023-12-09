<?php

// Очистка кеша инфоблоков
$eventManager = \Bitrix\Main\EventManager::getInstance();
$methodRun = '\ALS\Helper\CacheManager::processingEvent';

$eventManager->addEventHandler('iblock', 'OnAfterIBlockUpdate',         $methodRun);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockPropertyAdd',    $methodRun);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockPropertyUpdate', $methodRun);

$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd',    $methodRun);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', $methodRun);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementDelete', $methodRun);

$eventManager->addEventHandler('iblock', 'OnAfterIBlockSectionAdd',    $methodRun);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockSectionUpdate', $methodRun);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockSectionDelete', $methodRun);
