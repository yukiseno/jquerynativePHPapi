let appliedCoupon = null;

$(document).ready(function () {
  // Check if user is logged in
  const token = localStorage.getItem("authToken");
  if (!token) {
    if (confirm("Please login to checkout. Redirect to login page?")) {
      window.location.href = window.BASE_URL + "/login";
    } else {
      window.location.href = window.BASE_URL + "/cart";
    }
    return;
  }

  // Ensure discount and coupon rows are hidden initially
  $("#discountRow").addClass("d-none");
  $("#couponRow").addClass("d-none");

  loadBillingAddress();
  loadAppliedCoupon();
  renderOrderSummary();
});

function renderOrderSummary() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  if (cart.length === 0) {
    showAlert("Your cart is empty!", "warning");
    setTimeout(() => {
      window.location.href = window.BASE_URL + "/cart";
    }, 2000);
    return;
  }

  let html = "";
  let subtotal = 0;

  cart.forEach((item) => {
    const total = item.price * item.quantity;
    subtotal += total;
    html += `
      <div class="d-flex justify-content-between mb-2">
        <span>${item.name} x${item.quantity}</span>
        <span>$${(total / 100).toFixed(2)}</span>
      </div>
    `;
  });

  $("#orderItems").html(html);

  // Check if there's an applied coupon and calculate discount
  const discount = appliedCoupon ? appliedCoupon.discount_amount : 0;
  updateOrderTotal(subtotal, discount);
}

function updateOrderTotal(subtotal, discountPercent = 0) {
  // Convert from cents to dollars (subtotal is in cents)
  const subtotalDollars = subtotal / 100;

  // discountPercent is the percentage (e.g., 10 for 10%, 20 for 20%)
  const discountAmount = (subtotalDollars * discountPercent) / 100;
  const total = subtotalDollars - discountAmount;

  $("#subtotal").text("$" + subtotalDollars.toFixed(2));
  $("#discount").text("-$" + discountAmount.toFixed(2));
  $("#total").text("$" + total.toFixed(2));

  // Always hide discount row if no discount
  if (discountPercent > 0) {
    $("#discountRow").removeClass("d-none");
  } else {
    $("#discountRow").addClass("d-none");
  }

  // Always hide coupon row if no coupon applied
  if (appliedCoupon) {
    $("#couponCode").text(appliedCoupon.code);
    $("#couponRow").removeClass("d-none");
  } else {
    $("#couponRow").addClass("d-none");
  }
}

function applyCoupon() {
  const couponCode = $("#couponInput").val().trim();

  if (!couponCode) {
    showCouponMessage("Please enter a coupon code", "danger");
    return;
  }

  $.ajax({
    url: window.API_URL + "/apply/coupon",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({ coupon_code: couponCode }),
    success: function (response) {
      if (response.success) {
        appliedCoupon = response.data;
        // Save coupon to localStorage
        localStorage.setItem("appliedCoupon", JSON.stringify(response.data));

        // Calculate discount amount based on percentage
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        let subtotal = 0;
        cart.forEach((item) => (subtotal += item.price * item.quantity));

        // discount_amount is the percentage (e.g., 10 for 10%, 20 for 20%)
        const discountPercent = response.data.discount_amount;
        const discountDollars = (subtotal * discountPercent) / 100 / 100; // Convert cents to dollars

        showCouponMessage(
          `Coupon "${couponCode}" applied! Discount: ${discountPercent}% ($${discountDollars.toFixed(
            2,
          )})`,
          "success",
        );
        // Update UI - show remove button, disable input
        $("#couponInput").prop("disabled", true);
        $("#applyCouponBtn").hide();
        $("#removeCouponBtn").show();

        // Update order total with discount
        updateOrderTotal(subtotal, response.data.discount_amount);
      }
    },
    error: function (xhr) {
      showCouponMessage(
        xhr.responseJSON?.error || "Invalid or expired coupon",
        "danger",
      );
      appliedCoupon = null;
      localStorage.removeItem("appliedCoupon");
    },
  });
}

