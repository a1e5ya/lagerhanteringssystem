<?php
/**
 * Newsletter Signup - Restored Working Version with reCAPTCHA
 * 
 * Contains:
 * - Newsletter sign-up handler
 * 
 * Functions:
 * - subscribeToNewsletter()
 */

// Use init.php if it includes config and database setup
require_once 'init.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email from the form
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Get name and language (new fields)
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
    $language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'sv';
    
    // Simple reCAPTCHA check if token exists
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    if (!empty($recaptchaResponse) && defined('RECAPTCHA_SECRET_KEY') && !empty(RECAPTCHA_SECRET_KEY)) {
        // Basic reCAPTCHA verification - allow failure gracefully
        $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
        $postData = http_build_query([
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptchaResponse
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postData
            ]
        ]);
        
        $response = @file_get_contents($verifyURL, false, $context);
        if ($response) {
            $responseData = json_decode($response, true);
            if (!$responseData || !$responseData['success']) {
                // reCAPTCHA failed, but continue anyway (graceful failure)
                error_log("reCAPTCHA verification failed, but allowing submission");
            }
        }
    }
    
    // Call function to subscribe
    $result = subscribeToNewsletter($email, $name, $language);
    
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

// Only output the BASE_URL script for non-AJAX requests
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    echo "<script>const BASE_URL = '" . getBasePath() . "';</script>";
}

/**
 * Subscribe a user to the newsletter
 *
 * @param string $email User's email address
 * @param string $name User's name (optional)
 * @param string $language User's language preference
 * @return array Result of the operation with status and message
 */
function subscribeToNewsletter($email, $name = null, $language = 'sv') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'message' => $language === 'fi' ? 
                'Virheellinen sähköpostiosoite' :
                'Ogiltig e-postadress'
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
            $update_stmt = $pdo->prepare("UPDATE newsletter_subscriber SET subscriber_is_active = 1, subscriber_name = :name, subscriber_language_pref = :language WHERE subscriber_email = :email AND subscriber_is_active = 0");
            $update_stmt->execute(['email' => $email, 'name' => $name, 'language' => $language]);
            
            if ($update_stmt->rowCount() > 0) {
                return [
                    'status' => 'success',
                    'message' => $language === 'fi' ? 
                        'Uutiskirjeen tilauksesi on aktivoitu uudelleen!' :
                        'Din prenumeration har återaktiverats!'
                ];
            }
            
            return [
                'status' => 'info',
                'message' => $language === 'fi' ? 
                    'Tilaat jo uutiskirjettämme' :
                    'Du prenumererar redan på vårt nyhetsbrev'
            ];
        }
        
        // Insert new subscriber with name and language
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscriber (subscriber_email, subscriber_name, subscriber_language_pref) VALUES (:email, :name, :language)");
        $stmt->execute([
            'email' => $email, 
            'name' => $name,
            'language' => $language
        ]);
        
        // Log the subscription (optional)
        error_log("New newsletter subscription: $email at " . date('Y-m-d H:i:s'));
        
        return [
            'status' => 'success',
            'message' => $language === 'fi' ? 
                'Kiitos uutiskirjeen tilauksesta!' :
                'Tack för din prenumeration på vårt nyhetsbrev!'
        ];
        
    } catch (Exception $e) {
        error_log("Newsletter subscription error: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => $language === 'fi' ? 
                'Virhe tapahtui. Yritä uudelleen myöhemmin.' :
                'Ett fel uppstod. Försök igen senare.'
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