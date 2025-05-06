<?php
require_once '../config/config.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

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
                header('Location: admin.php?tab=addauthor');
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
                header('Location: admin.php?tab=addauthor');
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
            header('Location: admin.php?tab=addauthor');
            exit();
        }
    }
}
?>

<div class="container mt-2">
    <h2>Lägg till Författare</h2>
    <div id="author-message-container"></div>
    <form id="add-author-form" onsubmit="return false;">
        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label for="first_name" class="form-label">Förnamn</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="col-md-4">
                <label for="last_name" class="form-label">Efternamn</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">Lägg till</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Förnamn</th>
                    <th>Efternamn</th>
                    <th width="150px">Åtgärder</th>
                </tr>
            </thead>
            <tbody id="authors-list">
                <?php
                try {
                    $stmt = $pdo->query("SELECT author_id, first_name, last_name FROM author");
                    while ($row = $stmt->fetch()) {
                        echo "<tr>
                                <td>{$row['author_id']}</td>
                                <td>" . htmlspecialchars($row['first_name']) . "</td>
                                <td>" . htmlspecialchars($row['last_name']) . "</td>
                                <td>
                                <a href=\"#\" class=\"edit-item btn btn-warning btn-sm\" 
                                    data-id=\"" . $row['author_id'] . "\" 
                                    data-type=\"author\" 
                                    data-first-name=\"" . htmlspecialchars($row['first_name']) . "\" 
                                    data-last-name=\"" . htmlspecialchars($row['last_name']) . "\">Redigera</a>
                                <a href=\"javascript:void(0);\" class=\"btn btn-danger btn-sm delete-item\" 
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