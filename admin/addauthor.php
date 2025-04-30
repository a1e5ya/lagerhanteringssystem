<?php
require_once '../config/config.php';

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = trim($_POST['first_name']);
  $last_name = trim($_POST['last_name']);
  
  if ($first_name && $last_name) {
      try {
          // Prepare the SQL statement
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
          if ($isAjax) {
              header('Content-Type: application/json');
              echo json_encode(['success' => false, 'message' => 'Fel vid databasinmatning: ' . $e->getMessage()]);
              exit();
          } else {
              $_SESSION['error_message'] = "Fel vid databasinmatning: " . $e->getMessage();
              header('Location: admin.php?tab=addauthor');
              exit();
          }
      }
  } else {
      if ($isAjax) {
          header('Content-Type: application/json');
          echo json_encode(['success' => false, 'message' => 'Vänligen fyll i båda fälten.']);
          exit();
      } else {
          $_SESSION['error_message'] = "Vänligen fyll i båda fälten.";
          header('Location: admin.php?tab=addauthor');
          exit();
      }
  }
}

?>

<div class="container mt-2">
    <h2>Lägg till Författare</h2>
    <div id="author-message-container"></div>
    <form id="add-author-form">
        <div class="form-group mb-3">
            <label for="first_name">Förnamn</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="form-group mb-3">
            <label for="last_name">Efternamn</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Lägg till</button>
    </form>
</div>