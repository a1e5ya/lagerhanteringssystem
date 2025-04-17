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