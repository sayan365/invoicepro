<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $gstin = $conn->real_escape_string($_POST['gstin']);
    $bank_details = $conn->real_escape_string($_POST['bank_details']);
    
    // Handle logo upload
    if (!empty($_FILES['company_logo']['name'])) {
        $logo_name = time() . '_' . basename($_FILES['company_logo']['name']);
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . $logo_name;
        move_uploaded_file($_FILES['company_logo']['tmp_name'], $target_file);
        
        $conn->query("UPDATE users SET 
            company_name='$company_name',
            company_logo='$target_file',
            address='$address',
            phone='$phone',
            gstin='$gstin',
            bank_details='$bank_details'
            WHERE id = $user_id
        ");
    } else {
        $conn->query("UPDATE users SET 
            company_name='$company_name',
            address='$address',
            phone='$phone',
            gstin='$gstin',
            bank_details='$bank_details'
            WHERE id = $user_id
        ");
    }

    $_SESSION['success'] = "Profile updated successfully!";
    header('Location: profile.php');
    exit;
}

// Fetch user details
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 ">
<aside class="w-64 bg-white shadow-lg h-screen fixed">
  <div class="p-6 font-bold text-lg border-b">InvoicePro</div>
  <nav class="p-4 space-y-4">
    <a href="dashboard.php"      class="block hover:bg-gray-200 p-2 rounded">🏠 Dashboard</a>
    <a href="clients/index.php"  class="block hover:bg-gray-200 p-2 rounded">👤 Clients</a>
    <a href="services/index.php" class="block hover:bg-gray-200 p-2 rounded">🛠️ Services</a>
    <a href="invoices/index.php" class="block hover:bg-gray-200 p-2 rounded">🧾 Invoices</a>
    <a href="profile.php"        class="block hover:bg-gray-200 p-2 rounded">🤵 Profile</a>
    <a href="logout.php"         class="block text-red-500 hover:bg-red-100 p-2 rounded">🚪 Logout</a>
  </nav>
</aside>


<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-6">Profile Settings</h2>

    <?php if (isset($_SESSION['success'])) { ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">

        <div>
            <label class="block mb-1 font-semibold">Company Name</label>
            <input type="text" name="company_name" value="<?= htmlspecialchars($user['company_name']) ?>" class="w-full p-2 border rounded">
        </div>

        <div>
            <label class="block mb-1 font-semibold">Company Logo</label>
            <?php if (!empty($user['company_logo'])) { ?>
                <img src="<?= $user['company_logo'] ?>" alt="Company Logo" class="h-16 mb-2">
            <?php } ?>
            <input type="file" name="company_logo" class="w-full p-2 border rounded">
        </div>

        <div>
            <label class="block mb-1 font-semibold">Address</label>
            <textarea name="address" class="w-full p-2 border rounded"><?= htmlspecialchars($user['address']) ?></textarea>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="w-full p-2 border rounded">
        </div>

        <div>
            <label class="block mb-1 font-semibold">GSTIN (Optional)</label>
            <input type="text" name="gstin" value="<?= htmlspecialchars($user['gstin']) ?>" class="w-full p-2 border rounded">
        </div>

        <div>
            <label class="block mb-1 font-semibold">Bank Details (Optional)</label>
            <textarea name="bank_details" class="w-full p-2 border rounded"><?= htmlspecialchars($user['bank_details']) ?></textarea>
        </div>

        <div class="pt-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Profile</button>
        </div>

    </form>
</div>
</main>
</body>
</html>
