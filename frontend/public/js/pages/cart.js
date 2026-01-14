$(document).ready(function () {
  loadCart();
});

function loadCart() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  if (cart.length === 0) {
    $("#emptyCart").show();
    $("#cartContent").hide();
    $("#footerButtons").hide();
    return;
  }

  $("#emptyCart").hide();
  $("#cartContent").show();
  $("#footerButtons").show();

  let html = "";
  let total = 0;

  cart.forEach((item, index) => {
    const subtotal = (item.price / 100) * item.quantity;
    total += subtotal;

    const colorValue = (item.colorName || "").toLowerCase();
    const colorSquare = item.colorName
      ? `<div style="width: 20px; height: 20px; background-color: ${colorValue}; border: 1px solid #ddd; border-radius: 3px;"></div>`
      : "-";

    html += `
      <tr style="border-bottom: 1px solid #f0f0f0;">
        <td class="px-3 py-4">${index + 1}</td>
        <td class="px-3 py-4">
          <img src="${item.thumbnail || ""}" alt="${
      item.name
    }" style="height: 56px; width: 56px; border-radius: 4px; object-fit: cover;">
        </td>
        <td class="px-3 py-4 fw-semibold">${item.name}</td>
        <td class="px-3 py-4">
          <div style="display: flex; align-items: center; gap: 8px; width: fit-content;">
            <button class="btn btn-sm" style="padding: 2px 6px; border: none; color: #999; cursor: pointer;" onclick="incrementQuantity(${index})">▲</button>
            <span style="font-weight: 600; min-width: 20px; text-align: center;">${
              item.quantity
            }</span>
            <button class="btn btn-sm" style="padding: 2px 6px; border: none; color: #999; cursor: pointer;" onclick="decrementQuantity(${index})">▼</button>
          </div>
        </td>
        <td class="px-3 py-4">$${(item.price / 100).toFixed(2)}</td>
        <td class="px-3 py-4">
          ${colorSquare}
        </td>
        <td class="px-3 py-4">
          ${
            item.sizeName
              ? `<span style="display: inline-block; background-color: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #333;">${item.sizeName}</span>`
              : "-"
          }
        </td>
        <td class="px-3 py-4 fw-semibold">$${subtotal.toFixed(2)}</td>
        <td class="px-3 py-4">
          <button class="btn btn-sm" style="color: #999; border: none; cursor: pointer; font-size: 18px;" onclick="removeFromCart(${index})">✕</button>
        </td>
      </tr>
    `;
  });

  $("#cartItems").html(html);
  $("#totalPrice").text("$" + total.toFixed(2));
  updateCartCount();
}

function incrementQuantity(index) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (cart[index]) {
    cart[index].quantity = (cart[index].quantity || 1) + 1;
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  }
}

function decrementQuantity(index) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (cart[index] && cart[index].quantity > 1) {
    cart[index].quantity -= 1;
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  }
}

function removeFromCart(index) {
  if (confirm("Remove this item from cart?")) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  }
}
