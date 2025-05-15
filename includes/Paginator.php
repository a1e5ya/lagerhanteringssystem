<?php
/**
 * Paginator Class
 * 
 * A centralized pagination class for handling all pagination functionality
 * across the Karis Antikvariat inventory management system.
 * 
 * @package    KarisAntikvariat
 * @subpackage Core
 * @author     Axxell
 * @version    1.0
 */

class Paginator {
    /**
     * @var int Total number of items to paginate
     */
    private $totalItems;
    
    /**
     * @var int Number of items to display per page
     */
    private $itemsPerPage;
    
    /**
     * @var int Current page number
     */
    private $currentPage;
    
    /**
     * @var string Column to sort by
     */
    private $sortColumn;
    
    /**
     * @var string Sort direction ('asc' or 'desc')
     */
    private $sortDirection;
    
    /**
     * @var int Total number of pages
     */
    private $totalPages;
    
    /**
     * Constructor
     * 
     * @param int    $totalItems    Total number of items to paginate
     * @param int    $itemsPerPage  Number of items per page (default: 15)
     * @param int    $currentPage   Current page number (default: 1)
     * @param string $sortColumn    Column to sort by (default: '')
     * @param string $sortDirection Sort direction (default: 'asc')
     */
    public function __construct($totalItems = 0, $itemsPerPage = 15, $currentPage = 1, $sortColumn = '', $sortDirection = 'asc') {
        $this->totalItems = (int)$totalItems;
        $this->itemsPerPage = max(1, (int)$itemsPerPage); // Ensure minimum of 1 item per page
        $this->currentPage = max(1, (int)$currentPage);   // Ensure minimum page 1
        $this->sortColumn = $sortColumn;
        $this->sortDirection = (strtolower($sortDirection) === 'desc') ? 'desc' : 'asc';
        
        // Calculate total pages
        $this->calculateTotalPages();
    }
    
    /**
     * Calculate the total number of pages
     * 
     * @return void
     */
    private function calculateTotalPages() {
        $this->totalPages = ($this->itemsPerPage > 0) ? 
            (int)ceil($this->totalItems / $this->itemsPerPage) : 1;
            
        // Adjust current page if it exceeds total pages
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
    }
    
    /**
     * Set the total number of items
     * 
     * @param int $totalItems Total number of items
     * @return Paginator For method chaining
     */
    public function setTotalItems($totalItems) {
        $this->totalItems = max(0, (int)$totalItems);
        $this->calculateTotalPages();
        return $this;
    }
    
    /**
     * Get the SQL OFFSET value for the query
     * 
     * @return int The offset value
     */
    public function getOffset() {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    /**
     * Get the total number of pages
     * 
     * @return int Total number of pages
     */
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    /**
     * Get the total number of items
     * 
     * @return int Total number of items
     */
    public function getTotalItems() {
        return $this->totalItems;
    }
    
    /**
     * Get the current page number
     * 
     * @return int Current page number
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    /**
     * Get the number of items per page
     * 
     * @return int Items per page
     */
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }
    
    /**
     * Get the first record number on the current page
     * 
     * @return int First record number
     */
    public function getFirstRecordNumber() {
        return min($this->totalItems, $this->getOffset() + 1);
    }
    
    /**
     * Get the last record number on the current page
     * 
     * @return int Last record number
     */
    public function getLastRecordNumber() {
        return min($this->totalItems, $this->getOffset() + $this->itemsPerPage);
    }
    
