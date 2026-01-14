/**
 * Product Page Scripts
 * Product detail, color/size selection, and add to cart functionality
 */

let selectedColor = null;
let selectedSize = null;
let currentProduct = null;

// Select size
function selectSize(sizeId, sizeName, btn) {
  // Remove active class from all size buttons
  document
    .querySelectorAll(".size-btn")
    .forEach((b) => b.classList.remove("active"));
  // Add active class to clicked button
  btn.classList.add("active");
  selectedSize = { id: sizeId, name: sizeName };
  checkFormValidity();
}

// Select color
function selectColor(colorId, colorName, btn) {
  // Remove active class from all color buttons
  document
    .querySelectorAll(".color-btn")
    .forEach((b) => (b.style.borderColor = "#ddd"));
  // Add active class to clicked button
  btn.style.borderColor = "#667eea";
  selectedColor = { id: colorId, name: colorName };
  checkFormValidity();
}

// Check if form is valid
function checkFormValidity() {
  const quantity = parseInt(document.getElementById("quantity").value);
  const isValid = selectedSize && selectedColor && quantity >= 1;
  document.getElementById("addToCartBtn").disabled = !isValid;
}

// Add product to cart
function addProductToCart() {
  if (!selectedSize || !selectedColor) {
    showAlert("Please select both size and color", "warning");
    return;
  }

  const quantity = parseInt(document.getElementById("quantity").value);
  if (quantity < 1) {
    showAlert("Please enter a valid quantity", "warning");
    return;
  }

  const cartItem = {
    id: currentProduct.id,
    name: currentProduct.name,
    price: currentProduct.price,
    thumbnail: currentProduct.thumbnail,
    color: selectedColor,
    size: selectedSize,
    quantity: quantity,
  };

  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  // Check if exact item (with same color and size) already exists
  const existingIndex = cart.findIndex(
    (item) =>
      item.id === cartItem.id &&
      item.color.id === cartItem.color.id &&
      item.size.id === cartItem.size.id
  );

  if (existingIndex >= 0) {
    cart[existingIndex].quantity += quantity;
  } else {
    cart.push(cartItem);
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
  showAlert(`${currentProduct.name} added to cart!`, "success");

  // Reset form after adding
  setTimeout(() => {
    document.getElementById("quantity").value = 1;
    selectedColor = null;
    selectedSize = null;
    document
      .querySelectorAll(".size-btn")
      .forEach((b) => b.classList.remove("active"));
    document
      .querySelectorAll(".color-btn")
      .forEach((b) => (b.style.borderColor = "#ddd"));
    checkFormValidity();
  }, 1000);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  // Store product data from the view
  const productName = document.getElementById("productName")?.textContent;
  const productPrice = document.getElementById("productPrice")?.textContent;
  const productImage = document.getElementById("productImage")?.src;

  currentProduct = {
    name: productName,
    price: parseInt(productPrice.replace("$", "")) * 100,
    thumbnail: productImage,
  };

  // Add quantity change listener
  document
    .getElementById("quantity")
    ?.addEventListener("change", checkFormValidity);
  document
    .getElementById("quantity")
    ?.addEventListener("input", checkFormValidity);

  updateCartCount();
});
