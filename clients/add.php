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
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $company = $_POST['company'];
  $address = $_POST['address'];

  $stmt = $conn->prepare("INSERT INTO clients (user_id, name, email, phone, company, address) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssss", $user_id, $name, $email, $phone, $company, $address);
  $stmt->execute();

  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Add Client</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex ">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
  <div class="items-center ml-64 justify-center min-h-screen">
<form method="POST" class="bg-white p-6 rounded-xl shadow-lg space-y-4 w-full max-w-lg">
  <h2 class="text-2xl font-bold text-center">Add New Client</h2>

  <input type="text" name="name" placeholder="Client Name" class="w-full border p-2 rounded" required>
  <input type="email" name="email" placeholder="Email" class="w-full border p-2 rounded">
  <input type="text" name="phone" placeholder="Phone" class="w-full border p-2 rounded">
  <input type="text" name="company" placeholder="Company" class="w-full border p-2 rounded">
  <textarea name="address" placeholder="Address" class="w-full border p-2 rounded"></textarea>

  <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Save Client</button>

  <a href="index.php" class="block text-center text-blue-500 mt-2">Back to Clients</a>
</form>
</div>
</main>
</body>
</html>
