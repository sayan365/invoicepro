<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user'])) {
  header('Location: ../index.php');
  exit;
}

$user_id = $_SESSION['user']['id'];
$search = $_GET['search'] ?? '';

// Filter query based on search
$stmt = $conn->prepare("SELECT * FROM services WHERE user_id = ? AND name LIKE ?");
$like = "%$search%";
$stmt->bind_param("is", $user_id, $like);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Service List</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
      <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">Your Services</h2>
          <!-- Search Form -->
          <a href="add.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Service</a>
      </div>

      <form method="GET"  class="mb-4 flex">
          <input type="text" name="search" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring focus:border-blue-300">
          <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">Search</button>
      </form>

    <!-- Services Table -->
    <table class="w-full text-left border border-gray-200">
      <thead>
        <tr class="bg-gray-200 text-gray-700">
          <th class="p-2 border">Name</th>
          <th class="p-2 border">Price</th>
          <th class="p-2 border">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr class="hover:bg-gray-50">
              <td class="p-2 border"><?= htmlspecialchars($row['name']) ?></td>
              <td class="p-2 border">₹<?= number_format($row['price'], 2) ?></td>
              <td class="p-2 border space-x-2">
                <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline"
                  onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
              </td>
            </tr>
          <?php } ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="p-4 text-center text-gray-500">No services found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
