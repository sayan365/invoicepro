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
  $result = $conn->query("SELECT * FROM services WHERE id = $service_id AND user_id = $user_id");
  $service = $result->fetch_assoc();
  
  if (!$service) {
    header('Location: index.php');
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];

  $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ? WHERE id = ?");
  $stmt->bind_param("ssdi", $name, $description, $price, $service_id);
  $stmt->execute();

  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Service</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
<div class="items-center ml-64 justify-center min-h-screen">
<form method="POST" class="bg-white p-6 rounded-xl shadow-lg space-y-4 w-full max-w-lg">
  <h2 class="text-2xl font-bold text-center">Edit Service</h2>

  <input type="text" name="name" value="<?= $service['name'] ?>" placeholder="Service Name" class="w-full border p-2 rounded" required>
  <textarea name="description" placeholder="Description" class="w-full border p-2 rounded"><?= $service['description'] ?></textarea>
  <input type="number" name="price" value="<?= $service['price'] ?>" placeholder="Price" class="w-full border p-2 rounded" required step="0.01">

  <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Save Changes</button>

  <a href="index.php" class="block text-center text-blue-500 mt-2">Back to Services</a>
</form>
</div>
</main>
</body>
</html>
