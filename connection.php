<?php
$con = mysqli_connect('localhost', 'root', 'resende123', 'datatable_db');
if(mysqli_connect_errno())
{
    echo "Database connection error!";
    exit;
}
?>