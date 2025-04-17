<?php
/**
 * Home Page
 * 
 * Contains:
 * - Feature items display
 * - Search functionality
 */

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/ui.php';

// Load language settings if multilingual support is enabled
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Page title
$pageTitle = "Karis Antikvariat";

// Include header
include 'templates/header.php';
?>

<!-- Hero Banner with Full Width Image -->
<div class="hero-container position-relative">
    <img src="assets/images/hero.webp" alt="Karis Antikvariat" class="hero-image w-100">
    <div class="container">
        <div class="hero-content position-absolute">
            <div class="hero-text-container p-5 rounded text-center">
                <h1>Välkommen till Karis Antikvariat</h1>
                <p class="lead">Din lokala antikvariat med ett brett utbud av böcker, musik och samlarobjekt</p>
                <a href="#browse" class="btn btn-primary btn-lg mt-3">Bläddra i vårt sortiment</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Container -->
<div class="container my-4">
    <!-- Homepage Content -->
    <div id="homepage" class="mb-5">
        <!-- About Section -->
        <section id="about" class="my-5">
            <div class="row">
                <div class="col-lg-6">
                    <h2>Om vår butik</h2>
                    <p>Karis Antikvariat har ett mycket brett utbud av böcker, men vi har specialiserat oss på finlandssvenska författare, lokalhistoria och sjöfart.</p>
                    <p>Vi har dessutom barn- och ungdomsböcker, serietidningar, seriealbum, DVD-filmer, CD- och vinylskivor samt samlarobjekt.</p>
                    <p>Välkommen att besöka oss och upptäck vårt unika utbud!</p>
                </div>
                <div class="col-lg-6">
                    <!-- Image Carousel -->
                    <div id="storeCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                        </div>
                        <div class="carousel-inner rounded">
                            <div class="carousel-item active">
                                <img src="assets/images/bild1.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 1">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/bild2.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 2">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/bild3.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 3">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/bild4.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 4">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#storeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Föregående</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#storeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Nästa</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>


<!-- Browse Section -->
<section id="browse" class="my-5">
    <h2 class="mb-4">Bläddra i vårt sortiment</h2>
    
    <div class="search-bar mb-4">
        <form method="get" action="" id="search-form">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <input type="text" class="form-control" id="public-search" name="search" placeholder="Sök efter titel, författare eller kategori">
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <select class="form-select" id="public-category" name="category">
                        <option value="all">Alla kategorier</option>
                        <option value="bok">Böcker</option>
                        <option value="finlandssvenska">Finlandssvenska författare</option>
                        <option value="lokalhistoria">Lokalhistoria</option>
                        <option value="sjöfart">Sjöfart</option>
                        <option value="barn">Barn- och ungdomsböcker</option>
                        <option value="cd">CD-skivor</option>
                        <option value="vinyl">Vinylskivor</option>
                        <option value="dvd">DVD-filmer</option>
                        <option value="serier">Serier</option>
                        <option value="samlar">Samlarobjekt</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" id="public-search-btn">Sök</button>
                </div>
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
    <table class="table table-hover" id="public-inventory-table">
        <thead class="table-light">
            <tr>
                <th>Titel</th>
                <th>Författare/Artist</th>
                <th>Kategori</th>
                <th>Genre</th>
                <th>Skick</th>
                <th>Pris (€)</th>
            </tr>
        </thead>
        <tbody id="public-inventory-body">
            <!-- Placeholder data -->
            <tr class="clickable-row" data-href="singleproduct.php?id=1">
                <td data-label="Titel">Trollvinter</td>
                <td data-label="Författare/Artist">Tove Jansson</td>
                <td data-label="Kategori">Bok - Finlandssvenska</td>
                <td data-label="Genre">Barnböcker, Äventyr</td>
                <td data-label="Skick">Nyskick</td>
                <td data-label="Pris">€24.95</td>
            </tr>
            <tr class="clickable-row" data-href="singleproduct.php?id=2">
                <td data-label="Titel">Muumipeikko ja pyrstötähti</td>
                <td data-label="Författare/Artist">Tove Jansson</td>
                <td data-label="Kategori">Bok - Finlandssvenska</td>
                <td data-label="Genre">Barnböcker</td>
                <td data-label="Skick">Mycket bra</td>
                <td data-label="Pris">€19.95</td>
            </tr>
            <tr class="clickable-row" data-href="singleproduct.php?id=3">
                <td data-label="Titel">Pippi Långstrump</td>
                <td data-label="Författare/Artist">Astrid Lindgren</td>
                <td data-label="Kategori">Bok - Barn/Ungdom</td>
                <td data-label="Genre">Barnböcker, Äventyr</td>
                <td data-label="Skick">Mycket bra</td>
                <td data-label="Pris">€14.95</td>
            </tr>
            <tr class="clickable-row" data-href="singleproduct.php?id=4">
                <td data-label="Titel">Harry Potter och De Vises Sten</td>
                <td data-label="Författare/Artist">J.K. Rowling</td>
                <td data-label="Kategori">Bok - Barn/Ungdom</td>
                <td data-label="Genre">Barnböcker, Äventyr</td>
                <td data-label="Skick">Nyskick</td>
                <td data-label="Pris">€29.95</td>
            </tr>
            <tr class="clickable-row" data-href="singleproduct.php?id=5">
                <td data-label="Titel">Jazz Classics</td>
                <td data-label="Författare/Artist"></td>
                <td data-label="Kategori">CD - Musik</td>
                <td data-label="Genre">Jazz</td>
                <td data-label="Skick">Bra</td>
                <td data-label="Pris">€15.00</td>
            </tr>
        </tbody>
    </table>
