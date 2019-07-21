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

// check phone
if(!Library_Validation::isPhoneNumber($userphoneadd)) {
    echo json_encode(array('code' => 1, 'msg' => 'SDT không hợp lệ!'));
    exit();
}

if(intval($money) < 100000 || intval($money) > 500000000) {
    echo json_encode(array('code' => 1, 'msg' => 'Số tiền trong khoản 100,000 -> 500,000,000'));
    exit();
}

$models_users = new Models_Users();
$users = $models_users->getObjectByCondition('', array('status' => 1, 'phone' => $userphoneadd));
if(is_object($users)) {
    // chck ko cong cho chinh minh
    if($adminuser->getId() == $users->getId()) {
        echo json_encode(array('code' => 1, 'msg' => 'Bạn không thể tự cộng cho chính bạn!'));
        exit();
    }
    
    $file_log = "add_money.log";
    Library_Log::writeOpenTable($file_log);
    Library_Log::writeHtml("Admin : " . $adminuser->getId(), $file_log);
    Library_Log::writeHtml("UserAdd : " . $users->getId(), $file_log);
    Library_Log::writeHtml("Request : PhoneAdd : {$userphoneadd}, Money : {$money}", $file_log);
    
    // cong tien 
    $db = Models_Db::getDBO();
    $db->beginTransaction();
    $users = $models_users->getObject($users->getId(), 1);
    
    $current_balance = $users->balance;
    $update_balance = $current_balance + $money;

    Library_Log::writeHtml("Balance : " . number_format($current_balance), $file_log);
    Library_Log::writeHtml("Update Balance : " . number_format($update_balance), $file_log);

    $users->balance = $update_balance;
    $models_users->setPersistents($users);
    $models_users->edit(array('balance'), 1);
    $db->commit();
    
    // them vao lich su cong tien
    $histories = new Persistents_Histories();
    $histories->admin_id = $adminuser->getId();
    $histories->user_add = $users->getId();
    $histories->cur_balance = $current_balance;
    $histories->up_balance = $update_balance;
    $histories->money = $money;
    $histories->time = time();
    $histories->note = $adminuser->name;
    $histories->status = 1;
    $models_histories = new Models_Histories($histories);
    $models_histories->add();
    
    echo json_encode(array('code' => 0, 'msg' => 'Thanh cong!'));
    Library_Log::writeCloseTable($file_log);
}
else { 
    echo json_encode(array('code' => 1, 'msg' => 'Tài khoản không tồn tại!'));
}
