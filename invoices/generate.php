<?php
require_once('../includes/db.php');
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// Fetch clients and services
$user_id = $_SESSION['user']['id'];

$clients_result = $conn->query("SELECT * FROM clients WHERE user_id = $user_id");
$services_result = $conn->query("SELECT * FROM services WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class=" flex bg-gray-100 ">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
    <div class="max-w-2xl mx-auto bg-white shadow p-6 rounded">
        <h1 class="text-2xl font-bold mb-4">Generate Invoice</h1>

        <form action="generate_invoice.php" method="post">
            <label class="block mb-2 font-medium">Select Client:</label>
            <select name="client_id" required class="w-full mb-4 border p-2 rounded">
                <?php while ($client = $clients_result->fetch_assoc()): ?>
                    <option value="<?= $client['id'] ?>"><?= $client['name'] ?> (<?= $client['email'] ?>)</option>
                <?php endwhile; ?>
            </select>

            <label class="block mb-2 font-medium">Select Services:</label>
            <div class="space-y-2 mb-4">
                <?php while ($service = $services_result->fetch_assoc()): ?>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="services[]" value="<?= $service['id'] ?>" class="accent-blue-600">
                        <span><?= $service['name'] ?> - ₹<?= number_format($service['price'], 2) ?></span>
                    </label>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Generate Invoice</button>
        </form>
    </div>
    </main>
</body>
</html>
