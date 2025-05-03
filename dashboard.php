<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InvoicePro Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body {
    font-family: 'Inter', sans-serif;
  }
</style>
</head>
<body class="flex h-screen bg-gray-100 text-gray-800">

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-lg">
    <div class="p-6 font-bold text-lg border-b">InvoicePro</div>
    <nav class="p-4 space-y-4">
      <a href="dashboard.php" class="block hover:bg-gray-200 p-2 rounded">🏠 Dashboard</a>
      <a href="clients/index.php" class="block hover:bg-gray-200 p-2 rounded">👤 Clients</a>
      <a href="services/index.php" class="block hover:bg-gray-200 p-2 rounded">🛠️ Services</a>
      <a href="invoices/index.php" class="block hover:bg-gray-200 p-2 rounded">🧾 Invoices</a>
      <a href="profile.php" class="block hover:bg-gray-200 p-2 rounded">🤵 Profile</a>
      <a href="logout.php" class="block text-red-500 hover:bg-red-100 p-2 rounded">🚪 Logout</a>
    </nav>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-6">
    <h1 class="text-2xl font-bold mb-4">Welcome, <?= $_SESSION['user']['name'] ?>!</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Clients</h2>
        <p class="text-sm text-gray-600 mt-2">Manage and add clients.</p>
        <a href="clients/index.php" class="text-blue-500 hover:underline text-sm">View Clients →</a>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Services</h2>
        <p class="text-sm text-gray-600 mt-2">Set pricing and service details.</p>
        <a href="services/index.php" class="text-blue-500 hover:underline text-sm">View Services →</a>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Invoices</h2>
        <p class="text-sm text-gray-600 mt-2">Create and manage invoices.</p>
        <a href="invoices/index.php" class="text-blue-500 hover:underline text-sm">View Invoices →</a>
      </div>

    </div>
  </main>
</body>
</html>
