<?php
require_once 'init.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Pagination settings
$records_per_page = 20;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Sorting parameters
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'author_id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Validate sort column to prevent SQL injection
$allowed_columns = ['author_id', 'first_name', 'last_name'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'author_id'; // Default to author_id if invalid column
}

// Validate sort order
if ($sort_order !== 'asc' && $sort_order !== 'desc') {
    $sort_order = 'asc'; // Default to ascending if invalid
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    if ($first_name && $last_name) {
        try {
            $stmt = $pdo->prepare("INSERT INTO author (first_name, last_name) VALUES (?, ?)");
            $stmt->execute([$first_name, $last_name]);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Författare tillagd i databasen!']);
                exit();
            } else {
                $_SESSION['message'] = "Författare tillagd i databasen!";
                header('Location: ' . url('admin.php', ['tab' => 'addauthor']));
                exit();
            }
        } catch (PDOException $e) {
            $msg = 'Fel vid databasinmatning: ' . $e->getMessage();
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit();
            } else {
                $_SESSION['error_message'] = $msg;
                header('Location: ' . url('admin.php', ['tab' => 'addauthor']));
                exit();
            }
        }
    } else {
        $msg = "Vänligen fyll i båda fälten.";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $msg]);
            exit();
        } else {
            $_SESSION['error_message'] = $msg;
            header('Location: ' . url('admin.php', ['tab' => 'addauthor']));
            exit();
        }
    }
}

// Get total record count for pagination
try {
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM author");
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);
} catch (PDOException $e) {
    $total_records = 0;
    $total_pages = 0;
}

// Helper function to generate sort links
function getSortLink($column, $currentSortColumn, $currentSortOrder)
{
    $newOrder = ($column === $currentSortColumn && $currentSortOrder === 'asc') ? 'desc' : 'asc';
    $icon = '';

    if ($column === $currentSortColumn) {
        $icon = ($currentSortOrder === 'asc') ?
            '<i class="bi bi-sort-alpha-down"></i>' :
            '<i class="bi bi-sort-alpha-up"></i>';
    }

    return "?tab=addauthor&sort={$column}&order={$newOrder}" .
        (isset($_GET['page']) ? "&page={$_GET['page']}" : "") .
        "\" class=\"sort-link\" data-column=\"{$column}\" data-order=\"{$newOrder}\">{$icon}";
}
?>

