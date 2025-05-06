$(document).ready(function () {
  // Load the initial content based on the URL parameter
  const urlParams = new URLSearchParams(window.location.search);
  const initialTab = urlParams.get("tab") || "search";
  loadTabContent(initialTab);

  // Handle tab clicks
  $(".nav-link").on("click", function (e) {
    e.preventDefault(); // Prevent default anchor behavior
    const tab = $(this).data("tab"); // Get the tab name
    loadTabContent(tab);
  });

  function loadTabContent(tab) {
    let url = "";

    switch (tab) {
      case "search":
        url = "search.php";
        break;
      case "addproduct":
        url = "addproduct.php";
        break;
      case "addauthor":
        url = "addauthor.php";
        break;
      case "tabledatamanagement":
        url = "tabledatamanagement.php";
        break;
      case "lists":
        url = "lists.php";
        break;
      default:
        return; // Exit if no valid tab
    }

    // Load the content via AJAX
    $("#tabs-content").load(
      "/prog23/lagerhanteringssystem/admin/" + url,
      function (response, status, xhr) {
        if (status == "error") {
          console.log(
            "Error loading content: " + xhr.status + " " + xhr.statusText
          );
        } else {
          // Initialize event handlers after loading content
          if (tab === "addauthor") {
            // Re-initialize the author form handler after loading the content
            $("#add-author-form")
              .off("submit")
              .on("submit", function (e) {
                e.preventDefault();
                e.stopPropagation();

                const form = $(this);
                $.ajax({
                  type: "POST",
                  url: "/prog23/lagerhanteringssystem/admin/addauthor.php",
                  data: form.serialize(),
                  headers: {
                    "X-Requested-With": "XMLHttpRequest",
                  },
                  success: function (response) {
                    try {
                      const data =
                        typeof response === "object"
                          ? response
                          : JSON.parse(response);

                      if (data.success) {
                        $("#author-message-container").html(
                          `<div class='alert alert-success'>${data.message}</div>`
                        );
                        $("#author-message-container").show();
                        form[0].reset();
                      } else {
                        $("#author-message-container").html(
                          `<div class='alert alert-danger'>${data.message}</div>`
                        );
                        $("#author-message-container").show();
                      }
                    } catch (e) {
                      console.error("Error:", e);
                      $("#author-message-container").html(
                        `<div class='alert alert-danger'>Error processing the response.</div>`
                      );
                      $("#author-message-container").show();
                    }
                  },
                  error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    $("#author-message-container").html(
                      `<div class='alert alert-danger'>An error occurred.</div>`
                    );
                    $("#author-message-container").show();
                  },
                });
              });
          }

          // Other tab-specific initializations
          if (tab === "addproduct") {
            setupAutocomplete(
              "author-first",
              "suggest-author-first",
              "authorFirst"
            );
            setupAutocomplete(
              "author-last",
              "suggest-author-last",
              "authorLast"
            );
            setupAutocomplete(
              "item-publisher",
              "suggest-publisher",
              "publisher"
            );
          }
        }
      }
    );

    // Update active class for tabs
    $(".nav-link").removeClass("active");
    $('.nav-link[data-tab="' + tab + '"]').addClass("active");

    // Update the URL to reflect the current tab
    window.history.pushState(null, "", `?tab=${tab}`);
  }

  //  form submission via AJAX for add product
  $(document)
    .off("submit", "#add-item-form")
    .on("submit", "#add-item-form", function (e) {
      e.preventDefault();
      e.stopPropagation(); 
      const form = $(this);

      $.ajax({
        type: "POST",
        url: "/prog23/lagerhanteringssystem/admin/addproduct.php",
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
              
              $("#message-container").html(
                `<div class='alert alert-success'>${data.message}</div>`
              );
              $("#message-container").show();

              form[0].reset();

              // Reset image preview if it exists
              if ($("#new-item-image").length) {
                $("#new-item-image").attr("src", "assets/images/src-book.webp");
              }
            } else {
              // error message
              $("#message-container").html(
                `<div class='alert alert-danger'>${data.message}</div>`
              );
              $("#message-container").show();
            }
          } catch (e) {
            console.error("Error parsing response:", e);
            console.error("Raw response:", response);
            $("#message-container").html(
              `<div class='alert alert-danger'>Error processing the server response. Check console for details.</div>`
            );
            $("#message-container").show();
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
          console.error("Response Text:", xhr.responseText);
          $("#message-container").html(
            `<div class='alert alert-danger'>An error occurred. Please try again.</div>`
          );
          $("#message-container").show();
        },
      });
    });
});

