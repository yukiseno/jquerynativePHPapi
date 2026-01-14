<?php
$product = $data['product'] ?? null;
$colors = $data['colors'] ?? [];
$sizes = $data['sizes'] ?? [];

if (!$product):
?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($data['error'] ?? 'Product not found') ?>
    </div>
    <a href="<?= BASE_URL ?>/?page=home" class="btn btn-secondary">Back to Products</a>
<?php
    return;
endif;
?>

<div class="row">
    <div class="col-md-6">
        <img
            id="productImage"
            src="<?= htmlspecialchars($product['thumbnail']) ?>"
            class="img-fluid rounded"
            alt="<?= htmlspecialchars($product['name']) ?>" />
    </div>
    <div class="col-md-6">
        <h1 id="productName"><?= htmlspecialchars($product['name']) ?></h1>
        <p id="productDescription" class="text-muted"><?= htmlspecialchars($product['desc']) ?></p>
        <h3 id="productPrice" class="text-primary">$<?= number_format($product['price'] / 100, 2) ?></h3>

        <div class="mt-4">
            <?php if (!empty($product['colors'])): ?>
                <div class="mb-3">
                    <label class="form-label">Color:</label>
                    <select id="colorSelect" class="form-select">
                        <option value="">Select a color</option>
                        <?php foreach ($product['colors'] as $color): ?>
                            <option value="<?= $color['id'] ?>">
                                <?= htmlspecialchars($color['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if (!empty($product['sizes'])): ?>
                <div class="mb-3">
                    <label class="form-label">Size:</label>
                    <select id="sizeSelect" class="form-select">
                        <option value="">Select a size</option>
                        <?php foreach ($product['sizes'] as $size): ?>
                            <option value="<?= $size['id'] ?>">
                                <?= htmlspecialchars($size['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity:</label>
                <input
                    type="number"
                    id="quantity"
                    class="form-control"
                    value="1"
                    min="1"
                    max="10"
                    style="width: 100px" />
            </div>
        </div>

        <button class="btn btn-primary btn-lg mt-4" onclick="addToCart()">
            Add to Cart
        </button>
        <a href="<?= BASE_URL ?>/?page=home" class="btn btn-secondary btn-lg mt-4">
            Back to Products
        </a>
    </div>
</div>

<script>
    function addToCart() {
        const product = {
            id: <?= $product['id'] ?>,
            name: '<?= addslashes($product['name']) ?>',
            price: <?= $product['price'] ?>,
            color: document.getElementById('colorSelect')?.value || null,
            size: document.getElementById('sizeSelect')?.value || null
        };

        const quantity = parseInt(document.getElementById('quantity').value);

        if (quantity < 1) {
            alert('Please select a valid quantity');
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existing = cart.find(item =>
            item.id === product.id &&
            item.color === product.color &&
            item.size === product.size
        );

        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({
                ...product,
                quantity
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        alert('Added to cart!');
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById('cartCount').textContent = count;
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
    });
</script>