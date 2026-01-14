<?php
$colors = $data['colors'] ?? [];
$sizes = $data['sizes'] ?? [];
?>

<!-- Filters Section -->
<div class="filters-section">
    <div class="row">
        <div class="col-md-4">
            <div class="filter-label">Filter by Color:</div>
            <select id="colorFilter" class="form-select" onchange="applyFilter()">
                <option value="">All Colors</option>
                <?php foreach ($colors as $color): ?>
                    <option value="<?= $color['id'] ?>"><?= htmlspecialchars($color['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <div class="filter-label">Filter by Size:</div>
            <select id="sizeFilter" class="form-select" onchange="applyFilter()">
                <option value="">All Sizes</option>
                <?php foreach ($sizes as $size): ?>
                    <option value="<?= $size['id'] ?>"><?= htmlspecialchars($size['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <div class="filter-label">Search:</div>
            <input type="text" id="searchInput" class="form-control" placeholder="Search products..." onkeyup="debounceSearch()" />
        </div>
    </div>
</div>

<!-- Alert Messages -->
<div id="alertContainer" class="alert-container"></div>

<!-- Skeleton Loader -->
<div id="skeletonLoader" class="skeleton-loader" style="display: none">
    <?php for ($i = 0; $i < 12; $i++): ?>
        <div class="skeleton-card">
            <div class="skeleton-image"></div>
            <div class="skeleton-body">
                <div class="skeleton-text title"></div>
                <div class="skeleton-text price"></div>
                <div class="skeleton-text"></div>
                <div class="skeleton-text"></div>
            </div>
        </div>
    <?php endfor; ?>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" class="spinner-container" style="display: none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Products Grid (populated by JavaScript) -->
<div id="productsContainer"></div>