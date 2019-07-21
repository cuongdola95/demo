<?php
include 'config.php';
include 'Persistents/Users.php';
include 'Models/Core.php';
include 'Models/Db.php';
include 'Models/Users.php';

$modelUser = new Models_Users();

$list = $modelUser->getlist();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <title>crud</title>
</head>

<body>
    <div class="container-fluid">
        <br>
        <div class="row">
            <div class="col-lg-6" style="background: aquamarine;">
                <form method="POST" action="#" style="padding: 15px;">
                    <div class="form-group">
                        <label for="my-input">name</label>
                        <input id="name" class="form-control" type="text" name="name">
                    </div>
                    <div class="form-group">
                        <label for="my-input">phone</label>
                        <input id="email" class="form-control" type="text" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="my-input">order</label>
                        <input id="order" class="form-control" type="text" name="order">
                    </div>
                    <div class="form-group">
                        <label for="my-input">status</label>
                        <input id="sts" class="form-control" type="text" name="sts">
                    </div>
                    <button class="btn btn-danger btn-block" name="submit" onclick="return confirm('Are you sure you want to add new')">thêm</button>
                </form>
            </div>
            <div class="col-lg-6">
                <table class="table">
                    <?php
                    if (!empty($list)) {
                        foreach ($list as $key) {
                            ?>
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>name</th>
                                    <th>phone</th>
                                    <th>order</th>
                                    <th>status</th>
                                    <th>chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="row"><?php echo $key->getId() ?></td>
                                    <td><?php echo $key->name  ?></td>
                                    <td><?php echo $key->phone ?></td>
                                    <td><?php echo $key->orders  ?></td>
                                    <td><?php echo $key->status   ?></td>
                                    <td>
                                        <span class="btn btn-danger"> <a href="edit.php?id=<?php echo $key->getId() ?>">sửa</a> </span>
                                        <span class="btn btn-warning"><a href="delete.php?id=<?php echo $key->getId() ?>" onclick="return confirm('are you sure')">xóa</a> </span>
                                    </td>
                                </tr>
                            </tbody>
                        <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>

    </div>

    <?php
    if (isset($_POST['submit'])) {
        $user = new Persistents_Users();
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $orders = $_POST['order'];
        $status = $_POST['sts'];
        $user->name =$name;
        $user->phone =  $phone;
        $user->orders =  $orders;
        $user->status = $status;
        $modelUser->setPersistents($user);
        $modelUser->add();
        
    }

    ?>
</body>

</html>