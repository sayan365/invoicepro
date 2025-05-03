<?php
session_start();
include('../includes/db.php');
if (!isset($_SESSION['user'])) {
  header('Location: ../index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user_id = $_SESSION['user']['id'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];

  $stmt = $conn->prepare("INSERT INTO services (user_id, name, description, price) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("sssd", $user_id, $name, $description, $price);
  $stmt->execute();

  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Add Service</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
<div class="items-center ml-64 justify-center min-h-screen">
<form method="POST" class="bg-white p-6 rounded-xl shadow-lg space-y-4 w-full max-w-lg">
  <h2 class="text-2xl font-bold text-center">Add New Service</h2>

  <input type="text" name="name" placeholder="Service Name" class="w-full border p-2 rounded" required>
  <textarea name="description" placeholder="Description" class="w-full border p-2 rounded"></textarea>
  <input type="number" name="price" placeholder="Price" class="w-full border p-2 rounded" required step="0.01">

  <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Save Service</button>

  <a href="index.php" class="block text-center text-blue-500 mt-2">Back to Services</a>
</form>
</div>
</main>
</body>
</html>
