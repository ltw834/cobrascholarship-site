<?php
declare(strict_types=1);
session_start();

// Set session timeout to 30 minutes
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}

// Check authentication
if (empty($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Read CSV file
$csvFile = __DIR__ . '/../backups/applications.csv';
$applications = [];
$headers = [];
$totalRows = 0;

if (file_exists($csvFile)) {
    if (($handle = fopen($csvFile, 'r')) !== false) {
        $headers = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            $applications[] = $data;
            $totalRows++;
        }
        fclose($handle);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cobra Scholarship</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="admin-header">
        <h1>Cobra Scholarship Admin</h1>
        <nav>
            <a href="/admin/logout.php" class="logout-btn">Logout</a>
        </nav>
    </header>

    <main class="admin-main">
        <div class="stats">
            <h2>Applications Overview</h2>
            <p>Total applications: <strong><?php echo $totalRows; ?></strong></p>
            <div class="actions">
                <a href="/backups/applications.csv" download class="download-btn">Download CSV</a>
            </div>
        </div>

        <?php if (!empty($applications)): ?>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search applications...">
        </div>

        <div class="table-container">
            <table id="applicationsTable">
                <thead>
                    <tr>
                        <?php foreach ($headers as $header): ?>
                            <th><?php echo htmlspecialchars($header); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?php echo htmlspecialchars($cell); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-data">
            <p>No applications found.</p>
        </div>
        <?php endif; ?>
    </main>

    <script>
        // Simple client-side search
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('applicationsTable');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Make table headers clickable for basic sorting
        document.querySelectorAll('#applicationsTable th').forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => sortTable(index));
        });

        function sortTable(columnIndex) {
            const table = document.getElementById('applicationsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim();
                const bText = b.cells[columnIndex].textContent.trim();
                return aText.localeCompare(bText);
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
    </script>
</body>
</html>