function removeCoupon() {
  appliedCoupon = null;
  localStorage.removeItem("appliedCoupon");

  // Reset UI
  $("#couponInput").val("");
  $("#couponInput").prop("disabled", false);
  $("#applyCouponBtn").show();
  $("#removeCouponBtn").hide();

  // Hide discount and coupon rows immediately
  $("#discountRow").addClass("d-none");
  $("#couponRow").addClass("d-none");

  showCouponMessage("Coupon removed", "info");

  // Re-render order summary without discount
  renderOrderSummary();
}

function showCouponMessage(message, type) {
  $("#couponMessage").html(
    `<div class="alert alert-${type} small">${message}</div>`,
  );
  setTimeout(() => $("#couponMessage").html(""), 5000);
}

function placeOrder() {
  const phoneNumber = $("#phoneNumber").val().trim();
  const address = $("#address").val().trim();
  const city = $("#city").val().trim();
  const country = $("#country").val().trim();
  const zip = $("#zip").val().trim();

  if (!phoneNumber || !address || !city || !country || !zip) {
    showAlert("Please fill in all billing address fields", "danger");
    return;
  }

  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (cart.length === 0) {
    showAlert("Your cart is empty", "danger");
    return;
  }

  // Get auth token
  const token = localStorage.getItem("authToken");
  if (!token) {
    showAlert("Please login to place order", "danger");
    return;
  }

  // Prepare order data
  const orderData = {
    cartItems: cart,
    address: { phoneNumber, address, city, country, zip },
    couponId: appliedCoupon ? appliedCoupon.id : null,
  };

  // Show loading state
  $("#placeOrderBtn").prop("disabled", true).text("Placing Order...");

  // Call API to create order
  $.ajax({
    url: window.API_URL + "/orders/store",
    type: "POST",
    contentType: "application/json",
    headers: {
      Authorization: "Bearer " + token,
    },
    data: JSON.stringify(orderData),
    success: function (response) {
      // Clear cart and coupon
      localStorage.removeItem("cart");
      localStorage.removeItem("appliedCoupon");
      appliedCoupon = null;

      showAlert("Order placed successfully!", "success");
      setTimeout(() => {
        updateCartCount();
        window.location.href = window.BASE_URL + "/";
      }, 1500);
    },
    error: function (xhr) {
      $("#placeOrderBtn").prop("disabled", false).text("Place Order");
      const error = xhr.responseJSON?.error || "Failed to place order";
      showAlert(error, "danger");
    },
  });
}

function loadBillingAddress() {
  const token = localStorage.getItem("authToken");

  $.ajax({
    url: window.API_URL + "/user/profile",
    type: "GET",
    headers: {
      Authorization: "Bearer " + token,
    },
    success: function (response) {
      if (response.data) {
        const user = response.data;
        $("#phoneNumber").val(user.phone_number || "");
        $("#address").val(user.address || "");
        $("#city").val(user.city || "");
        $("#country").val(user.country || "");
        $("#zip").val(user.zip_code || "");
      }
    },
    error: function () {
      // Silent fail - fields will be empty
    },
  });
}

// Load applied coupon from localStorage
function loadAppliedCoupon() {
  const savedCoupon = localStorage.getItem("appliedCoupon");
  if (savedCoupon) {
    appliedCoupon = JSON.parse(savedCoupon);
    // Display coupon in the input field
    $("#couponInput").val(appliedCoupon.code);
    $("#couponInput").prop("disabled", true);
    // Show remove button, hide apply button
    $("#applyCouponBtn").hide();
    $("#removeCouponBtn").show();
    showCouponMessage(
      `Coupon "${appliedCoupon.code}" applied! Discount: $${(
        appliedCoupon.discount_amount / 100
      ).toFixed(2)}`,
      "success",
    );
  }
}
