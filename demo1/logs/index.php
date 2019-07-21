<?php
include 'config.php';

$models_users = new Models_Users();
$list = $models_users->getList();

echo "<pre>";
var_dump($list);

