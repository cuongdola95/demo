<?php
include 'config.php';
include 'Persistents/Users.php';
include 'Models/Core.php';
include 'Models/Db.php';
include 'Models/Users.php';

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
<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $modelUser = new Models_Users();

    $list = $modelUser->getListId($id);
}
?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <form method="POST" action="#" style="padding: 15px;">
                    <?php
                    foreach ($list as $key) {

                        ?>
                        <div class="form-group">
                            <label for="my-input">name</label>
                            <input id="name" class="form-control" type="text" name="uname" value="<?php echo $key->name; ?>">
                        </div>
                        <div class="form-group">
                            <label for="my-input">phone</label>
                            <input id="email" class="form-control" type="text" name="uphone" value="<?php echo $key->phone; ?>">
                        </div>
                        <div class="form-group">
                            <label for="my-input">order</label>
                            <input id="order" class="form-control" type="text" name="uorder" value="<?php echo $key->orders; ?>">
                        </div>
                        <div class="form-group">
                            <label for="my-input">status</label>
                            <input id="sts" class="form-control" type="text" name="usts" value="<?php echo $key->status; ?>">
                        </div>
                        <button class="btn btn-danger btn-block" name="submit">submit</button>
                    <?php
                    }
                    $us = $modelUser->getObject($id);
                    if (isset($_POST['submit'])) {

                        $name = $_POST['uname'];
                        $phone = $_POST['uphone'];
                        $orders = $_POST['uorder'];
                        $stt = $_POST['usts'];

                        if (is_object($us)) {
                            $us->name = $name;
                            $us->phone = $phone;
                            $us->orders = $orders;
                            $us->status = $stt;
                            $modelUser->setPersistents($us);
                            $modelUser->edit(array('name','phone','orders','status'), true);
                        }
                        header('Location:index.php');
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>