<?php
include('db.php');

// Handle status update if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $quotation_number = $_POST['quotation_number'];
    $new_status = $_POST['new_status'];

    // If changing to "sent" from "expired" or "draft", extend the validity
    if ($new_status == 'sent') {
        $new_valid_until = date('Y-m-d', strtotime('+14 days'));
        $stmt = $pdo->prepare("UPDATE pixel_media_quotations SET status = ?, valid_until = ? WHERE quotation_number = ?");
        $stmt->execute([$new_status, $new_valid_until, $quotation_number]);
    } else {
        $stmt = $pdo->prepare("UPDATE pixel_media_quotations SET status = ? WHERE quotation_number = ?");
        $stmt->execute([$new_status, $quotation_number]);
    }

    // Refresh the page to show updated status
    header("Location: quotation_history.php");
    exit;
}

// Fetch all quotations from the database
$stmt = $pdo->prepare("SELECT * FROM pixel_media_quotations ORDER BY quotation_date DESC");
$stmt->execute();
$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for expired quotations (valid_until < today)
$today = date('Y-m-d');
foreach ($quotations as &$quotation) {
    if ($quotation['valid_until'] < $today && $quotation['status'] != 'expired' && $quotation['status'] != 'accepted' && $quotation['status'] != 'rejected') {
        $stmt = $pdo->prepare("UPDATE pixel_media_quotations SET status = 'expired' WHERE quotation_number = ?");
        $stmt->execute([$quotation['quotation_number']]);
        $quotation['status'] = 'expired';
    }
}
unset($quotation); // Break the reference
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM pixel_media_quotations WHERE 
            quotation_number LIKE :search OR 
            company_name LIKE :search OR 
            phone LIKE :search OR 
            status LIKE :search OR 
            DATE(quotation_date) = :date_search
          ORDER BY quotation_date DESC";
