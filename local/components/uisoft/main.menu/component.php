<?php

use Uisoft\App\MainMenu;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$items = MainMenu::get();
$arResult["ITEMS"] = $items;
$this->includeComponentTemplate();