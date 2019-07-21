<?php
include '../config.php';

// get post datas
foreach($_GET as $key => $value) {
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

$stt = 0;
$models_histories = new Models_Histories();
$lists_his = $models_histories->customQuery("SELECT * FROM Histories WHERE admin_id = {$adminuser->getId()} OR user_add = {$adminuser->getId()} ORDER BY id DESC LIMIT 0, 300");
foreach ($lists_his as $obj) {
    $stt++;
?>
    <tr>
        <td><?= $stt ?></td>
        <td><?= $models_users->getObject($obj->user_add)->name ?></td>
        <td class="<?= $obj->money > 0 ? 'text-danger' : 'text-warning' ?>"><?= $obj->money > 0 ? ' + ' . number_format($obj->money) : ' - ' . number_format($obj->money*-1) ?> VND</td>
        <td class="text-info"><?= number_format($obj->cur_balance) ?> VND</td>
        <td class="text-success"><?= number_format($obj->up_balance) ?> VND</td>
        <td><?= date('d/m/Y H:i:s', $obj->time) ?></td>
        <td><?= $obj->note ?></td>
    </tr>
<?php
    }
?>