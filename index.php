<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    header("Location: dashboard.php");
    exit;
  } else {
    echo "Invalid login!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login • InvoicePro</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<form method="POST" class="max-w-md mx-auto mt-20 bg-white shadow-md rounded-xl p-6 space-y-4">
  <h2 class="text-2xl font-bold text-center">Login to InvoicePro</h2>

  <input type="email" name="email" placeholder="Email"
    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />

  <input type="password" name="password" placeholder="Password"
    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />

  <button type="submit"
    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Login</button>

  <p class="text-center text-sm">Don't have an account?
    <a href="register.php" class="text-blue-600 hover:underline">Register</a>
  </p>
</form>

</body>
</html>

