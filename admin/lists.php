<?php
/**
 * Lists Management
 * 
 * Contains:
 * - Product lists with filtering
 * - Batch operations
 * 
 * Functions:
 * - selectListItems()
 * - renderList()
 * - printList()
 * - batchOperations()
 * - exportToCSV()
 */
require_once '../config/config.php'; // Adjust the path as necessary

?>
            <!-- Lists Tab -->
            <div class="" id="lists">
                <!-- Lists content here, similar to before but with static placeholders -->
                <div class="table-responsive">
                    <table class="table table-hover" id="lists-table">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>ID</th>
                                <th>Titel</th>
                                <th>Författare</th>
                                <th>Kategori</th>
                                <th>Hylla</th>
                                <th>Skick</th>
                                <th>Pris</th>
                                <th>Status</th>
                                <th>Tillagd datum</th>
                            </tr>
                        </thead>
                        <tbody id="lists-body">
                            <!-- Table content will be loaded by JavaScript -->
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">Inga objekt hittades.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



            <script> // Function to handle exportable data (CSV, PDF, Excel)
  function exportData(format, type) {
    // Get current search parameters
    const searchTerm = document.getElementById(type === 'lists' ? 'lists-search-term' : 'search-term')?.value || '';
    const category = document.getElementById(type === 'lists' ? 'lists-category' : 'category-filter')?.value || 'all';
    
    // Create export URL
    const exportUrl = `admin/export.php?format=${format}&type=${type}&search=${encodeURIComponent(searchTerm)}&category=${encodeURIComponent(category)}`;
    
    // Create and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.target = '_blank';
    link.download = `export-${type}-${format}-${new Date().toISOString().slice(0, 10)}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  // Additional utility functions
  // Function to format currency
  function formatCurrency(amount) {
    return new Intl.NumberFormat('sv-SE', {
      style: 'currency',
      currency: 'SEK',
      minimumFractionDigits: 2
    }).format(amount);
  }

  // Function to validate ISBN
  function validateISBN(isbn) {
    // Remove hyphens and spaces
    isbn = isbn.replace(/[-\s]/g, '');
    
    // ISBN-10 validation
    if (isbn.length === 10) {
      let sum = 0;
      for (let i = 0; i < 9; i++) {
        sum += parseInt(isbn[i]) * (10 - i);
      }
      
      // Check digit can be 'X' (representing 10)
      const checkDigit = isbn[9].toUpperCase() === 'X' ? 10 : parseInt(isbn[9]);
      sum += checkDigit;
      
      return sum % 11 === 0;
    }
    
    // ISBN-13 validation
    if (isbn.length === 13) {
      let sum = 0;
      for (let i = 0; i < 12; i++) {
        sum += parseInt(isbn[i]) * (i % 2 === 0 ? 1 : 3);
      }
      
      return (10 - (sum % 10)) % 10 === parseInt(isbn[12]);
    }
    
    return false;
  }
</script>