<?php
include '../config.php';
header("Content-Type: application/json;charset=utf-8");

if(!is_object($adminuser)) {
    echo json_encode(array('code' => 1, 'msg' => 'Bạn chưa đăng nhập!'));
    exit();
}

$pk = intval($_POST['pk']);
$value = intval($_POST['value']);

$models_users = new Models_Users();
$users = $models_users->getObject($pk);
if(is_object($users)) {
    if($value == 0) {
        $users->dathu = 0;
    }
    else {
        $users->dathu = $users->dathu + $value;
    }
    $models_users = new Models_Users($users);
    $models_users->edit(array('dathu'), 1);
    echo json_encode(array('code' => 0, 'msg' => 'Success!'));
}
else {
    echo json_encode(array('code' => 1, 'msg' => 'Object not found!'));
}