// Author handling for add product
$(document).ready(function () {
  // Store for multiple authors
  let authors = [];

  // Add author to the list
  $(document).on("click", "#add-author-to-list", function (e) {
    e.preventDefault();

    const firstName = $("#author-first").val().trim();
    const lastName = $("#author-last").val().trim();

    // Basic validation
    if (!firstName && !lastName) {
      alert("Please enter at least first or last name for the author");
      return;
    }

    // Add to array
    authors.push({
      first_name: firstName,
      last_name: lastName,
    });

    // Add to visual list
    const authorElement = $(`
            <div class="author-item mb-2 d-flex align-items-center">
                <span class="me-2">${firstName} ${lastName}</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-author" data-index="${
                  authors.length - 1
                }">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        `);

    $("#authors-list").append(authorElement);

    // Clear inputs
    $("#author-first").val("");
    $("#author-last").val("");

    // Update hidden field with JSON data
    $("#authors-json").val(JSON.stringify(authors));
  });

  // Remove author from the list
  $(document).on("click", ".remove-author", function () {
    const index = $(this).data("index");

    // Remove from array
    authors.splice(index, 1);

    // Re-render entire list (to handle indices correctly)
    $("#authors-list").empty();
    authors.forEach((author, idx) => {
      const authorElement = $(`
                <div class="author-item mb-2 d-flex align-items-center">
                    <span class="me-2">${author.first_name} ${author.last_name}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-author" data-index="${idx}">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            `);
      $("#authors-list").append(authorElement);
    });

    // Update hidden field with JSON data
    $("#authors-json").val(JSON.stringify(authors));
  });

  // Modify form submission to handle multiple authors
  $(document)
    .off("submit", "#add-item-form")
    .on("submit", "#add-item-form", function (e) {
      e.preventDefault();
      e.stopPropagation();
      const form = $(this);

      // If we have a single author in the input fields and none in our list
      // add the current author to the list
      if (authors.length === 0) {
        const firstName = $("#author-first").val().trim();
        const lastName = $("#author-last").val().trim();

        if (firstName || lastName) {
          authors.push({
            first_name: firstName,
            last_name: lastName,
          });

          // Update hidden field with JSON data
          $("#authors-json").val(JSON.stringify(authors));
        }
      }

      $.ajax({
        type: "POST",
        url: "/prog23/lagerhanteringssystem/admin/addproduct.php",
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
              $("#message-container").html(
                `<div class='alert alert-success'>${data.message}</div>`
              );
              $("#message-container").show();

              // Clear the form
              form[0].reset();

              // Reset image preview if it exists
              if ($("#new-item-image").length) {
                $("#new-item-image").attr("src", "assets/images/src-book.webp");
              }

              // Clear authors list
              authors = [];
              $("#authors-list").empty();
              $("#authors-json").val("");
            } else {
              // Display error message
              $("#message-container").html(
                `<div class='alert alert-danger'>${data.message}</div>`
              );
              $("#message-container").show();
            }
          } catch (e) {
            console.error("Error parsing response:", e);
            console.error("Raw response:", response);
            $("#message-container").html(
              `<div class='alert alert-danger'>Error processing the server response. Check console for details.</div>`
            );
            $("#message-container").show();
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
          console.error("Response Text:", xhr.responseText);
          $("#message-container").html(
            `<div class='alert alert-danger'>An error occurred. Please try again.</div>`
          );
          $("#message-container").show();
        },
      });
    });
});

//  delete button functionality

