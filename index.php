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

$categories = getCategories($pdo);

// Load language settings if multilingual support is enabled
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Page title
$pageTitle = "Karis Antikvariat";

// Include header
include 'templates/header.php';
?>
<?php
// Hantera sökformuläret
$searchResults = [];
if (isset($_GET['search']) || (isset($_GET['category']) && $_GET['category'] !== 'all')) {
    $searchResults = searchProducts([
        'search' => $_GET['search'] ?? '',
        'category' => $_GET['category'] ?? 'all'
    ]);
}
?>

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

<div class="container my-4">
    <div id="homepage" class="mb-5">
        <section id="about" class="my-5">
            <div class="row">
                <div class="col-lg-6">
                    <h2>Om vår butik</h2>
                    <p>Karis Antikvariat har ett mycket brett utbud av böcker, men vi har specialiserat oss på finlandssvenska författare, lokalhistoria och sjöfart.</p>
                    <p>Vi har dessutom barn- och ungdomsböcker, serietidningar, seriealbum, DVD-filmer, CD- och vinylskivor samt samlarobjekt.</p>
                    <p>Välkommen att besöka oss och upptäck vårt unika utbud!</p>
                </div>
                <div class="col-lg-6">
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


        <section id="browse" class="my-5">
            <h2 class="mb-4">Bläddra i vårt sortiment</h2>

            <div class="search-bar mb-4">
                <form method="get" action="" id="search-form">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="text" class="form-control" id="public-search" name="search" 
                                placeholder="Sök efter titel, författare eller kategori" 
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <select class="form-select" id="public-category" name="category">
                                <option value="all">Alla kategorier</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['category_id']) ?>" 
                                    <?= (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </option>
                                <?php endforeach; ?>
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
                <th></th>
            </tr>
        </thead>
        <tbody id="public-inventory-body">
            <?php if (!empty($searchResults)): ?>
                <?php foreach ($searchResults as $product): ?>
                    <tr class="clickable-row" data-href="singleproduct.php?id=<?php echo htmlspecialchars($product->prod_id); ?>">
                        <td data-label="Titel"><?php echo htmlspecialchars($product->title); ?></td>
                        <td data-label="Författare/Artist">
                            <?php
                            if (!empty($product->first_names) && !empty($product->last_names)) {
                                echo htmlspecialchars($product->first_names . ' ' . $product->last_names);
                            } elseif (!empty($product->first_names)) {
                                echo htmlspecialchars($product->first_names);
                            } elseif (!empty($product->last_names)) {
                                echo htmlspecialchars($product->last_names);
                            } else {
                                echo 'Okänd författare'; // Fallback om inga namn finns
                            }
                            ?>
                        </td>
                        <td data-label="Kategori"><?php echo htmlspecialchars($product->category_name); ?></td>
                        <td data-label="Genre"><?php echo htmlspecialchars($product->genre_names); ?></td>
                        <td data-label="Skick"><?php echo htmlspecialchars($product->condition_name); ?></td>
                        <td data-label="Pris"><?php echo htmlspecialchars(number_format($product->price, 2, ',', ' ')) . ' €'; ?></td>
                        <td><a class="btn btn-success" href="singleproduct.php?id=<?php echo htmlspecialchars($product->prod_id); ?>">Visa detaljer</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <?php if (isset($_GET['search']) || (isset($_GET['category']) && $_GET['category'] !== 'all')): ?>

                    <tr><td colspan="5">Inga produkter hittades som matchar din sökning.</td></tr>
                <?php else: ?>
                    <tr><td colspan="5">Använd sökfältet ovan för att söka efter produkter.</td></tr>
                <?php endif; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
        </section>


        <section class="my-5">
            <h2 class="mb-4">På rea</h2>
            <div class="row g-4 row-cols-1 row-cols-md-3" id="featured-items">
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
    document.addEventListener('DOMContentLoaded', function () {
        // Gör raderna klickbara
        const clickableRows = document.querySelectorAll('#public-inventory-body .clickable-row');
        clickableRows.forEach(row => {
            row.addEventListener('click', function(event) {
                if (!event.target.closest('a')) {
                    window.location.href = this.dataset.href;
                }
            });
        });

        // Scrolla automatiskt till sökresultaten om en sökning har gjorts
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search') || (urlParams.has('category') && urlParams.get('category') !== 'all')) {
            const browseSection = document.getElementById('browse');
            if (browseSection) {
                browseSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
</script>
