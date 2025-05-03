<?php
$invoiceDir = __DIR__ . '/';
$invoice = isset($_GET['file']) ? basename($_GET['file']) : '';
$filePath = $invoiceDir . $invoice;

if (!file_exists($filePath) || pathinfo($invoice, PATHINFO_EXTENSION) !== 'pdf') {
    echo "<p>⚠️ Invoice not found or invalid file type.</p>";
    exit;
}

// Extract metadata
$created = date("Y-m-d H:i:s", filemtime($filePath));
$client = 'Unknown';
if (strpos($invoice, '_') !== false) {
    $parts = explode('_', $invoice);
    $last = end($parts);
    $client = pathinfo($last, PATHINFO_FILENAME);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 10px; }
        .info-box { margin-bottom: 20px; }
        .label { font-weight: bold; }
        .btn {
            padding: 8px 12px;
            margin-right: 10px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
        }
        .btn:hover { background-color: #0056b3; }
        .danger { background-color: #dc3545; }
        .danger:hover { background-color: #a71d2a; }
        .pdf-preview {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
<h1>Invoice Details</h1>

<div class="info-box">
    <p><span class="label">Filename:</span> <?= htmlspecialchars($invoice) ?></p>
    <p><span class="label">Client:</span> <?= htmlspecialchars($client) ?></p>
    <p><span class="label">Created On:</span> <?= $created ?></p>

    <a class="btn" href="<?= htmlspecialchars($invoice) ?>" download>⬇ Download PDF</a>
    <a class="btn danger" href="index.php?delete=<?= urlencode($invoice) ?>" onclick="return confirm('Are you sure you want to delete this invoice?')">🗑 Delete</a>
    <a class="btn" href="index.php">← Back to Invoice List</a>
</div>

<embed class="pdf-preview" src="<?= htmlspecialchars($invoice) ?>" type="application/pdf">
</main>
</body>
</html>
