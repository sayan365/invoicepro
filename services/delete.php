<?php
session_start();
include('../includes/db.php');
if (!isset($_SESSION['user'])) {
  header('Location: ../index.php');
  exit;
}

if (isset($_GET['id'])) {
  $service_id = $_GET['id'];
  $user_id = $_SESSION['user']['id'];
  $stmt = $conn->prepare("DELETE FROM services WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $service_id, $user_id);
  $stmt->execute();
}

header("Location: index.php");
exit;
