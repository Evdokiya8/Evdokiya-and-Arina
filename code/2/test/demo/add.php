<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include("config.php");
$db = mysqli_connect($dbhost, $dbuser, $dbpassword, $database) or die("Connection Error: " . mysqli_error($db));


if (!(isset($_GET['show']))) {
    //t_name=123&t_cost=123&t_url=123
    $name=mysqli_real_escape_string($db, $_POST['t_name']);
    $cost=(float)mysqli_real_escape_string($db,$_POST['t_cost']);
    $url=mysqli_real_escape_string($db,$_POST['t_url']);

    $SQL= "INSERT INTO demo (name, cost, path) value ('$name','$cost','$url')";
    $result = mysqli_query($db, $SQL) or die("Couldn't execute query." . mysqli_error($db));
}
$SQL= "SELECT * from demo order by 2";
$result = mysqli_query($db, $SQL) or die("Couldn't execute query." . mysqli_error($db));
$ret= [];
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $ret[]=$row;
}
die(json_encode($ret));