<div class="container mt-2">
    <div id="author-message-container"></div>
    <form id="add-author-form" onsubmit="return false;">
        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-5">
                <label for="first_name" class="form-label">Förnamn</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="col-md-5">
                <label for="last_name" class="form-label">Efternamn</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary mt-4 px-5">Lägg till</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th><a href="<?php echo getSortLink('author_id', $sort_column, $sort_order); ?>">ID</a></th>
                    <th><a href="<?php echo getSortLink('first_name', $sort_column, $sort_order); ?>">Förnamn</a></th>
                    <th><a href="<?php echo getSortLink('last_name', $sort_column, $sort_order); ?>">Efternamn</a></th>
                    <th width="150px">Åtgärder</th>
                </tr>
            </thead>
            <tbody id="authors-list">
                <?php
                try {
                    // query with ORDER BY for sorting and LIMIT/OFFSET for pagination
                    $query = "SELECT author_id, first_name, last_name FROM author 
                              ORDER BY {$sort_column} {$sort_order} 
                              LIMIT ? OFFSET ?";

                    $stmt = $pdo->prepare($query);
                    $stmt->bindValue(1, $records_per_page, PDO::PARAM_INT);
                    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
                    $stmt->execute();

                    while ($row = $stmt->fetch()) {
                        echo "<tr>
                                <td>{$row['author_id']}</td>
                                <td>" . htmlspecialchars($row['first_name']) . "</td>
                                <td>" . htmlspecialchars($row['last_name']) . "</td>
                                <td>
                                <a href=\"#\" class=\"edit-item btn btn-outline-primary btn-sm\" 
                                    data-id=\"" . $row['author_id'] . "\" 
                                    data-type=\"author\" 
                                    data-first-name=\"" . htmlspecialchars($row['first_name']) . "\" 
                                    data-last-name=\"" . htmlspecialchars($row['last_name']) . "\">Redigera</a>
                                <a href=\"javascript:void(0);\" class=\"btn btn-outline-danger btn-sm delete-item\" 
                                    data-id=\"" . $row['author_id'] . "\" 
                                    data-type=\"author\">Ta bort</a>
                                </td>
                            </tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='4'>Fel vid hämtning av författare: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination controls -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Författare sida navigation">
                <ul class="pagination justify-content-center">
                    <!-- Previous page link -->
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link pagination-link"
                            href="<?php echo ($page <= 1) ? '#' : "?tab=addauthor&page=" . ($page - 1) . "&sort={$sort_column}&order={$sort_order}"; ?>"
                            <?php echo ($page <= 1) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                            «
                        </a>
                    </li>

                    <!-- Page numbers -->
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);

                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link pagination-link" href="?tab=addauthor&page=1&sort=' . $sort_column . '&order=' . $sort_order . '">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        }
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                          <a class="page-link pagination-link" href="?tab=addauthor&page=' . $i . '&sort=' . $sort_column . '&order=' . $sort_order . '">' . $i . '</a>
                          </li>';
                    }

                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        }
                        echo '<li class="page-item"><a class="page-link pagination-link" href="?tab=addauthor&page=' . $total_pages . '&sort=' . $sort_column . '&order=' . $sort_order . '">' . $total_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next page link -->
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link pagination-link"
                            href="<?php echo ($page >= $total_pages) ? '#' : "?tab=addauthor&page=" . ($page + 1) . "&sort={$sort_column}&order={$sort_order}"; ?>">
                            »
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <div class="text-center mt-2">
            <small>Visar
                <?php echo min($total_records, $offset + 1); ?>-<?php echo min($total_records, $offset + $records_per_page); ?>
                av <?php echo $total_records; ?> författare |
                Sorterad efter <?php
                $column_names = ['author_id' => 'ID', 'first_name' => 'Förnamn', 'last_name' => 'Efternamn'];
                $order_names = ['asc' => 'stigande', 'desc' => 'fallande'];
                echo $column_names[$sort_column] . ' (' . $order_names[$sort_order] . ')';
                ?></small>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Redigera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Stäng"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden fields -->
                    <input type="hidden" id="edit-item-id">
                    <input type="hidden" id="edit-item-type">

                    <!-- Author fields -->
                    <div id="edit-author-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="edit-first-name" class="form-label">Förnamn</label>
                            <input type="text" class="form-control" id="edit-first-name">
                        </div>
                        <div class="mb-3">
                            <label for="edit-last-name" class="form-label">Efternamn</label>
                            <input type="text" class="form-control" id="edit-last-name">
                        </div>
                    </div>

                    <!-- Generic name field -->
                    <div id="edit-single-name-field">
                        <div class="mb-3">
                            <label for="edit-item-name" class="form-label">Namn</label>
                            <input type="text" class="form-control" id="edit-item-name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="button" class="btn btn-primary" id="save-edit">Spara ändringar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update pagination links to use AJAX instead of full page load
    $(document).ready(function () {
        // Handle both pagination links and sorting links with AJAX
        $(document).on('click', '.pagination-link, .sort-link', function (e) {
            e.preventDefault();

            const href = $(this).attr('href');
            if (href === '#') return; // Skip disabled links

            // Extract parameters from href
            const urlParams = new URLSearchParams(href.split('?')[1]);
            const page = urlParams.get('page') || 1;
            const sort = urlParams.get('sort') || 'author_id';
            const order = urlParams.get('order') || 'asc';

            // Load content via AJAX
            $.ajax({
                url: BASE_URL + '/admin/addauthor.php',
                data: {
                    page: page,
                    sort: sort,
                    order: order
                },
                success: function (response) {
                    // Replace only the table content
                    const $newContent = $(response);
                    $('.table-responsive').html($newContent.find('.table-responsive').html());

                    // Update URL without full page refresh
                    window.history.pushState(null, '', `?tab=addauthor&page=${page}&sort=${sort}&order=${order}`);
                },
                error: function () {
                    console.error('Failed to load page');
                }
            });
        });

        // Bootstrap Icons are loaded for sort indicators
        if (!$('link[href*="bootstrap-icons"]').length) {
            $('head').append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">');
        }
    });
    $(document).ready(function() {
    // Initialize the author form functionality
    initializeAddAuthor();
});
// Function to refresh the table after successful submission
function refreshAuthorsTable() {
  // Get current sort parameters and page
  const urlParams = new URLSearchParams(window.location.search);
  const page = urlParams.get('page') || 1;
  const sort = urlParams.get('sort') || 'author_id';
  const order = urlParams.get('order') || 'asc';
  
  // Fetch the updated table content
  $.ajax({
    url: BASE_URL + '/admin/addauthor.php',
    data: {
      page: page,
      sort: sort,
      order: order
    },
    success: function(response) {
      // Extract just the table content
      const $tempDiv = $('<div>').html(response);
      const tableContent = $tempDiv.find('#authors-list').html();
      
      // Update just the table body
      $('#authors-list').html(tableContent);
      
      console.log('Authors table refreshed');
    },
    error: function() {
      console.error('Failed to refresh table');
    }
  });
}
</script>