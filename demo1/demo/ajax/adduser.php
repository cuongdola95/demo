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

// check name
if(strlen($name) <= 5) {
    echo json_encode(array('code' => 1, 'msg' => 'Tên tuổi dài dài 1 tý!'));
    exit();
}

// check phone
if(!Library_Validation::isPhoneNumber($userphone)) {
    echo json_encode(array('code' => 1, 'msg' => 'SDT không hợp lệ!'));
    exit();
}

if(strlen($password) <= 7) {
    echo json_encode(array('code' => 1, 'msg' => 'Mật khẩu dài dài 1 tý!'));
    exit();
}

// them vao data
$users = new Persistents_Users();
$users->name = $name;
$users->phone = $userphone;

$salt = md5(rand(0, 10000000000000));
$password = hash('sha256', $password . $salt);

$users->refer = $adminuser->getId();
$users->salt = $salt;
$users->password = $password;
$users->time = time();
$users->status = 1;

$models_users = new Models_Users($users);
if($models_users->add()) {
    echo json_encode(array('code' => 0, 'msg' => 'Thanh cong!'));
}
else {
    echo json_encode(array('code' => 1, 'msg' => 'Tài khoản đã tồn tại!'));
}
