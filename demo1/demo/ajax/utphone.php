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

$id = intval($id);

$models_phones = new Models_Phones();
$obj = $models_phones->getObject($id);
if(is_object($obj)) {
    if(in_array($adminuser->getId(), array(1, 2, 261))) {
        $obj->orders = 10;
    }
    else {
        $obj->orders = 9;
    }
    $models_phones->setPersistents($obj);
    $models_phones->edit(array('orders'), 1);
    echo json_encode(array('code' => 0, 'msg' => 'Thanh cong!'));
}
else {
    echo json_encode(array('code' => 1, 'msg' => 'Phone không tồn tại!'));
}
