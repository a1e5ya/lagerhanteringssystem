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
 * @version    1.1
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
     * @var array Standard page size options
     */
    private $pageSizeOptions = [10, 20, 50, 100];
    
    /**
     * @var array Allowed sort columns (empty = all allowed)
     */
    private $allowedSortColumns = [];
    
    /**
     * Constructor
     * 
     * @param int    $totalItems       Total number of items to paginate
     * @param int    $itemsPerPage     Number of items per page (default: 20)
     * @param int    $currentPage      Current page number (default: 1)
     * @param string $sortColumn       Column to sort by (default: '')
     * @param string $sortDirection    Sort direction (default: 'asc')
     * @param array  $allowedSortColumns List of allowed sort columns (default: [])
     */
    public function __construct($totalItems = 0, $itemsPerPage = 20, $currentPage = 1, $sortColumn = '', $sortDirection = 'asc', array $allowedSortColumns = []) {
        $this->totalItems = (int)$totalItems;
        $this->itemsPerPage = $this->validatePageSize((int)$itemsPerPage);
        $this->currentPage = max(1, (int)$currentPage);
        $this->allowedSortColumns = $allowedSortColumns;
        $this->sortColumn = $this->validateSortColumn($sortColumn);
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
     * Validate and normalize the page size
     * 
     * @param int $size Requested page size
     * @return int Valid page size
     */
    private function validatePageSize($size) {
        // If size is in the list of options, use it
        if (in_array($size, $this->pageSizeOptions)) {
            return $size;
        }
        
        // Otherwise use the closest option
        $closest = $this->pageSizeOptions[0];
        $minDiff = abs($size - $closest);
        
        foreach ($this->pageSizeOptions as $option) {
            $diff = abs($size - $option);
            if ($diff < $minDiff) {
                $closest = $option;
                $minDiff = $diff;
            }
        }
        
        return $closest;
    }
    
    /**
     * Validate and sanitize sort column
     * 
     * @param string $column Column to sort by
     * @return string Validated column or empty string
     */
    private function validateSortColumn($column) {
        // If no restrictions, allow any column
        if (empty($this->allowedSortColumns)) {
            return $column;
        }
        
        // If column is in allowed list, use it
        if (in_array($column, $this->allowedSortColumns)) {
            return $column;
        }
        
        // Otherwise, return empty string (no sorting)
        return '';
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
     * Set the allowed sort columns
     * 
     * @param array $columns List of allowed sort columns
     * @return Paginator For method chaining
     */
    public function setAllowedSortColumns(array $columns) {
        $this->allowedSortColumns = $columns;
        $this->sortColumn = $this->validateSortColumn($this->sortColumn);
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
     * Get available page size options
     * 
     * @return array Page size options
     */
    public function getPageSizeOptions() {
        return $this->pageSizeOptions;
    }
    
    /**
     * Generate HTML for pagination links
     * 
     * @param string $baseUrl      The base URL for pagination links
     * @param string $linkClass    CSS class for pagination links (default: 'pagination-link')
     * @param bool   $includeSize  Whether to include the page size selector (default: true)
     * @return string HTML for pagination controls
     */
    public function generateLinks($baseUrl, $linkClass = 'pagination-link', $includeSize = true) {
        if ($this->totalPages <= 1 && !$includeSize) {
            return ''; // No pagination needed for a single page and no size selector
        }
        
        $html = '<div class="table-pagination-controls">';
        
        // Page size selector (if requested)
        if ($includeSize) {
            $html .= '<div class="table-page-size">';
            $html .= '<label>Show ';
            $html .= '<select class="form-select form-select-sm table-page-size-selector" style="width: auto;">';
            
            foreach ($this->pageSizeOptions as $size) {
                $selected = ($size == $this->itemsPerPage) ? ' selected' : '';
                $html .= '<option value="' . $size . '"' . $selected . '>' . $size . '</option>';
            }
            
            $html .= '</select> items per page';
            $html .= '</label>';
            $html .= '</div>';
        }
        
        // Pagination links (if more than one page)
        if ($this->totalPages > 1) {
            $html .= '<nav aria-label="Pagination">';
            $html .= '<ul class="pagination justify-content-center">';
            
            // Previous page link
            $html .= '<li class="page-item ' . ($this->currentPage <= 1 ? 'disabled' : '') . '">';
            if ($this->currentPage <= 1) {
                $html .= '<span class="page-link">&laquo;</span>';
            } else {
$params = ['page' => ($this->currentPage - 1), 'limit' => $this->itemsPerPage];
$html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&' . http_build_query($params) . '" data-page="' . ($this->currentPage - 1) . '">&laquo;</a>';            }
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
                $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=1&limit=' . $this->itemsPerPage . '" data-page="1">1</a>';
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
                    $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . $i . '&limit=' . $this->itemsPerPage . '" data-page="' . $i . '">' . $i . '</a>';
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
                $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . $this->totalPages . '&limit=' . $this->itemsPerPage . '" data-page="' . $this->totalPages . '">' . $this->totalPages . '</a>';
                $html .= '</li>';
            }
            
            // Next page link
            $html .= '<li class="page-item ' . ($this->currentPage >= $this->totalPages ? 'disabled' : '') . '">';
            if ($this->currentPage >= $this->totalPages) {
                $html .= '<span class="page-link">&raquo;</span>';
            } else {
                $html .= '<a class="page-link ' . $linkClass . '" href="' . $baseUrl . '&page=' . ($this->currentPage + 1) . '&limit=' . $this->itemsPerPage . '" data-page="' . ($this->currentPage + 1) . '">&raquo;</a>';
            }
            $html .= '</li>';
            
            $html .= '</ul>';
            $html .= '</nav>';
        }
        
        // Pagination info
        if ($this->totalItems > 0) {
            $html .= '<div class="table-pagination-info">';
            $html .= 'Showing <span class="table-showing-start">' . $this->getFirstRecordNumber() . '</span> to ';
            $html .= '<span class="table-showing-end">' . $this->getLastRecordNumber() . '</span> of ';
            $html .= '<span class="table-total-items">' . $this->totalItems . '</span> items';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
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
        // Skip if column is not allowed
        if (!empty($this->allowedSortColumns) && !in_array($column, $this->allowedSortColumns)) {
            return htmlspecialchars($label);
        }
        
        // Determine the sort direction for this column
        $newDirection = ($column === $this->sortColumn && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        
        // Determine the sort icon based on current sort
        $icon = '';
        if ($column === $this->sortColumn) {
            $icon = ($this->sortDirection === 'asc') ?
                '<i class="fas fa-sort-up ms-1"></i>' :
                '<i class="fas fa-sort-down ms-1"></i>';
        } else {
            $icon = '<i class="fas fa-sort ms-1 text-muted"></i>';
        }
        
        // Generate the link with sort and page size parameters
$params = [
    'sort' => $column,
    'order' => $newDirection,
    'page' => 1,
    'limit' => $this->itemsPerPage
];
$url = '?' . http_build_query($params);        
        return '<a href="' . $url . '" class="sort-link text-decoration-none text-reset" data-column="' . htmlspecialchars($column) . '" data-order="' . $newDirection . '">' . htmlspecialchars($label) . $icon . '</a>';
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
     * Get the sort direction
     * 
     * @return string Sort direction ('asc' or 'desc')
     */
    public function getSortDirection() {
        return $this->sortDirection;
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
            'lastRecord' => $this->getLastRecordNumber(),
            'pageSizeOptions' => $this->pageSizeOptions
        ];
    }
}