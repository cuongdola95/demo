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

$users = $models_users->getObject($userId);
if(is_object($users) && $users->balance > 0) {
    // kiem tra xem user nay da hoan tien het chua
    $models_phones = new Models_Phones();
    $list_phones = $models_phones->customFilter('id', array('userid' => $userId, 'canthanhtoan' => array(0 , '>')));
    if(count($list_phones) > 0) {
        echo json_encode(array('code' => 1, 'msg' => 'Tài khoản chưa hoàn tiền hết, yêu cầu hoàn tiền!'));
    }
    else {
        // cong tien 
        $db = Models_Db::getDBO();
        $db->beginTransaction();
        $users = $models_users->getObject($users->getId(), 1);
    
        $current_balance = $users->balance;
        $users->balance = 0;
        $models_users->setPersistents($users);
        $models_users->edit(array('balance'), 1);
        
        $db->commit();
        
        // them vao lich su cong tien
        $histories = new Persistents_Histories();
        $histories->admin_id = $adminuser->getId();
        $histories->user_add = $users->getId();
        $histories->cur_balance = $current_balance;
        $histories->up_balance = 0;
        $histories->money = $current_balance * -1;
        $histories->time = time();
        $histories->note = 'Reset';
        $histories->status = 1;
        $models_histories = new Models_Histories($histories);
        $models_histories->add();
        
        echo json_encode(array('code' => 0, 'msg' => 'thanh cong'));
    }
}
else {
    echo json_encode(array('code' => 1, 'msg' => 'Taì khoản đã reset!'));
}
