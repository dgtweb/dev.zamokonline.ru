<?php

/** @var array $arResult */
/** @global CMain $APPLICATION */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<?
$APPLICATION->RestartBuffer();
unset($arResult["COMBO"]);

echo CUtil::PHPToJSObject($arResult, true);
