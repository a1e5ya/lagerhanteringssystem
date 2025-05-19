<?php
/**
 * Newsletter Signup
 * 
 * Contains:
 * - Newsletter sign-up handler
 * 
 * Functions:
 * - subscribeToNewsletter()
 */

// Use init.php if it includes config and database setup
require_once 'init.php';

// Define BASE_URL for JavaScript usage
echo "<script>const BASE_URL = '" . getBasePath() . "';</script>";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email from the form
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Optional: get name if your form includes it
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    
    // Call function to subscribe
    $result = subscribeToNewsletter($email, $name);
    
    // Handle AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    // Redirect back to the referring page with a status parameter
    $redirect_url = $_SERVER['HTTP_REFERER'] ?? url('index.php');
    $redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . 'newsletter=' . $result['status'];
    
    header("Location: $redirect_url");
    exit;
}

/**
 * Subscribe a user to the newsletter
 *
 * @param string $email User's email address
 * @param string $name User's name (optional)
 * @return array Result of the operation with status and message
 */
function subscribeToNewsletter($email, $name = null) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'message' => 'Ogiltig e-postadress'
        ];
    }

    try {
        global $pdo;
        
        // Check if email already exists
        $check_stmt = $pdo->prepare("SELECT subscriber_id FROM newsletter_subscriber WHERE subscriber_email = :email");
        $check_stmt->execute(['email' => $email]);
        
        if ($check_stmt->rowCount() > 0) {
            $subscriber = $check_stmt->fetch();
            
            // Reactivate unsubscribed users
            $update_stmt = $pdo->prepare("UPDATE newsletter_subscriber SET subscriber_is_active = 1 WHERE subscriber_email = :email AND subscriber_is_active = 0");
            $update_stmt->execute(['email' => $email]);
            
            if ($update_stmt->rowCount() > 0) {
                return [
                    'status' => 'success',
                    'message' => 'Din prenumeration har återaktiverats!'
                ];
            }
            
            return [
                'status' => 'info',
                'message' => 'Du prenumererar redan på vårt nyhetsbrev'
            ];
        }
        
        // Insert new subscriber
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscriber (subscriber_email, subscriber_name) VALUES (:email, :name)");
        $stmt->execute([
            'email' => $email, 
            'name' => $name
        ]);
        
        // Log the subscription (optional)
        error_log("New newsletter subscription: $email at " . date('Y-m-d H:i:s'));
        
        return [
            'status' => 'success',
            'message' => 'Tack för din prenumeration på vårt nyhetsbrev!'
        ];
        
    } catch (Exception $e) {
        error_log("Newsletter subscription error: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Ett fel uppstod. Försök igen senare.'
        ];
    }
}

/**
 * Unsubscribe a user from the newsletter
 * 
 * @param string $email User's email address
 * @return array Result of the operation with status and message
 */
function unsubscribeFromNewsletter($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'message' => 'Ogiltig e-postadress'
        ];
    }
    
    try {
        global $pdo;
        
        $stmt = $pdo->prepare("UPDATE newsletter_subscriber SET subscriber_is_active = 0 WHERE subscriber_email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'status' => 'success',
                'message' => 'Du har avregistrerat dig från vårt nyhetsbrev'
            ];
        } else {
            return [
                'status' => 'info',
                'message' => 'E-postadressen hittades inte i vår prenumerationslista'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Newsletter unsubscription error: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Ett fel uppstod. Försök igen senare.'
        ];
    }
}
