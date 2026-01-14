<?php
$product = $data['product'] ?? null;
$colors = $data['colors'] ?? [];
$sizes = $data['sizes'] ?? [];

if (!$product):
?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($data['error'] ?? 'Product not found') ?>
    </div>
    <a href="/" class="btn btn-outline-secondary">Back to Products</a>
<?php
    return;
endif;

$status = $product['status'] == 1 ? 'In Stock' : 'Out of Stock';
$statusClass = $product['status'] == 1 ? 'bg-success' : 'bg-danger';
?>

<!-- Alert Messages -->
<div id="alertContainer" class="alert-container"></div>

<div class="row mt-4">
    <!-- Product Image -->
    <div class="col-md-6">
        <img
            id="productImage"
            src="<?= htmlspecialchars($product['thumbnail']) ?>"
            class="img-fluid rounded product-detail-image"
            alt="<?= htmlspecialchars($product['name']) ?>" />
    </div>

    <!-- Product Details -->
    <div class="col-md-6">
        <!-- Name & Price -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h1 id="productName" class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                <p id="productDescription" class="text-muted"><?= htmlspecialchars($product['desc'] ?? '') ?></p>
            </div>
            <h2 id="productPrice" class="text-primary fw-bold">$<?= number_format($product['price'] / 100, 2) ?></h2>
        </div>

        <!-- Stock Status -->
        <div class="mb-4">
            <span id="stockStatus" class="badge <?= $statusClass ?> fs-6"><?= $status ?></span>
        </div>

        <!-- Sizes Section -->
        <div class="mb-4">
            <label class="fw-semibold mb-2">Size:</label>
            <div id="sizesContainer" class="d-flex gap-2 flex-wrap">
                <?php foreach ($sizes as $size): ?>
                    <button
                        type="button"
                        class="btn btn-outline-secondary size-btn"
                        data-size-id="<?= $size['id'] ?>"
                        onclick="selectSize(<?= $size['id'] ?>, '<?= htmlspecialchars($size['name']) ?>', this)">
                        <?= htmlspecialchars($size['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <small class="text-muted d-block mt-2">Please select a size</small>
        </div>

        <!-- Colors Section -->
        <div class="mb-4">
            <label class="fw-semibold mb-2">Color:</label>
            <div id="colorsContainer" class="d-flex gap-2 flex-wrap">
                <?php foreach ($colors as $color): ?>
                    <button
                        type="button"
                        class="btn color-btn"
                        data-color-id="<?= $color['id'] ?>"
                        data-color-name="<?= htmlspecialchars($color['name']) ?>"
                        onclick="selectColor(<?= $color['id'] ?>, '<?= htmlspecialchars($color['name']) ?>', this)"
                        style="background-color: <?= strtolower($color['name']) ?>; width: 50px; height: 50px; border: 2px solid #ddd;"
                        title="<?= htmlspecialchars($color['name']) ?>">
                    </button>
                <?php endforeach; ?>
            </div>
            <small class="text-muted d-block mt-2">Please select a color</small>
        </div>

        <!-- Quantity Input -->
        <div class="mb-4">
            <label for="quantity" class="form-label fw-semibold">Quantity:</label>
            <input
                type="number"
                id="quantity"
                class="form-control"
                value="1"
                min="1"
                max="<?= $product['qty'] ?? 999 ?>"
                style="max-width: 100px" />
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2">
            <button
                id="addToCartBtn"
                class="btn btn-primary btn-lg"
                onclick="addProductToCart()"
                disabled>
                Add to Cart
            </button>
            <a href="/" class="btn btn-outline-secondary btn-lg">
                Back to Products
            </a>
        </div>
    </div>
</div>