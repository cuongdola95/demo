<?php
include '../config.php';

// get post data
foreach($_POST as $key => $value) {
    $$key = trim($value);
    if(!Library_Validation::antiSql($value)) {
        echo json_encode(array('code' => 1, 'msg' => 'Dữ liệu có vấn đề!'));
        exit();
    }
}

if(!is_object($adminuser)) {
    echo json_encode(array('code' => 1, 'msg' => 'Bạn chưa đăng nhập!'));
    exit();
}

$userId = intval($userId);

$models_users = new Models_Users();
$users = $models_users->getObject($userId);
if(is_object($users)) {
    $models_users->execute("UPDATE Users set priority = 0");
    $users->priority = 1;
    $models_users->setPersistents($users);
    $models_users->edit(array('priority'), 1);
    echo json_encode(array('code' => 0, 'msg' => 'Thanh cong!'));
}
else {
    echo json_encode(array('code' => 1, 'msg' => 'Tài khoản không tồn tại!'));
}