</div>
</section>


<!-- Featured Items -->
<section class="my-5">
    <h2 class="mb-4">På rea</h2>
    <div class="row g-4 row-cols-1 row-cols-md-3" id="featured-items">
        <!-- Static featured item 1 -->
        <div class="col">
            <div class="card h-100">
                <div class="row g-0 h-100">
                    <div class="col-6">
                        <img src="assets/images/src-book.webp" class="card-img-top h-100 object-fit-cover" alt="Trollvinter">
                    </div>
                    <div class="col-6">
                        <div class="card-body d-flex flex-column h-100">
                            <h5 class="card-title">Trollvinter</h5>
                            <p class="card-text text-muted flex-grow-1">Tove Jansson</p>
                            <p class="text-success fw-bold mb-2">€24.95</p>
                            <a href="singleproduct.php?id=1" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Static featured item 2 -->
        <div class="col">
            <div class="card h-100">
                <div class="row g-0 h-100">
                    <div class="col-6">
                        <img src="assets/images/src-cd.webp" class="card-img-top h-100 object-fit-cover" alt="Sibelius Symphony No. 2">
                    </div>
                    <div class="col-6">
                        <div class="card-body d-flex flex-column h-100">
                            <h5 class="card-title">Sibelius Symphony No. 2</h5>
                            <p class="card-text text-muted flex-grow-1">Helsinki Philharmonic</p>
                            <p class="text-success fw-bold mb-2">€22.50</p>
                            <a href="singleproduct.php?id=6" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Static featured item 3 -->
        <div class="col">
            <div class="card h-100">
                <div class="row g-0 h-100">
                    <div class="col-6">
                        <img src="assets/images/src-magazine.webp" class="card-img-top h-100 object-fit-cover" alt="Åbo - En historisk resa">
                    </div>
                    <div class="col-6">
                        <div class="card-body d-flex flex-column h-100">
                            <h5 class="card-title">Åbo - En historisk resa</h5>
                            <p class="card-text text-muted flex-grow-1">Zacharias Topelius</p>
                            <p class="text-success fw-bold mb-2">€34.95</p>
                            <a href="singleproduct.php?id=7" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    </div>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>

<script>
    // Make rows clickable
    document.addEventListener('DOMContentLoaded', function() {
        const clickableRows = document.querySelectorAll('.clickable-row');
        clickableRows.forEach(row => {
            row.addEventListener('click', function() {
                window.location.href = this.dataset.href;
            });
        });
    });
</script>