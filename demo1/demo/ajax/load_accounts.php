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

$models_users = new Models_Users();
$models_histories = new Models_Histories();
$models_phones = new Models_Phones();
$listusers = $models_users->customFilter('', array('status' => 1, 'refer' => $adminuser->getId()));
        
?>

<table class="table table-hover">
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Số dư</th>
        <th>Tổng tiền</th>
        <th>Đã thanh toán</th>
        <th>Thực thu</th>
        <th>Đã thu</th>
        <th>Tạo lúc</th>
        <th></th>
    </tr>
    <?php
        $year = date('Y');
        switch($month) {
            case 0 : $month = 10; $year = 2018;
                break;
            case -1 : $month = 11; $year = 2018;
                break;
            case -2 : $month = 12 ; $year = 2018;
                break;
        }

        $time = mktime(0, 0, 0, $month, 1, $year);
        $end_time = mktime(23, 59, 59, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year), $year);
        $stt = 0;
        foreach ($listusers as $obj) {
            $stt++;
            $link = 'viewlist.php?acc=' . base64_encode(Library_String::encryptStr($obj->getId(), $key_enc));
            $total_cong = $models_histories->getSumByColumn2('money', "admin_id = {$adminuser->getId()} AND user_add = {$obj->getId()} AND time BETWEEN {$time} AND {$end_time}");

            $total += $total_cong;
            $total_du += $obj->balance;

            $dathanhtoan = $models_phones->getSumByColumn2('dathanhtoan', "userid = {$obj->getId()} AND time BETWEEN {$time} AND {$end_time}");
            $tienam = $models_phones->getSumByColumn2('canthanhtoan', "userid = {$obj->getId()} AND canthanhtoan < 0 AND time BETWEEN {$time} AND {$end_time}");
            $thucthu = $dathanhtoan - $tienam*-1;
            
            $total_thucthu += $thucthu;
            $total_dathanhtoan += $dathanhtoan;
            $total_dathu += $obj->dathu;
    ?>
            <tr>
                <td><?= $stt ?></td>
                <td>
                    <a href="<?= $link ?>" target="_blank">
                        <?php
                            if($obj->priority == 1) {
                                echo "<span class='text-danger'>" . $obj->name . "({$obj->getId()})(UT)</span>";
                            }
                            else {
                                echo "<span class='text-info'>" . $obj->name . "({$obj->getId()})</span>";
                            }
                        ?>
                    </a>
                </td>
                <td><?= $obj->phone ?></td>
                <td class="text-info"><?= number_format($obj->balance) ?></td>
                <td class="text-success"><?= $total_cong < 0 ? 0 : number_format($total_cong) ?></td>
                <td class="text-warning"><?= number_format($dathanhtoan) ?></td>
                <td class="text-danger"><?= number_format($thucthu) ?></td>
                <td class="text-danger"><a href="#" class="dathu" data-name="dathu" data-pk="<?= $obj->getId() ?>"><?= number_format($obj->dathu) ?></a></td>
                <td><?= date('d/m/Y H:i:s', $obj->time) ?></td>
                <td>
                    <button type="button" class="btn btn-warning btn-xs btn-reset" data="<?= $obj->getId() ?>">Reset</button>
                </td>
            </tr>
    <?php
        }
    ?>
            <tr>
                <td colspan="3">Tổng kết</td>
                <td class="text-info"><?= number_format($total_du) ?></td>
                <td class="text-success"><?= $total < 0 ? 0 : number_format($total) ?></td>
                <td class="text-warning"><?= number_format($total_dathanhtoan) ?></td>
                <td class="text-danger"><?= number_format($total_thucthu) ?></td>
                <td class="text-info"><?= number_format($total_dathu) ?></td>
                <td colspan="1"></td>
            </tr>
</table>
<script>
    $(function(){
        $('.dathu').editable({
            type: 'text',
            url: '/ajax/dathu.php',
            title: 'Enter Number',
            success: function(res, newValue) {
                if(res.code == 1) {
                    return res.msg;
                }
            }
        });
    });
</script>