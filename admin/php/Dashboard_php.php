<?php
$conn = mysqli_connect('localhost', 'root', '', 'schooldb');
if (!$conn) {
  die('Connection Error' . mysqli_connect_error());
}
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Users_account_tbl");
$row = mysqli_fetch_assoc($result);
$totalCustomers = $row['total'];
?>