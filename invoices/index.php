<?php
// 1) REQUIRE LOGIN + DB CONNECTION
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user'])) {
  header('Location: ../index.php');
  exit;
}

$user_id = $_SESSION['user']['id'];

// require_once __DIR__ . '../includes/db.php';  // $conn = new mysqli(...)

// ------------------------------------------------------------------

// 2) SCAN FOLDER
$invoiceDir = __DIR__ . '/';
$files = array_diff(scandir($invoiceDir), ['.', '..']);

// Filter PDF files
$invoices = array_filter($files, function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
});

// 3) FILTER TO JUST THIS USER’S PDFs
$allowed = [];
$stmt = $conn->prepare("SELECT pdf_path FROM invoices WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // store just the basename (so it matches the scanned filename)
    $allowed[] = basename($row['pdf_path']);
}
$stmt->close();

// intersect with scanned files
$invoices = array_filter($invoices, function($file) use ($allowed) {
    return in_array($file, $allowed, true);
});

// Sort by most recent (descending filemtime)
usort($invoices, function($a, $b) use ($invoiceDir) {
    return filemtime($invoiceDir . $b) - filemtime($invoiceDir . $a);
});

// ------------------------------------------------------------------

// 4) HANDLE SEARCH
$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
if ($search !== '') {
    $invoices = array_filter($invoices, function($file) use ($search) {
        return strpos(strtolower($file), $search) !== false;
    });
}

// 5) HANDLE DELETE (file **and** DB record)
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $filePath = $invoiceDir . $fileToDelete;
    if (file_exists($filePath)) {
        unlink($filePath);

        // remove the DB row for this user + file
        $d = $conn->prepare("
            DELETE FROM invoices
             WHERE user_id = ?
               AND pdf_path LIKE ?
        ");
        // use a wildcard in case you store subfolders in pdf_path
        $like = "%$fileToDelete";
        $d->bind_param('is', $user_id, $like);
        $d->execute();
        $d->close();
    }
    header("Location: index.php");
    exit;
}

// 6) PAGINATION
$perPage = 10;
$total   = count($invoices);
$page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start   = ($page - 1) * $perPage;
$pagedInvoices = array_slice($invoices, $start, $perPage);
$totalPages    = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Invoices</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h1 { margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        input[type="text"] { padding: 6px; width: 200px; border: solid #b5b5b5 }
        button, .btn {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover { background-color: #0056b3; }
        .pagination { margin-top: 20px; }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 6px 10px;
            background: #ddd;
            border-radius: 4px;
        }
        .pagination a.active { background: #007bff; color: white; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">

<div class="top-bar">
    <h1><b>Invoices</b></h1>
    <a href="generate.php" class="btn">➕ Create New Invoice</a>
</div>

<form method="get" style="margin-top: 10px;">
    <input type="text" name="search" placeholder="Search invoices..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn">Search</button>
</form>

<?php if (empty($pagedInvoices)): ?>
    <p>No invoices found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Filename</th>
                <th>Created On</th>
                <th>View</th>
                <th>Download</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pagedInvoices as $invoice): ?>
            <?php
            $created = date("Y-m-d H:i:s", filemtime($invoiceDir . $invoice));
            $client = 'Unknown';
            if (strpos($invoice, '_') !== false) {
                $parts = explode('_', $invoice);
                $last = end($parts);
                $client = pathinfo($last, PATHINFO_FILENAME);
            }
            ?>
            <tr>
                <td><?= htmlspecialchars($client) ?></td>
                <td><?= htmlspecialchars($invoice) ?></td>
                <td><?= $created ?></td>
                <td><a class="btn" href="view_invoice.php?file=<?= urlencode($invoice) ?>">👁 View</a></td>
                <td><a class="btn" href="<?= htmlspecialchars($invoice) ?>" download>⬇ Download</a></td>
                <td>
                    <a class="btn" href="?delete=<?= urlencode($invoice) ?>"
                       onclick="return confirm('Delete this invoice?')">🗑 Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(['search'=>$search,'page'=>$i]) ?>"
               class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
</main>
</body>
</html>
