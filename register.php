<?php include('includes/db.php'); ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $role = 'user'; // default

  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $password, $role);

  if ($stmt->execute()) {
    echo "Registration successful. <a href='index.php'>Login</a>";
  } else {
    echo "Error: " . $stmt->error;
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
  <h2 class="text-2xl font-bold text-center">Create an Account</h2>

  <input type="text" name="name" placeholder="Name"
    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />

  <input type="email" name="email" placeholder="Email"
    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />

  <input type="password" name="password" placeholder="Password"
    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />

  <button type="submit"
    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Register</button>

  <p class="text-center text-sm">Already have an account?
    <a href="index.php" class="text-blue-600 hover:underline">Login</a>
  </p>
</form>

</body>
</html>