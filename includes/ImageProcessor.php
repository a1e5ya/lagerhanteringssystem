<?php
/**
 * ImageProcessor Class
 * 
 * Handles image upload, processing, and management for products
 * 
 * @package    KarisAntikvariat
 * @subpackage Classes
 */

class ImageProcessor {
    private $pdo;
    private $uploadPath;
    private $uploadUrl;
    private $maxSize;
    private $allowedExtensions;
    private $maxImagesPerProduct;

    /**
     * Constructor
     * 
     * @param PDO $pdo Database connection
     * @param array $config Upload configuration from app_config
     */
    public function __construct($pdo, $config = null) {
        global $app_config;
        
        $this->pdo = $pdo;
        
        // Use provided config or fall back to global config
        $uploadConfig = $config ?? $app_config['uploads'];
        
        $this->uploadPath = __DIR__ . '/../assets/uploads/products';
        $this->uploadUrl = 'assets/uploads/products';
        $this->maxSize = $uploadConfig['max_size'];
        $this->allowedExtensions = $uploadConfig['allowed_extensions'];
        $this->maxImagesPerProduct = $uploadConfig['max_images_per_product'] ?? 10;
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadPath)) {
            if (!mkdir($this->uploadPath, 0755, true)) {
                error_log("Failed to create upload directory: " . $this->uploadPath);
            }
        }
    }

    /**
     * Upload multiple product images
     * 
     * @param array $files $_FILES array for multiple files
     * @param int $productId Product ID
     * @return array Result with success flag and messages
     */
    public function uploadProductImages($files, $productId) {
        if (!$files || !isset($files['name']) || empty($files['name'][0])) {
            return [
                'success' => true,
                'message' => 'Inga bilder valda för uppladdning',
                'uploaded_count' => 0
            ];
        }

        $results = [];
        $errors = [];
        $uploadedCount = 0;
        
        // Check current image count
        $currentImageCount = $this->getProductImageCount($productId);
        
        // Process each uploaded file
        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            // Skip if no file uploaded in this slot
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            // Check if we've reached the limit
            if ($currentImageCount + $uploadedCount >= $this->maxImagesPerProduct) {
                $errors[] = "Maxgräns på {$this->maxImagesPerProduct} bilder per produkt uppnådd";
                break;
            }
            
            $fileData = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $result = $this->uploadSingleImage($fileData, $productId);
            
            if ($result['success']) {
                $uploadedCount++;
                $results[] = $result;
            } else {
                $errors[] = $result['message'];
            }
        }
        
        return [
            'success' => $uploadedCount > 0,
            'message' => $uploadedCount > 0 
                ? "{$uploadedCount} bilder uppladdade framgångsrikt" 
                : "Inga bilder kunde laddas upp",
            'uploaded_count' => $uploadedCount,
            'errors' => $errors,
            'results' => $results
        ];
    }

    /**
     * Upload a single image
     * 
     * @param array $file Single file data
     * @param int $productId Product ID
     * @return array Result with success flag and message
     */
    private function uploadSingleImage($file, $productId) {
        // Validate file
        $validation = $this->validateImage($file);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        try {
            $this->pdo->beginTransaction();

            // Generate unique filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $productId . '_' . uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadPath . '/' . $filename;
            $relativePath = $this->uploadUrl . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Kunde inte flytta uppladdad fil');
            }

            // Optimize image (reduce file size while maintaining quality)
            $this->optimizeImage($filepath, $extension);

            // Insert into image table (fixed to match actual database schema)
            $stmt = $this->pdo->prepare("
                INSERT INTO image (prod_id, image_path, image_uploaded_at) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$productId, $relativePath]);
            $imageId = $this->pdo->lastInsertId();

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Bild uppladdad framgångsrikt',
                'image_id' => $imageId,
                'filename' => $filename,
                'path' => $relativePath
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            
            // Clean up uploaded file if database operation failed
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            error_log("Image upload error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Fel vid bilduppladdning: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate uploaded image
     * 
     * @param array $file File data
     * @return array Validation result
     */
    private function validateImage($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Filen är för stor (server limit)',
                UPLOAD_ERR_FORM_SIZE => 'Filen är för stor (form limit)',
                UPLOAD_ERR_PARTIAL => 'Filen laddades endast delvis upp',
                UPLOAD_ERR_NO_FILE => 'Ingen fil valdes',
                UPLOAD_ERR_NO_TMP_DIR => 'Tillfällig mapp saknas',
                UPLOAD_ERR_CANT_WRITE => 'Kunde inte skriva fil till disk',
                UPLOAD_ERR_EXTENSION => 'Filuppladdning stoppades av en tillägg'
            ];
            
            return [
                'valid' => false,
                'message' => $errorMessages[$file['error']] ?? 'Okänt uppladdningsfel'
            ];
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            return [
                'valid' => false,
                'message' => 'Filen är för stor. Max storlek: ' . $this->formatBytes($this->maxSize)
            ];
        }

        // Check if file is actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'valid' => false,
                'message' => 'Filen är inte en giltig bild'
            ];
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return [
                'valid' => false,
                'message' => 'Filtyp inte tillåten. Tillåtna: ' . implode(', ', $this->allowedExtensions)
            ];
        }

        return ['valid' => true];
    }

    /**
     * Optimize uploaded image
     * 
     * @param string $filepath Full path to image file
     * @param string $extension File extension
     */
    private function optimizeImage($filepath, $extension) {
        // Get image dimensions
        list($width, $height) = getimagesize($filepath);
        
        // Only optimize if image is larger than 1200px in any dimension
        if ($width <= 1200 && $height <= 1200) {
            return;
        }

        // Calculate new dimensions (max 1200px, maintain aspect ratio)
        $maxDimension = 1200;
        if ($width > $height) {
            $newWidth = $maxDimension;
            $newHeight = intval($height * ($maxDimension / $width));
        } else {
            $newHeight = $maxDimension;
            $newWidth = intval($width * ($maxDimension / $height));
        }

        // Create image resource based on type
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $sourceImage = imagecreatefromjpeg($filepath);
                break;
            case 'png':
                $sourceImage = imagecreatefrompng($filepath);
                break;
            case 'webp':
                $sourceImage = imagecreatefromwebp($filepath);
                break;
            default:
                return; // Unsupported format for optimization
        }

        if (!$sourceImage) {
            return; // Failed to create image resource
        }

        // Create new image with optimized dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($extension === 'png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save optimized image
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($newImage, $filepath, 85); // 85% quality
                break;
            case 'png':
                imagepng($newImage, $filepath, 8); // Compression level 8
                break;
            case 'webp':
                imagewebp($newImage, $filepath, 85); // 85% quality
                break;
        }

        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }

    /**
     * Get product images
     * 
     * @param int $productId Product ID
     * @return array Array of image objects
     */
    public function getProductImages($productId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT image_id, image_path, image_uploaded_at
                FROM image
                WHERE prod_id = ?
                ORDER BY image_uploaded_at ASC
            ");
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching product images: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get primary product image (first uploaded image)
     * 
     * @param int $productId Product ID
     * @return object|null Primary image object or null
     */
    public function getPrimaryProductImage($productId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT image_id, image_path, image_uploaded_at
                FROM image
                WHERE prod_id = ?
                ORDER BY image_uploaded_at ASC
                LIMIT 1
            ");
            $stmt->execute([$productId]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching primary product image: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get product image count
     * 
     * @param int $productId Product ID
     * @return int Number of images
     */
    public function getProductImageCount($productId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM image WHERE prod_id = ?
            ");
            $stmt->execute([$productId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting product images: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete product image
     * 
     * @param int $imageId Image ID
     * @return array Result with success flag and message
     */
    public function deleteProductImage($imageId) {
        try {
            $this->pdo->beginTransaction();

            // Get image info before deletion
            $stmt = $this->pdo->prepare("SELECT image_path FROM image WHERE image_id = ?");
            $stmt->execute([$imageId]);
            $image = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$image) {
                throw new Exception('Bild hittades inte');
            }

            // Delete from image table
            $stmt = $this->pdo->prepare("DELETE FROM image WHERE image_id = ?");
            $stmt->execute([$imageId]);

            // Delete physical file
            $fullPath = __DIR__ . '/../' . $image->image_path;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Bild raderad framgångsrikt'
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error deleting image: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Fel vid radering av bild: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format bytes to human readable format
     * 
     * @param int $bytes Number of bytes
     * @return string Formatted string
     */
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get default image path
     * 
     * @param int $categoryId Category ID (unused, kept for compatibility)
     * @return string Default image path
     */
    public function getDefaultImagePath($categoryId = null) {
        return 'assets/images/default_antiqe_image.webp';
    }
}