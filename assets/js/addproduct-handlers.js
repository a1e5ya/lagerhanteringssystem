(function() {
  // Use event delegation to handle button clicks for dynamically loaded content
  $(document).on('click', '#add-author-btn', function(e) {
      e.preventDefault();
      
      const authorInput = $('#author-name');
      const authorName = authorInput.val().trim();
      
      if (authorName) {
          addAuthor(authorName);
          authorInput.val('');
      }
  });
  
  $(document).on('click', '#add-genre-btn', function(e) {
      e.preventDefault();
      
      const genreSelect = $('#item-genre');
      const genreId = genreSelect.val();
      const genreName = genreSelect.find('option:selected').text();
      
      if (genreId) {
          addGenre(genreId, genreName);
          genreSelect.val('');
      }
  });
  
  // Author management functions
  function addAuthor(authorName) {  
      // Initialize authors array if not exists
      window.authors = window.authors || [];
      
      if (!authorName || window.authors.includes(authorName)) {
          return;
      }
      
      // Add to authors array
      window.authors.push(authorName);
      updateAuthorsJson();
      
      // Remove "no author" message if it exists
      const selectedAuthorsDiv = $('.selected-authors');
      const noAuthorMessage = selectedAuthorsDiv.find('em');
      if (noAuthorMessage.length) {
          noAuthorMessage.remove();
      }
      
      // Add visual badge
      const authorBadge = $('<div class="selected-author badge bg-secondary p-2 me-2 mb-2"></div>');
      authorBadge.text(authorName);
      authorBadge.css('cursor', 'pointer');
      
      // Add remove functionality
      authorBadge.on('click', function() {
          // Remove from array
          const index = window.authors.indexOf(authorName);
          if (index > -1) {
              window.authors.splice(index, 1);
              updateAuthorsJson();
          }
          
          // Remove visual badge
          $(this).remove();
          
          // Add "no author" message if there are no authors
          if (window.authors.length === 0) {
              selectedAuthorsDiv.append('<em class="text-muted">Ingen författare vald</em>');
          }
      });
      
      selectedAuthorsDiv.append(authorBadge);
  }
  
  function updateAuthorsJson() {
      const authorsJsonInput = $('#authors-json');
      if (authorsJsonInput.length) {
          authorsJsonInput.val(JSON.stringify(window.authors));
      }
  }
  
  // Genre management functions
  function addGenre(genreId, genreName) {
      
      // Initialize genres array if not exists
      window.genres = window.genres || [];
      
      // Check if already exists
      if (window.genres.includes(genreId) || !genreId) {
          return;
      }
      
      // Add to genres array
      window.genres.push(genreId);
      updateGenresJson();
      
      // Add visual badge
      const selectedGenresDiv = $('.selected-genres');
      const genreBadge = $('<div class="selected-genre badge bg-secondary p-2 me-2 mb-2"></div>');
      genreBadge.text(genreName);
      genreBadge.css('cursor', 'pointer');
      
      // Add remove functionality
      genreBadge.on('click', function() {
          // Remove from array
          const index = window.genres.indexOf(genreId);
          if (index > -1) {
              window.genres.splice(index, 1);
              updateGenresJson();
          }
          
          // Remove visual badge
          $(this).remove();
      });
      
      selectedGenresDiv.append(genreBadge);
  }
  
  function updateGenresJson() {
      const genresJsonInput = $('#genres-json');
      if (genresJsonInput.length) {
          genresJsonInput.val(JSON.stringify(window.genres));
      }
  }
  
  // Also handle image preview with event delegation
  $(document).on('change', '#item-image-upload', function() {
      if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
              $('#new-item-image').attr('src', e.target.result);
          };
          reader.readAsDataURL(this.files[0]);
      }
  });
})();
