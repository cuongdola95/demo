<?php
include '../config.php';

// get post data
foreach($_POST as $key => $value) {
    $$key = trim($value);
    if(!Library_Validation::antiSql($value)) {
        //echo json_encode(array('code' => 1, 'msg' => 'Dữ liệu có vấn đề!'));
        //exit();
    }
}

if(!is_object($adminuser)) {
    echo json_encode(array('code' => 1, 'msg' => 'Bạn chưa đăng nhập!'));
    exit();
}

// check phone
if($type == 1 && $adminuser->refer == 33) {
    echo json_encode(array('code' => 1, 'msg' => 'Lien he admin de chay tra truoc!'));
    exit();
}

// check tra truoc
$orders = 0;
if($type == 1) {
    $orders = 9;
}

// check phone
if(!Library_Validation::isPhoneNumber($phone) && ($type == 0 || $type == 1)) {
    echo json_encode(array('code' => 1, 'msg' => 'SDT không hợp lệ!'));
    exit();
}

// check -
if(strpos($phone, "-") !== FALSE) {
    echo json_encode(array('code' => 1, 'msg' => 'FTTH có dấu "-" không được thanh toán, vui lòng loại bỏ!'));
    exit();
}

$type = intval($type);
$canthanhtoan = intval($canthanhtoan);

if(!in_array($type, array(0,1,2))) {
    echo json_encode(array('code' => 1, 'msg' => 'Hình thức thanh toán không hợp lệ!'));
    exit();
}

if($gop != 0 && $gop != 1) {
    echo json_encode(array('code' => 1, 'msg' => 'Kiểu gộp không hợp lệ!'));
    exit();
}

if($canthanhtoan < 50000) {
    echo json_encode(array('code' => 1, 'msg' => 'Số tiền cần thanh toán >= 50,000!'));
    exit();
}

// kiem tra so du
if($adminuser->balance < $canthanhtoan) {
    echo json_encode(array('code' => 1, 'msg' => 'Tài khoản bạn không đủ tiền!'));
    exit();
}

$file_log = "add_phone.log";
Library_Log::writeOpenTable($file_log);
Library_Log::writeHtml("Admin : " . $adminuser->getId(), $file_log);
Library_Log::writeHtml("Request : Phone : {$phone}, Thanh toan : {$canthanhtoan}, Type : {$type}, Gop : {$gop}", $file_log);
$phone=trim(strtolower($phone));
$order = new Persistents_Orders();
$order->user_id =$adminuser->getId();
$order->note ="nạp đơn";
$order->time =time();
$order->orders =1;
$order->status =1;
$models_order = new Models_Orders($order);
$order_id = $models_order->add(1);
$phones = new Persistents_Phones();
$phones->phone = $phone;
$phones->loai = $loai;
$phones->type = $type;
$phones->canthanhtoan = $canthanhtoan;
$phones->userid = $adminuser->getId();
$phones->order_id = $order_id;
$phones->time = time();
$phones->orders = $orders;
$phones->status = 1;

$models_phones = new Models_Phones($phones);
$last_id = $models_phones->add(1);
    
if($last_id) {
   $db = Models_Db::getDBO();
    $db->beginTransaction();
    
    $models_users = new Models_Users();
    $users = $models_users->getObject($adminuser->getId(), 1);

    $current_balance = $users->balance;
    $update_balance = $current_balance - $canthanhtoan;

    Library_Log::writeHtml("Balance : " . number_format($current_balance), $file_log);
    Library_Log::writeHtml("Update Balance : " . number_format($update_balance), $file_log);

    $users->balance = $update_balance;
    $models_users->setPersistents($users);
    $models_users->edit(array('balance'), 1);
    $db->commit();
    
    $phones = $models_phones->getObject($last_id);
    $phones->last_balance = $update_balance;
    $models_phones->setPersistents($phones);
    $models_phones->edit(array('last_balance'), 1);
    
    // them vao lich su cong tien
    $histories = new Persistents_Histories();
    $histories->admin_id = $adminuser->getId();
    $histories->user_add = $adminuser->getId();
    $histories->cur_balance = $current_balance;
    $histories->up_balance = $update_balance;
    $histories->money = $canthanhtoan*-1;
    $histories->time = time();
    $histories->note = 'Phone id : ' . $last_id;
    $histories->status = 1;
    $models_histories = new Models_Histories($histories);
    $models_histories->add();
    
    echo json_encode(array('code' => 0, 'msg' => 'thanh cong'));
}
else {
    echo json_encode(array('code' => 1, 'msg' => 'SĐT đã tồn tại trong hệ thống!'));
}