$stmt = $pdo->prepare($query);
$searchTerm = '%' . $search . '%';
$dateSearch = $search;
$stmt->execute([
    ':search' => $searchTerm,
    ':date_search' => $dateSearch
]);
$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation History</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .status-draft {
            background-color: #6c757d;
            /* Gray */
        }

        .status-sent {
            background-color: #17a2b8;
            /* Cyan */
        }

        .status-accepted {
            background-color: #28a745;
            /* Green */
        }

        .status-rejected {
            background-color: #dc3545;
            /* Red */
        }

        .status-expired {
            background-color: #ffc107;
            /* Yellow */
            color: #212529;
            /* Dark text for better contrast */
        }

        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .status-dropdown {
            min-width: 120px;
            display: inline-block;
        }

        .status-form {
            display: inline-block;
            margin-left: 5px;
        }

        .status-cell {
            min-width: 200px;
        }

        .expired-row {
            background-color: #fff3cd;
            /* Light yellow background for expired */
        }

        .accepted-row {
            background-color: #d4edda;
            /* Light green background for accepted */
        }

        .rejected-row {
            background-color: #f8d7da;
            /* Light red background for rejected */
        }

        .draft-row {
            background-color: #e2e3e5;
            /* Light gray background for draft */
        }


        .status-container {
            display: inline-block;
            min-width: 220px;
        }

        .status-control-group {
            display: flex;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .status-control-group:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .status-select-wrapper {
            position: relative;
            flex-grow: 1;
        }

        .status-dropdown {
            width: 100%;
            padding: 8px 35px 8px 15px;
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 6px 0 0 6px;
            appearance: none;
            background-color: white;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            height: 38px;
            transition: border-color 0.3s;
        }

        .status-dropdown:focus {
            outline: none;
            border-color: #4d90fe;
            box-shadow: 0 0 0 2px rgba(77, 144, 254, 0.2);
        }

        .select-arrow {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #666;
            font-size: 12px;
        }

        .status-update-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            border-radius: 0 6px 6px 0;
        }

        .status-update-btn:hover {
            background-color: #3e8e41;
        }

        .status-update-btn i {
            margin-right: 6px;
        }

        .btn-text {
            display: inline-block;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .status-container {
                width: 100%;
            }

            .btn-text {
                display: none;
            }

            .status-update-btn {
                padding: 0 10px;
            }
        }

        /* Custom dropdown options with icons */
        .status-dropdown option {
            position: relative;
            padding-left: 25px;
        }

        .status-dropdown option:before {
            content: attr(data-icon);
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
        }

        h2 {
            color: #168955;
        }

        .thead-green {
            background-color: #168955;
            /* Bootstrap green */
            color: white;
            /* Text color */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <h2 class="my-4">Quotation History</h2>
        <form method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by Quotation ID, Date, Phone, Status, Company Name"
                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <button type="button" class="btn btn-secondary" onclick="resetSearch()">Reset</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered ">
                <thead class="thead-green">
                    <tr>
                        <th>Quotation #</th>
                        <th>Date</th>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th class="status-cell">Status</th>
                        <th>Valid Until</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotations as $quotation): ?>
                        <tr class="<?php
                        echo $quotation['status'] == 'expired' ? 'expired-row' : '';
                        echo $quotation['status'] == 'accepted' ? 'accepted-row' : '';
                        echo $quotation['status'] == 'rejected' ? 'rejected-row' : '';
                        echo $quotation['status'] == 'draft' ? 'draft-row' : '';
                        ?>">
                            <td><?php echo htmlspecialchars($quotation['quotation_number']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($quotation['quotation_date'])); ?></td>
                            <td><?php echo htmlspecialchars($quotation['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($quotation['contact_person']); ?></td>
                            <td><?php echo htmlspecialchars($quotation['phone']); ?></td>
                            <td>Rs. <?php echo number_format($quotation['total_amount'], 2); ?></td>
                            <td class="status-cell">
                                <span class="status-badge status-<?php echo $quotation['status']; ?>" style="margin: 3%;">
                                    <?php echo ucfirst($quotation['status']); ?>
                                </span>

                                <div class="status-container">
                                    <form method="post" class="status-form">
                                        <input type="hidden" name="quotation_number"
                                            value="<?php echo $quotation['quotation_number']; ?>">

                                        <div class="status-control-group">
                                            <div class="status-select-wrapper">
                                                <select name="new_status" class="form-select status-dropdown">
                                                    <?php if ($quotation['status'] == 'draft'): ?>
                                                        <option value="sent" data-icon="fa-paper-plane" class="text-info">Mark
                                                            as Sent</option>
                                                        <option value="accepted" data-icon="fa-check-circle"
                                                            class="text-success">Mark as Accepted</option>
                                                        <option value="rejected" data-icon="fa-times-circle"
                                                            class="text-danger">Mark as Rejected</option>
                                                    <?php elseif ($quotation['status'] == 'sent'): ?>
                                                        <option value="accepted" data-icon="fa-check-circle"
                                                            class="text-success">Accept Quotation </option>
                                                        <option value="rejected" data-icon="fa-times-circle"
                                                            class="text-danger">Reject Quotation</option>
                                                        <option value="draft" data-icon="fa-edit" class="text-secondary">Revert
                                                            to Draft</option>
                                                    <?php elseif ($quotation['status'] == 'accepted'): ?>
                                                        <option value="rejected" data-icon="fa-times-circle"
                                                            class="text-danger">Change to Rejected</option>
                                                        <option value="sent" data-icon="fa-paper-plane" class="text-info">Revert
                                                            to Sent</option>
                                                        <option value="draft" data-icon="fa-edit" class="text-secondary">Revert
                                                            to Draft</option>
                                                    <?php elseif ($quotation['status'] == 'rejected'): ?>
                                                        <option value="accepted" data-icon="fa-check-circle"
                                                            class="text-success">Accept Now</option>
                                                        <option value="sent" data-icon="fa-paper-plane" class="text-info">Revert
                                                            to Sent</option>
                                                        <option value="draft" data-icon="fa-edit" class="text-secondary">Revert
                                                            to Draft</option>
                                                    <?php elseif ($quotation['status'] == 'expired'): ?>
                                                        <option value="sent" data-icon="fa-sync-alt" class="text-info">Renew &
                                                            Mark as Sent</option>
                                                        <option value="draft" data-icon="fa-edit" class="text-secondary">Revert
                                                            to Draft</option>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="select-arrow">
                                                    <i class="fas fa-chevron-down"></i>
                                                </div>
                                            </div>
                                            <button type="submit" name="update_status" value="1" class="status-update-btn">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>



                                <script>
                                    $(document).ready(function () {
                                        // Enhance dropdown with custom functionality
                                        $('.status-dropdown').on('change focus', function () {
                                            $(this).css('border-color', '#4d90fe');
                                        }).on('blur', function () {
                                            $(this).css('border-color', '#ddd');
                                        });
                                    });
                                </script>
                            </td>
                            <td>
                                <?php echo $quotation['valid_until']; ?>
                                <?php if ($quotation['valid_until'] < $today && $quotation['status'] != 'accepted' && $quotation['status'] != 'rejected'): ?>
                                    <span class="badge badge-danger">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="view_quotation.php?quotation_number=<?php echo urlencode($quotation['quotation_number']); ?>"
                                    class="btn btn-sm btn-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="view_quotation.php?quotation_number=<?php echo urlencode($quotation['quotation_number']); ?>&print=true"
                                    class="btn btn-sm btn-success" title="Print" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if (in_array($quotation['status'], ['draft', 'expired'])): ?>
                                    <a href="edit_quotation.php?quotation_number=<?php echo urlencode($quotation['quotation_number']); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="delete_quotation.php?quotation_number=<?php echo urlencode($quotation['quotation_number']); ?>"
                                    class="btn btn-sm btn-danger" title="Delete"
                                    onclick="return confirm('Are you sure you want to delete this quotation?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script>
        function resetSearch() {
            // Clear the search input field
            document.querySelector('input[name="search"]').value = '';
            // Reload the page without the search parameter
            window.location.href = window.location.pathname;
        }
    </script>
    <script>
        $(document).ready(function () {
            // Auto-extend validity when renewing expired quotations or marking draft as sent
            $('select[name="new_status"]').on('change', function () {
                if ($(this).val() === 'sent') {
                    var validUntilCell = $(this).closest('tr').find('td:nth-child(8)');
                    var today = new Date();
                    var newDate = new Date();
                    newDate.setDate(today.getDate() + 14); // Add 14 days

                    var formattedDate = newDate.toISOString().split('T')[0];
                    validUntilCell.html(formattedDate + ' <span class="badge badge-success">Renewed</span>');
                }
            });
        });
    </script>

</body>

</html>