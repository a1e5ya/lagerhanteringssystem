/**
 * forms.js - Form handling functionality
 * Contains form submission handling, validation, and data processing
 */

// authors management for add product
function initializeAuthorsManagement() {
  window.authors = [];

  $(document).on("click", function (e) {
    e.preventDefault();

    const firstName = $("#author-first").val().trim();
    const lastName = $("#author-last").val().trim();

    if (!firstName && !lastName) return;

    window.authors.push({
      first_name: firstName,
      last_name: lastName,
    });

    $("#authors-json").val(JSON.stringify(window.authors));

    // Clear inputs
    $("#author-first").val("");
    $("#author-last").val("");
  });
}

// genres management for add product
function initializeGenresManagement() {
  window.genres = [];

  $(document).on("change", "#item-genre", function () {
    const genreId = $(this).val();
    const genreName = $(this).find("option:selected").text();

    if (!genreId) return;

    // Prevent duplicates
    const exists = window.genres.find((g) => g.genre_id === genreId);
    if (exists) {
      $(this).val("");
      return;
    }

    window.genres.push({
      genre_id: genreId,
      genre_name: genreName,
    });

    // Update hidden field only
    $("#genres-json").val(JSON.stringify(window.genres));

    // Reset select input
    $(this).val("");
  });
}

// Handle form submission via AJAX
$(document)
  .off("submit", "#add-item-form")
  .on("submit", "#add-item-form", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const form = $(this);

    

    $.ajax({
      type: "POST",
url: BASE_URL + "/admin/addproduct.php",
      data: new FormData(form[0]),
      processData: false,
      contentType: false,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      success: function (response) {
        try {
          // Check if response is already an object
          const data =
            typeof response === "object" ? response : JSON.parse(response);

          if (data.success) {
            // Display success message
            showMessage(data.message, "success");

            // Clear the form
            form[0].reset();

            // Reset image preview if it exists
            if ($("#new-item-image").length) {
              $("#new-item-image").attr("src", BASE_URL + "/assets/images/src-book.webp");
            }

          } else {
            // Display error message
            showMessage(data.message, "danger");
          }
        } catch (e) {
          console.error("Error parsing response:", e);
          console.error("Raw response:", response);
          showMessage(
            "Error processing the server response. Check console for details.",
            "danger"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.error("Response Text:", xhr.responseText);
        showMessage("An error occurred. Please try again.", "danger");
      },
    });
  });

// Initialize add product page
function initializeAddProduct() {
  setupAutocomplete("author-first", "suggest-author-first", "authorFirst");
  setupAutocomplete("author-last", "suggest-author-last", "authorLast");
  setupAutocomplete("item-publisher", "suggest-publisher", "publisher");

  // Set up image preview
  const imageUpload = document.getElementById("item-image-upload");
  const imagePreview = document.getElementById("new-item-image");

  if (imageUpload && imagePreview) {
    imageUpload.addEventListener("change", function () {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  // Initialize authors management
  initializeAuthorsManagement();

  // Initialize genres management
  initializeGenresManagement();
}

// Initialize add author page
function initializeAddAuthor() {
    // Set up author form submission
    const authorForm = document.getElementById("add-author-form");
    if (authorForm) {
      $(authorForm)
        .off("submit")
        .on("submit", function (e) {
          e.preventDefault();
          e.stopPropagation();
          const form = $(this);
  
          $.ajax({
            type: "POST",
            url: BASE_URL + "/admin/addauthor.php",
            data: form.serialize(),
            headers: {
              "X-Requested-With": "XMLHttpRequest",
            },
            success: function (response) {
              try {
                const data =
                  typeof response === "object" ? response : JSON.parse(response);
  
                if (data.success) {
                  $("#author-message-container").html(
                    `<div class='alert alert-success'>${data.message}</div>`
                  );
                  $("#author-message-container").show();
                  form[0].reset();
                  
                  // Refresh the table after successful submission
                  refreshAuthorsTable();
                } else {
                  // Error handling...
                }
              } catch (e) {
                // Error handling...
              }
            },
            error: function (xhr, status, error) {
              // Error handling...
            },
          });
        });
    }
  }

// Helper function to show messages
function showMessage(message, type) {
  // First try specific message containers
  let container = $("#message-container");

  // If not found, try author message container
  if (container.length === 0) {
    container = $("#author-message-container");
  }

  // If still not found, create a general message container
  if (container.length === 0) {
    container = $('<div id="message-container"></div>');
    container.prependTo("#tabs-content");
  }

  container.html(`<div class="alert alert-${type}">${message}</div>`);
  container.show();

  // Scroll to message
  $("html, body").animate(
    {
      scrollTop: container.offset().top - 100,
    },
    200
  );

  // Auto hide after 5 seconds
  setTimeout(function () {
    container.fadeOut(500);
  }, 5000);
}