// Single event handler for delete functionality
$(document)
  .off("click", ".delete-item")
  .on("click", ".delete-item", function (e) {
    e.preventDefault();
    e.stopPropagation();

    // Get data from data attributes
    const id = $(this).data("id");
    const type = $(this).data("type");

    // Show confirmation
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
      // Temporarily disable the button to prevent double-clicks
      const $button = $(this);
      $button.prop("disabled", true);

      // Send AJAX request
      $.ajax({
        type: "POST",
        url: "/prog23/lagerhanteringssystem/admin/delete_item.php",
        data: {
          id: id,
          type: type,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            // Show success message
            $("#message-container").html(
              `<div class="alert alert-success">${response.message}</div>`
            );
            $("#message-container").show();

            // Remove the row from the table
            $button.closest("tr").fadeOut(300, function () {
              $(this).remove();
            });
          } else {
            // Show error message
            $("#message-container").html(
              `<div class="alert alert-danger">${response.message}</div>`
            );
            $("#message-container").show();

            // Re-enable the button
            $button.prop("disabled", false);
          }
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
          $("#message-container").html(
            '<div class="alert alert-danger">An error occurred during deletion.</div>'
          );
          $("#message-container").show();

          // Re-enable the button
          $button.prop("disabled", false);
        },
      });
    }
  });

// Modal
// Handle edit link clicks
$(document).on("click", ".edit-item", function (e) {
    e.preventDefault();
  
    const id = $(this).data("id");
    const type = $(this).data("type");
  
    $("#edit-item-id").val(id);
    $("#edit-item-type").val(type);
  
    if (type === "author") {
      const firstName = $(this).data("first-name");
      const lastName = $(this).data("last-name");
  
      // Show only author fields
      $("#edit-single-name-field").hide();
      $("#edit-author-fields").show();
  
      $("#edit-first-name").val(firstName);
      $("#edit-last-name").val(lastName);
  
      $("#editItemModalLabel").text("Redigera författare");
    } else {
      const name = $(this).data("name");
  
      // Show only single name field
      $("#edit-single-name-field").show();
      $("#edit-author-fields").hide();
  
      $("#edit-item-name").val(name);
      $("#editItemModalLabel").text("Redigera " + type.charAt(0).toUpperCase() + type.slice(1));
    }
  
    $("#editItemModal").modal("show");
  });
  

// Handle save button click
$(document).on("click", "#save-edit", function () {
    const id = $("#edit-item-id").val();
    const type = $("#edit-item-type").val();
  
    let postData = { id, type };
  
    if (type === "author") {
      const firstName = $("#edit-first-name").val().trim();
      const lastName = $("#edit-last-name").val().trim();
  
      if (!firstName || !lastName) {
        alert("Både förnamn och efternamn krävs.");
        return;
      }
  
      postData.first_name = firstName;
      postData.last_name = lastName;
    } else {
      const name = $("#edit-item-name").val().trim();
      if (!name) {
        alert("Ange ett namn.");
        return;
      }
  
      postData.name = name;
    }
  
    $.ajax({
      type: "POST",
      url: "/prog23/lagerhanteringssystem/admin/edit_item.php",
      data: postData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#message-container").html(`<div class="alert alert-success">${response.message}</div>`).show();
          $("#editItemModal").modal("hide");
  
          // Update the table row
          const rowSelector = `a.edit-item[data-id="${id}"][data-type="${type}"]`;
          const row = $(rowSelector).closest("tr");
  
          if (type === "author") {
            row.find("td:nth-child(2)").text(postData.first_name);
            row.find("td:nth-child(3)").text(postData.last_name);
            $(rowSelector)
              .data("first-name", postData.first_name)
              .data("last-name", postData.last_name);
          } else {
            row.find("td:nth-child(2)").text(postData.name);
            $(rowSelector).data("name", postData.name);
          }
        } else {
          alert(`Fel: ${response.message}`);
        }
      },
      error: function (xhr) {
        console.error("Error:", xhr.responseText);
        alert("Ett fel inträffade. Försök igen.");
      },
    });
  });
  

