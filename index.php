<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include __DIR__ . '/config.php';
include __DIR__ . '/functions.php';

$scheme1 = get_bitrix_scheme($bitrix1);
$scheme2 = get_bitrix_scheme($bitrix2);

echo compile_bitrix_diff($scheme1, $scheme2, $bitrix1, $bitrix2);