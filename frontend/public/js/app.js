/**
 * Global App Utilities
 * Common functions used across all pages
 */

// UI Helper Functions
function showSpinner(show) {
  const spinner = document.getElementById("loadingSpinner");
  if (spinner) spinner.style.display = show ? "block" : "none";
}

function showAlert(message, type = "info") {
  const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
  const container = document.getElementById("alertContainer");
  if (container) container.innerHTML = alertHtml;
}

function hideAlert() {
  const container = document.getElementById("alertContainer");
  if (container) container.innerHTML = "";
}

// Cart Management
function addToCart(id, name, price) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existing = cart.find((item) => item.id === id);

  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({ id, name, price, quantity: 1 });
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
  showAlert(`${name} added to cart!`, "success");
}

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const count = cart.reduce((sum, item) => sum + item.quantity, 0);
  const cartElement = document.getElementById("cartCount");
  if (cartElement) cartElement.textContent = count;
}

// Auth Management
function logout() {
  localStorage.removeItem("authToken");
  localStorage.removeItem("authUser");

  // Clear PHP session
  $.ajax({
    url: window.BASE_URL + "/api.php",
    type: "POST",
    data: { action: "clear_session" },
    complete: function () {
      updateCartCount();
      window.location.href = window.BASE_URL + "/";
    },
  });
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  updateCartCount();
});
