<?php
// invoices/generate.php
require_once(__DIR__ . '/../tcpdf/tcpdf.php');
include(__DIR__ . '/../includes/db.php');
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');  // Set it to Indian time or your preferred timezone

// 1) Validate Input
if (empty($_POST['client_id']) || empty($_POST['services'] ?? [])) {
    die("<p class='p-4 bg-red-100 text-red-700'>Please select a client and at least one service.</p>");
}
$user_id     = $_SESSION['user']['id'];
$client_id   = intval($_POST['client_id']);
$service_ids = array_map('intval', $_POST['services']);

// 2) Fetch Profile (your company info)
$prof = $conn->query("SELECT company_name, company_logo, address AS comp_address, phone AS comp_phone,
                      gstin, bank_details 
                      FROM users 
                      WHERE id = $user_id")
             ->fetch_assoc();

// 3) Fetch Client
$client = $conn->query("SELECT name AS cli_name, company AS cli_company, email AS cli_email, 
                               phone AS cli_phone, address AS cli_address
                        FROM clients 
                        WHERE id = $client_id 
                          AND user_id = $user_id")
               ->fetch_assoc();
if (!$client) die("<p>Client not found!</p>");

// 4) Fetch Services & Compute Total
$in  = implode(',', $service_ids);
$res = $conn->query("SELECT name, description, price FROM services 
                     WHERE id IN ($in) AND user_id = $user_id");
$services = [];
$total = 0;
while ($r = $res->fetch_assoc()) {
    $services[] = $r;
    $total     += $r['price'];
}

// 5) Invoice Number & File Paths
$invoice_number = 'INV-' . date('Ymd') . '-' . date('His');
$filename       = "invoice_{$invoice_number}_{$client['cli_name']}.pdf";
$save_path      = __DIR__ . '/' . $filename;
$web_path       = 'invoices/' . $filename;

// 6) Build PDF
$pdf = new TCPDF('P','mm','A4',true,'UTF-8',false);
$pdf->SetCreator('InvoicePro');
$pdf->SetTitle($invoice_number);
$pdf->SetFont('helvetica','',10);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE,20);
$pdf->AddPage();

// --- HEADER: Logo + Company Info + INVOICE title ---
$logo = $prof['company_logo']
    ? realpath(__DIR__ . "/../".$prof['company_logo'])
    : '';
$html = '<table width="100%" cellpadding="4">
  <tr>
    <td width="60%">
      '.($logo && file_exists($logo)
        ? '<img src="'.$logo.'" width="100"><br>'
        : '').'
      <strong style="font-size:14pt;">'.htmlspecialchars($prof['company_name']).'</strong><br>
      <span style="font-size:9pt; color:#555;">'
        .nl2br(htmlspecialchars($prof['comp_address'])).'<br>
        Phone: '.htmlspecialchars($prof['comp_phone']).'<br>
        GSTIN: '.htmlspecialchars($prof['gstin']).'
      </span>
    </td>
    <td width="40%" style="text-align:right;">
      <span style="font-size:18pt; font-weight:bold;">INVOICE</span><br>
      <span style="font-size:10pt;"># '.$invoice_number.'</span><br>
      <span style="font-size:9pt; background:#f2f2f2; padding:4px; display:inline-block;">
        '.date('d M Y').'
      </span>
    </td>
  </tr>
</table>
<hr style="border:none; border-top:1px solid #ccc; margin:8px 0;">';

// --- CLIENT INFO BOX ---
$html .= '<table width="100%" cellpadding="6" style="background:#f9f9f9; border-radius:4px; font-size:10pt;">
  <tr>
    <td>
      <strong>Bill To:</strong><br>
      '.htmlspecialchars($client['cli_name']).'<br>
      '.htmlspecialchars($client['cli_company']).'<br>
      Address: '.htmlspecialchars($client['cli_address']).'<br>
      Email: '.htmlspecialchars($client['cli_email']).'<br>
      Phone: '.htmlspecialchars($client['cli_phone']).'
    </td>
  </tr>
</table>';

$pdf->writeHTML($html, false, false, false, '');

$pdf->SetFont('dejavusans', '', 10);

// --- SERVICES TABLE (dark header / two-column theme) ---
$tbl = '
<table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; font-size:10pt;">
  <thead>
    <tr style="background-color:gray; color:#ffffff;">
      <th width="60%" style="text-align:left; padding:8px;">Description</th>
      <th width="25%" style="text-align:left; padding:8px;"></th>
      <th width="15%" style="text-align:right; padding:8px;">Amount</th>
    </tr>
  </thead>
  <tbody>';
foreach ($services as $s) {
    // assume unit price = price, amount = price
    $unit  = number_format($s['price'], 2);
    $amount = number_format($s['price'], 2);
    $tbl .= '
    <tr style="border-bottom:1px solid #dddddd;">
      <td style="padding:6px 8px;">
        '.htmlspecialchars($s['name']).'<br>
        <small>'.htmlspecialchars($s['description']).'</small>
      </td>
      <td style="text-align:right; padding:6px 8px;"></td>
      <td style="text-align:right; padding:6px 8px;">₹'.$amount.'</td>
    </tr>';
}
$tbl .= '
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2" style="text-align:right; padding:8px; border-top:2px solid #333333;">Subtotal</td>
      <td style="text-align:right; padding:8px; border-top:2px solid #333333;">₹'.number_format($total, 2).'</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:right; padding:8px;">Sales Tax (18%)</td>
      <td style="text-align:right; padding:8px;">₹'.number_format($total * 0.18, 2).'</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:right; padding:8px; border-top:1px solid #333333;"><strong>Total (₹)</strong></td>
      <td style="text-align:right; padding:8px; border-top:1px solid #333333;"><strong>₹'.number_format($total * 1.18, 2).'</strong></td>
    </tr>
  </tfoot>
</table>';
$pdf->writeHTML($tbl, true, false, false, '');


// --- SIGNATURE LINE AND PAYMENT DETAILS FOOTER IN ONE ROW ---
$pdf->Ln(12);
$pdf->SetFont('dejavusans', '', 10);

// Combining signature and payment details into one row
$combinedRow = '<table width="100%" cellpadding="4" padding-top="6" cellspacing="0">
<tr><br/><br/>
<td width="40%" style="border-top:1px solid #333; text-align:center; font-size:10pt;">
Authorized Signature
</td>
<td width="60%" style="font-size:9pt; color:#555; line-height:1.4; text-align:right;">
<strong>Payment Details:</strong><br>' . nl2br(htmlspecialchars($prof['bank_details'])) . '
</td>
</tr>
</table>';

$pdf->writeHTML($combinedRow, false, false, false, '');
// --- THANK YOU NOTE ---
$pdf->Ln(6);
$pdf->SetFont('helvetica','I',10);
$pdf->Cell(0,0,'Thank you for your business!',0,1,'C');

// 7) Save PDF & Record in DB
$pdf->Output($save_path, 'F');

// prepare a string variable for bind_param
$services_str = implode(',', $service_ids);

$stmt = $conn->prepare(
  "INSERT INTO invoices 
     (user_id, invoice_number, client_id, services, total, pdf_path) 
   VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
  "isssds",
  $user_id,
  $invoice_number,
  $client_id,
  $services_str,
  $total,
  $web_path
);
$stmt->execute();

// 8) Show Success & Links
$mailto = "mailto:{$client['cli_email']}"
            . "?subject=" . urlencode("Invoice $invoice_number from {$prof['company_name']}")
            . "&body=" . urlencode("Hello {$client['cli_name']},\n\nPlease find attached Invoice $invoice_number.\nTotal: ₹" . number_format($total, 2) . "\n\nRegards,\n{$prof['company_name']}");
?>
<!DOCTYPE html>
<html><head>
  <meta charset="utf-8"><title>Invoice <?= $invoice_number ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
  .pdf-preview {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
        }
</style>
<body class="flex bg-gray-100 ">
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area: add a left margin = sidebar width -->
<main class="flex-1 ml-64 p-6">
  <div class="w-auto mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Invoice <?= $invoice_number ?> Created</h1>
    <a href="<?= $web_path ?>" download
       class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">⬇ Download PDF</a>
    <a href="<?= $mailto ?>" target="_blank"
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 ml-4">✉️ Email Invoice</a>
    <a class="inline-block bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 ml-4" href="index.php">← Back to Invoice List</a>
  </div>
  <embed class="pdf-preview" src="<?= htmlspecialchars($filename) ?>" type="application/pdf">
</main>
</body></html>
