<?php
include 'config.php';
include 'Persistents/Users.php';
include 'Models/Core.php';
include 'Models/Db.php';
include 'Models/Users.php';


if(isset($_GET['id'])){
    $id = $_GET['id'];
    $modelUser = new Models_Users();
    $modelUser->delete($id);
   
}

header('Location:index.php');


