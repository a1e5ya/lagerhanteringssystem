<?php
/**
 * Formatter Class
 * 
 * Contains utility methods for formatting various data types
 * - Currency values
 * - Dates
 * - Numbers
 * - Phone numbers
 * - Text
 * 
 * @package    KarisAntikvariat
 * @subpackage Includes
 * @author     Axxell
 * @version    1.0
 */

class Formatter {
    /**
     * @var string The locale to use for formatting
     */
    private $locale;
    
    /**
     * Initialize formatter with locale
     * 
     * @param string $locale The locale to use (default: sv_SE)
     */
    public function __construct(string $locale = 'sv_SE') {
        $this->locale = $locale;
        
        // Set locale for formatting
        setlocale(LC_MONETARY, $this->locale);
        setlocale(LC_TIME, $this->locale);
        setlocale(LC_NUMERIC, $this->locale);
    }
    
    /**
     * Format currency values
     * 
     * @param float|null $amount The amount to format
     * @param string $currency The currency symbol (default: €)
     * @return string Formatted currency value or dash if NULL
     */
    public function formatCurrency($amount, string $currency = '€'): string {
        if ($amount === null) {
            return '-';
        }
        
        return number_format((float)$amount, 2, ',', ' ') . ' ' . $currency;
    }
    
    /**
     * Format dates
     * 
     * @param string|DateTime $date The date to format
     * @param string $format The date format (default: Y-m-d)
     * @return string Formatted date
     */
    public function formatDate($date, string $format = 'Y-m-d'): string {
        if (empty($date)) {
            return '';
        }
        
        if ($date instanceof DateTime) {
            return $date->format($format);
        }
        
        return date($format, strtotime($date));
    }
    
    /**
     * Format numbers
     * 
     * @param float|null $number The number to format
     * @param int $decimals Number of decimal places
     * @return string Formatted number or dash if NULL
     */
    public function formatNumber($number, int $decimals = 2): string {
        if ($number === null) {
            return '-';
        }
        
        return number_format((float)$number, $decimals, ',', ' ');
    }
    
    /**
     * Format phone numbers
     * 
     * @param string $phone The phone number to format
     * @return string Formatted phone number
     */
    public function formatPhoneNumber(string $phone): string {
        // Remove all non-numeric characters
        $numericPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format for Swedish/Finnish phone numbers
        if (strlen($numericPhone) === 10) {
            // Format as 07X-XXX XX XX or similar
            return substr($numericPhone, 0, 3) . '-' . 
                   substr($numericPhone, 3, 3) . ' ' . 
                   substr($numericPhone, 6, 2) . ' ' . 
                   substr($numericPhone, 8, 2);
        }
        
        // Default - just add spaces for readability
        return chunk_split($numericPhone, 3, ' ');
    }
    
    /**
     * Format and truncate text
     * 
     * @param string $text The text to format
     * @param int|null $maxLength Maximum length before truncation
     * @return string Formatted text
     */
    public function formatText(string $text, ?int $maxLength = null): string {
        $formattedText = htmlspecialchars($text);
        
        if ($maxLength !== null && mb_strlen($formattedText) > $maxLength) {
            $formattedText = mb_substr($formattedText, 0, $maxLength) . '...';
        }
        
        return $formattedText;
    }
    
    /**
     * Format price - A convenience method that's an alias of formatCurrency
     * 
     * @param float|null $price The price to format
     * @return string Formatted price with € symbol or dash if NULL
     */
    public function formatPrice($price): string {
        return $this->formatCurrency($price);
    }
}
?>