    /**
     * Generate HTML for pagination links
     * 
     * @param string $baseUrl    The base URL for pagination links
     * @param string $linkClass  CSS class for pagination links (default: 'pagination-link')
     * @return string HTML for pagination controls
     */
    public function generateLinks($baseUrl, $linkClass = 'pagination-link') {
        if ($this->totalPages <= 1) {
            return ''; // No pagination needed for a single page
        }
        
        $html = '<nav aria-label="Pagination">';
        $html .= '<ul class="pagination justify-content-center">';
        
        // Previous page link
        $html .= '<li class="page-item ' . ($this->currentPage <= 1 ? 'disabled' : '') . '">';
        if ($this->currentPage <= 1) {
            $html .= '<span class="page-link">&laquo;</span>';
        } else {
            $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . ($this->currentPage - 1) . '" data-page="' . ($this->currentPage - 1) . '">&laquo;</a>';
        }
        $html .= '</li>';
        
        // Calculate range of page numbers to display
        $startPage = max(1, $this->currentPage - 2);
        $endPage = min($this->totalPages, $this->currentPage + 2);
        
        // Ensure we always show at least 5 pages if available
        if ($endPage - $startPage + 1 < 5) {
            if ($startPage == 1) {
                $endPage = min($this->totalPages, $startPage + 4);
            } elseif ($endPage == $this->totalPages) {
                $startPage = max(1, $endPage - 4);
            }
        }
        
        // First page link if not in range
        if ($startPage > 1) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=1" data-page="1">1</a>';
            $html .= '</li>';
            
            // Add ellipsis if needed
            if ($startPage > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Page numbers
        for ($i = $startPage; $i <= $endPage; $i++) {
            $html .= '<li class="page-item ' . ($i == $this->currentPage ? 'active' : '') . '">';
            if ($i == $this->currentPage) {
                $html .= '<span class="page-link">' . $i . '</span>';
            } else {
                $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . $i . '" data-page="' . $i . '">' . $i . '</a>';
            }
            $html .= '</li>';
        }
        
        // Last page link if not in range
        if ($endPage < $this->totalPages) {
            // Add ellipsis if needed
            if ($endPage < $this->totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . $this->totalPages . '" data-page="' . $this->totalPages . '">' . $this->totalPages . '</a>';
            $html .= '</li>';
        }
        
        // Next page link
        $html .= '<li class="page-item ' . ($this->currentPage >= $this->totalPages ? 'disabled' : '') . '">';
        if ($this->currentPage >= $this->totalPages) {
            $html .= '<span class="page-link">&raquo;</span>';
        } else {
            $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . ($this->currentPage + 1) . '" data-page="' . ($this->currentPage + 1) . '">&raquo;</a>';
        }
        $html .= '</li>';
        
        $html .= '</ul>';
        $html .= '</nav>';
        
        return $html;
    }
    
    /**
     * Get a sorting link for a table column
     * 
     * @param string $column The column name to sort by
     * @param string $label  The label text to display
     * @return string HTML for the sort link
     */
    public function getSortLink($column, $label) {
        // Determine the sort direction for this column
        $newDirection = ($column === $this->sortColumn && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        
        // Determine the sort icon based on current sort
        $icon = '';
        if ($column === $this->sortColumn) {
            $icon = ($this->sortDirection === 'asc') ?
                '<i class="bi bi-sort-alpha-down"></i>' :
                '<i class="bi bi-sort-alpha-up"></i>';
        }
        
        // Generate the link
        $link = '?sort=' . urlencode($column) . '&order=' . $newDirection;
        
        return $link . '" class="sort-link" data-column="' . htmlspecialchars($column) . '" data-order="' . $newDirection . '">' . $icon . htmlspecialchars($label);
    }
    
    /**
     * Get the SQL LIMIT clause
     * 
     * @return string SQL LIMIT clause
     */
    public function getLimitSql() {
        return 'LIMIT ' . $this->itemsPerPage . ' OFFSET ' . $this->getOffset();
    }

    /**
     * Get the column to sort by
     * 
     * @return string Column name for sorting
     */
    public function getSortColumn() {
        return $this->sortColumn;
    }
    
    /**
     * Get the SQL ORDER BY clause
     * 
     * @return string SQL ORDER BY clause or empty string if no sort is set
     */
    public function getOrderBySql() {
        if (empty($this->sortColumn)) {
            return '';
        }
        
        return 'ORDER BY ' . $this->sortColumn . ' ' . $this->sortDirection;
    }
    
    /**
     * Convert pagination data to array for AJAX responses
     * 
     * @return array Pagination data as an array
     */
    public function toArray() {
        return [
            'currentPage' => $this->currentPage,
            'totalPages' => $this->totalPages,
            'totalItems' => $this->totalItems,
            'itemsPerPage' => $this->itemsPerPage,
            'sortColumn' => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
            'firstRecord' => $this->getFirstRecordNumber(),
            'lastRecord' => $this->getLastRecordNumber()
        ];
    }
}