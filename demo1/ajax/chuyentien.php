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
if(!Library_Validation::isPhoneNumber($phonenhan)) {
    echo json_encode(array('code' => 1, 'msg' => 'SDT không hợp lệ!'));
    exit();
}

if(intval($sotiennhan) > 50000000) {
    echo json_encode(array('code' => 1, 'msg' => 'Số tiền <= 50,000,000'));
    exit();
}

if($adminuser->balance < $sotiennhan) {
    echo json_encode(array('code' => 1, 'msg' => 'Số tiền trong khoản ko du'));
    exit();
}

$models_users = new Models_Users();
$users = $models_users->getObjectByCondition('', array('status' => 1, 'phone' => $phonenhan));
if(is_object($users)) {
    // chck ko chuyen cho chinh minh
    if($adminuser->getId() == $users->getId()) {
        echo json_encode(array('code' => 1, 'msg' => 'Bạn không thể tự chuyển cho chính bạn!'));
        exit();
    }

    if($adminuser->getId() != $users->refer) {
        echo json_encode(array('code' => 1, 'msg' => 'Tai khoan nhan khong phai thuoc cap cua ban!'));
        exit();
    }
    
    $file_log = "chuyen_money.log";
    Library_Log::writeOpenTable($file_log);
    Library_Log::writeHtml("Admin : " . $adminuser->getId(), $file_log);
    Library_Log::writeHtml("Userchuyen : " . $users->getId(), $file_log);
    Library_Log::writeHtml("Request : PhoneAdd : {$phonenhan}, Money : {$sotiennhan}", $file_log);
    
    // cong tien 
    $db = Models_Db::getDBO();
    $db->beginTransaction();
    $users = $models_users->getObject($users->getId(), 1);
    $adminuser = $models_users->getObject($adminuser->getId(), 1);
    
    $current_balance = $users->balance;
    $update_balance = $current_balance + $sotiennhan;

    $current_balance2 = $adminuser->balance;
    $update_balance2 = $current_balance2 - $sotiennhan;

    Library_Log::writeHtml("Balance : " . number_format($current_balance), $file_log);
    Library_Log::writeHtml("Update Balance : " . number_format($update_balance), $file_log);

    Library_Log::writeHtml("Balance2 : " . number_format($current_balance2), $file_log);
    Library_Log::writeHtml("Update Balance2 : " . number_format($update_balance2), $file_log);

    $users->balance = $update_balance;
    $models_users->setPersistents($users);
    $models_users->edit(array('balance'), 1);

    $adminuser->balance = $update_balance2;
    $models_users->setPersistents($adminuser);
    $models_users->edit(array('balance'), 1);

    
    // them vao lich su cong tien
    $histories = new Persistents_Histories();
    $histories->admin_id = $adminuser->getId();
    $histories->user_add = $users->getId();
    $histories->cur_balance = $current_balance;
    $histories->up_balance = $update_balance;
    $histories->money = $sotiennhan;
    $histories->time = time();
    $histories->note = "Cong tu " . $adminuser->name;
    $histories->status = 1;
    $models_histories = new Models_Histories($histories);
    $models_histories->add();
    $db->commit();
    
    echo json_encode(array('code' => 0, 'msg' => 'Thanh cong!'));
    Library_Log::writeCloseTable($file_log);
}
else { 
    echo json_encode(array('code' => 1, 'msg' => 'Tài khoản không tồn tại!'));
}
