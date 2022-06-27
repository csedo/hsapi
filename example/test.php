<?php

use hscore\RequestGenerator;
require '../vendor/autoload.php';

$request = new RequestGenerator('products', 'json');
$request->authenticate('hs_demo', '123', '3e4ef8e3746947f2a9855cb585c2fec0');

$response = $request->send();

echo $response;