// author form submission via AJAX
$(document)
  .off("submit", "#add-author-form")
  .on("submit", "#add-author-form", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const form = $(this);

    $.ajax({
      type: "POST",
      url: "/prog23/lagerhanteringssystem/admin/addauthor.php",
      data: form.serialize(),
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
            $("#author-message-container").html(
              `<div class='alert alert-success'>${data.message}</div>`
            );
            $("#author-message-container").show();

            // Clear the form
            form[0].reset();
          } else {
            // Display error message
            $("#author-message-container").html(
              `<div class='alert alert-danger'>${data.message}</div>`
            );
            $("#author-message-container").show();
          }
        } catch (e) {
          console.error("Error parsing response:", e);
          console.error("Raw response:", response);
          $("#author-message-container").html(
            `<div class='alert alert-danger'>Error processing the server response. Check console for details.</div>`
          );
          $("#author-message-container").show();
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.error("Response Text:", xhr.responseText);
        $("#author-message-container").html(
          `<div class='alert alert-danger'>An error occurred. Please try again.</div>`
        );
        $("#author-message-container").show();
      },
    });
  });

// setupAutocomplete function
function setupAutocomplete(inputId, suggestBoxId, type) {
  const input = document.getElementById(inputId);
  const suggestBox = document.getElementById(suggestBoxId);

  if (!input || !suggestBox) {
    console.error(`Element not found: ${inputId} or ${suggestBoxId}`);
    return;
  }

  input.addEventListener("input", function () {
    const query = input.value.trim();
    if (query.length < 2) {
      suggestBox.innerHTML = "";
      suggestBox.style.display = "none";
      return;
    }

    // Use fetch API to get suggestions
    fetch(
      `admin/autocomplete.php?type=${type}&query=${encodeURIComponent(query)}`
    )
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        suggestBox.innerHTML = "";

        if (data.length === 0) {
          suggestBox.style.display = "none";
          return;
        }

        // Create suggestion items
        data.forEach((item) => {
          const div = document.createElement("div");
          div.textContent = item;
          div.classList.add("list-group-item", "list-group-item-action");
          div.style.cursor = "pointer";
          div.style.padding = "0.5rem 1rem";
          div.addEventListener("click", function () {
            input.value = item;
            suggestBox.innerHTML = "";
            suggestBox.style.display = "none";
          });
          suggestBox.appendChild(div);
        });

        // Show the suggestion box
        suggestBox.style.display = "block";
      })
      .catch((error) => {
        console.error("Error fetching autocomplete data:", error);
        suggestBox.style.display = "none";
      });
  });

  // Close suggestions when clicking outside
  document.addEventListener("click", function (e) {
    if (suggestBox && !suggestBox.contains(e.target) && e.target !== input) {
      suggestBox.innerHTML = "";
      suggestBox.style.display = "none";
    }
  });
}

// Direct DOM ready function to ensure all elements are loaded
document.addEventListener("DOMContentLoaded", function () {
  // Check if we're on the addproduct tab
  const addProductForm = document.getElementById("add-item-form");
  if (addProductForm) {
    console.log("Add product form detected, setting up autocomplete");
    setupAutocomplete("author-first", "suggest-author-first", "authorFirst");
    setupAutocomplete("author-last", "suggest-author-last", "authorLast");
    setupAutocomplete("item-publisher", "suggest-publisher", "publisher");
  }
});

// Additional tab content load handler for AJAX-loaded content
$(document).on("click", ".nav-link", function (e) {
  const tab = $(this).data("tab");

  // Wait for tab content to load
  if (tab === "addproduct") {
    // Use a small delay to ensure DOM elements are rendered
    setTimeout(function () {
      console.log("Add product tab loaded, setting up autocomplete");
      setupAutocomplete("author-first", "suggest-author-first", "authorFirst");
      setupAutocomplete("author-last", "suggest-author-last", "authorLast");
      setupAutocomplete("item-publisher", "suggest-publisher", "publisher");
    }, 500);
  }
});

// Handle the case where the page loads with the addproduct tab active
$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const initialTab = urlParams.get("tab") || "search";

  if (initialTab === "addproduct") {
    setTimeout(function () {
      console.log("Initial load with addproduct tab, setting up autocomplete");
      setupAutocomplete("author-first", "suggest-author-first", "authorFirst");
      setupAutocomplete("author-last", "suggest-author-last", "authorLast");
      setupAutocomplete("item-publisher", "suggest-publisher", "publisher");
    }, 500);
  }
});
