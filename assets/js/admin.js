/**
 * Admin JavaScript
 * Admin-specific scripts
 */
$(document).ready(function() {
  // Load the initial content for the search tab
  loadTabContent('search');

  // Handle tab clicks
  $('.nav-link').on('click', function(e) {
      e.preventDefault(); // Prevent default anchor behavior
      const tab = $(this).data('tab'); // Get the tab name
      loadTabContent(tab); // Load corresponding content
  });

  function loadTabContent(tab) {
      // Construct the URL based on the selected tab
      let url = '';

      switch (tab) {
          case 'search':
              url = 'search.php'; // Assuming you have this file
              break;
          case 'addproduct':
              url = 'addproduct.php';
              break;
          case 'tabledatamanagement':
              url = 'tabledatamanagement.php'; // This is your edit database file
              break;
          case 'lists':
              url = 'lists.php'; // Assuming you have this file
              break;
          default:
              return; // Exit if no valid tab
      }
      console.log("Loading content from: /prog23/lagerhanteringssystem/admin/" + url); // Add this line

      // Load the content via AJAX
      $('#tabs-content').load('/prog23/lagerhanteringssystem/admin/' + url, function(response, status, xhr) {
          if (status == "error") {
              console.log("Error loading content: " + xhr.status + " " + xhr.statusText);
          }
      });

      // Update active class for tabs
      $('.nav-link').removeClass('active'); // Remove active class from all tabs
      $('.nav-link[data-tab="' + tab + '"]').addClass('active'); // Add active class to clicked tab
  }
});