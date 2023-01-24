<?php

if(isset($_POST['submit'])){

    //Grabbing Users Details From Form
    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    //Instantiating Classes
    include "../classes/dbh.classes.php";
    include "../classes/login.classes.php";
    include "../classes/login-contr.classes.php";
    $signup = new LoginContr($uid, $pwd);
    //Running Error Handlers
    $signup->loginUser();



    //Going To Dashboard
    header("location: ../dashboard/dash.php");
}