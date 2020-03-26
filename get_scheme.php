<?php

include __DIR__ . '/config.php';
include __DIR__ . '/functions.php';

echo json_encode(get_bitrix_scheme(realpath(__DIR__ . '/..')));