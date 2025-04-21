<?php
include '../includes/connection.php';

$action = $_POST['action'];
if ($action == 'pending') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($id == "" || $status == "") {
        echo "Some Fields Not Found";
        return;
    }

    $sql = "UPDATE `order` SET status='$status' WHERE id='$id'";
    if (mysqli_query($con, $sql)) {
        echo "Order Is Replesd";
        return;
    } else {
        echo "Somthing Went's Wrong. Please Try Again.";
        return;
    }
} else if ($action == 'cancel') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($id == "" || $status == "") {
        echo "Some Fields Not Found";
        return;
    }

    $sql = "UPDATE `order` SET status='$status' WHERE id='$id'";
    if (mysqli_query($con, $sql)) {
        echo "Order Cancel";
        return;
    } else {
        echo "Somthing Went's Wrong. Please Try Again.";
        return;
    }
}
