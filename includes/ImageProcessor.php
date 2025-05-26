<?php
// ... (ImageProcessor.php code from previous responses, no changes needed to its PHP logic for paths) ...
// The key line is: $this->uploadDir = __DIR__ . '/../' . rtrim($app_config['uploads']['product_images_path'], '/') . '/';
// This correctly resolves from 'includes/' to 'lagerhanteringssystem/' and then appends 'assets/images/'.
/**
 * ImageProcessor
 *
 * This class handles the uploading, validation, and storage of images
 * for products, and manages the image paths in the database.
 *
 * @package Bookstore
 * @author System Administrator
 * @version 1.0
 */
class ImageProcessor {
    private $pdo;
    private $uploadDir;
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 5 * 1024 * 1024; // 5 MB

    public function __construct(PDO $pdo, string $uploadDir = '../assets/images/') {
        $this->pdo = $pdo;
        $this->uploadDir = rtrim($uploadDir, '/') . '/'; // Ensure trailing slash

        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Uploads multiple images, saves their paths to the database, and returns success/error.
     *
     * @param array $files The $_FILES array for the image input.
     * @param int $productId The ID of the product to associate images with.
     * @return array An array containing 'success' (boolean) and 'message' (string) or 'errors' (array).
     */
    public function uploadProductImages(array $files, int $productId): array {
        if (empty($files['name'][0])) {
            return ['success' => true, 'message' => 'No images provided.'];
        }

        $uploadedCount = 0;
        $errors = [];

        // Loop through each uploaded file
        foreach ($files['name'] as $index => $fileName) {
            $fileTmpName = $files['tmp_name'][$index];
            $fileSize = $files['size'][$index];
            $fileError = $files['error'][$index];

            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = "Error uploading file '{$fileName}': " . $this->getFileUploadErrorMessage($fileError);
                continue;
            }

            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file type and size
            if (!in_array($fileExt, $this->allowedTypes)) {
                $errors[] = "Invalid file type for '{$fileName}'. Only JPG, JPEG, PNG, GIF, WEBP are allowed.";
                continue;
            }
            if ($fileSize > $this->maxFileSize) {
                $errors[] = "File '{$fileName}' is too large. Maximum size is " . ($this->maxFileSize / (1024 * 1024)) . " MB.";
                continue;
            }

            // Generate a unique file name to prevent overwrites
            $newFileName = uniqid('prod_img_', true) . '.' . $fileExt;
            $destination = $this->uploadDir . $newFileName;
            $imagePathForDb = 'assets/images/' . $newFileName; // Path to store in DB

            if (move_uploaded_file($fileTmpName, $destination)) {
                // Save image path to database
                if ($this->saveImagePathToDatabase($productId, $imagePathForDb)) {
                    $uploadedCount++;
                } else {
                    $errors[] = "Failed to save image path for '{$fileName}' to database.";
                    // Optionally delete the file if DB save fails
                    unlink($destination);
                }
            } else {
                $errors[] = "Failed to move uploaded file '{$fileName}' to destination.";
            }
        }

        if (empty($errors) && $uploadedCount > 0) {
            return ['success' => true, 'message' => "Successfully uploaded {$uploadedCount} image(s)."];
        } elseif (empty($errors) && $uploadedCount === 0) {
            return ['success' => true, 'message' => "No valid images were uploaded."];
        } else {
            return ['success' => false, 'message' => "Some images failed to upload.", 'errors' => $errors];
        }
    }

    /**
     * Saves the image path to the 'image' table in the database.
     *
     * @param int $productId
     * @param string $imagePath
     * @return bool True on success, false on failure.
     */
    private function saveImagePathToDatabase(int $productId, string $imagePath): bool {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO image (prod_id, image_path) VALUES (?, ?)");
            return $stmt->execute([$productId, $imagePath]);
        } catch (PDOException $e) {
            error_log("Database error saving image path: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves an error message for file upload errors.
     *
     * @param int $code The error code from $_FILES['error'].
     * @return string The error message.
     */
    private function getFileUploadErrorMessage(int $code): string {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
            case UPLOAD_ERR_FORM_SIZE:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
            case UPLOAD_ERR_PARTIAL:
                return "The uploaded file was only partially uploaded.";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk.";
            case UPLOAD_ERR_EXTENSION:
                return "A PHP extension stopped the file upload.";
            default:
                return "Unknown upload error.";
        }
    }
}