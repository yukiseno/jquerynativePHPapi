/**
 * Home Page Scripts
 * Product listing, filtering, and search functionality
 */

let searchTimeout;

// Load products from API
function loadProducts() {
  const color = document.getElementById("colorFilter").value;
  const size = document.getElementById("sizeFilter").value;
  const search = document.getElementById("searchInput").value.trim();

  const activeFilters = [color, size, search].filter((f) => f).length;

  if (activeFilters > 1) {
    showAlert("Please use only one filter at a time", "info");
    return;
  }

  showSpinner(true);
  hideAlert();

  // Show skeleton loader
  document.getElementById("skeletonLoader").style.display = "grid";

  let url = API_URL + "/products";

  if (color) {
    url = API_URL + `/products/${color}/color`;
  } else if (size) {
    url = API_URL + `/products/${size}/size`;
  } else if (search) {
    url = API_URL + `/products/${encodeURIComponent(search)}/find`;
  }

  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      const products = data.data?.data || [];
      // Hide skeleton loader
      document.getElementById("skeletonLoader").style.display = "none";
      if (products.length === 0) {
        showAlert("No products found", "info");
        document.getElementById("productsContainer").innerHTML = `
          <div class="col-12 no-products">
            <div class="no-products-icon">📦</div>
            <p class="no-products-text">No products found</p>
          </div>
        `;
      } else {
        renderProducts(products);
      }
      showSpinner(false);
    })
    .catch((error) => {
      console.error("Error:", error);
      // Hide skeleton loader on error
      document.getElementById("skeletonLoader").style.display = "none";
      showAlert("Failed to load products", "danger");
      showSpinner(false);
    });
}

// Render products
function renderProducts(products) {
  let html = '<div class="row">';
  products.forEach((product) => {
    const sizes = product.sizes || [];
    const colors = product.colors || [];
    const status = product.status == 1 ? "In Stock" : "Out of Stock";
    const statusClass = product.status == 1 ? "text-success" : "text-danger";

    let sizesHtml = "";
    sizes.forEach((size) => {
      sizesHtml += `<span class="badge bg-light text-dark me-1">${size.name}</span>`;
    });

    let colorsHtml = "";
    colors.forEach((color) => {
      const colorValue = color.name.toLowerCase();
      colorsHtml += `<div class="color-circle" style="background-color: ${colorValue};" title="${color.name}"></div>`;
    });

    html += `
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <a href="/product/${product.slug}" class="product-link">
                    <div class="card product-card product-card-wrapper h-100">
                        <div class="product-image-wrapper">
                            <img src="${
                              product.thumbnail
                            }" class="product-image" alt="${product.name}">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title fw-semibold">${
                              product.name
                            }</h6>
                            <p class="card-text fw-bold mb-2">$${(
                              product.price / 100
                            ).toFixed(2)}</p>
                            
                            <div class="mb-2">
                                ${sizesHtml}
                            </div>
                            
                            <div class="mb-2">
                                <small class="fw-semibold ${statusClass}">${status}</small>
                            </div>
                            
                            <div class="mb-2">
                                ${colorsHtml}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        `;
  });
  html += "</div>";
  // Hide skeleton loader before rendering
  document.getElementById("skeletonLoader").style.display = "none";
  document.getElementById("productsContainer").innerHTML = html;
}

// Apply filter
function applyFilter() {
  const color = document.getElementById("colorFilter").value;
  const size = document.getElementById("sizeFilter").value;
  const search = document.getElementById("searchInput").value.trim();

  if (color) {
    document.getElementById("sizeFilter").disabled = true;
    document.getElementById("sizeFilter").value = "";
    document.getElementById("searchInput").disabled = true;
    document.getElementById("searchInput").value = "";
  } else if (size) {
    document.getElementById("colorFilter").disabled = true;
    document.getElementById("colorFilter").value = "";
    document.getElementById("searchInput").disabled = true;
    document.getElementById("searchInput").value = "";
  } else if (search) {
    document.getElementById("colorFilter").disabled = true;
    document.getElementById("colorFilter").value = "";
    document.getElementById("sizeFilter").disabled = true;
    document.getElementById("sizeFilter").value = "";
  } else {
    document.getElementById("colorFilter").disabled = false;
    document.getElementById("sizeFilter").disabled = false;
    document.getElementById("searchInput").disabled = false;
  }

  loadProducts();
}

// Debounce search
function debounceSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilter();
  }, 500);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  loadProducts();
});
