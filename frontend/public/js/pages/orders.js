$(document).ready(function () {
  // Check if user is logged in
  const token = localStorage.getItem("authToken");
  if (!token) {
    window.location.href = window.BASE_URL + "/login";
    return;
  }

  loadOrders();
});

function formatPrice(cents) {
  return "$" + (cents / 100).toFixed(2);
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

function loadOrders() {
  const token = localStorage.getItem("authToken");

  $.ajax({
    url: window.API_URL + "/user/orders",
    type: "GET",
    contentType: "application/json",
    headers: {
      Authorization: "Bearer " + token,
    },
    success: function (response) {
      const orders = response.data || [];

      if (orders.length === 0) {
        $("#ordersTableBody").html(
          '<tr><td colspan="8" class="text-center text-muted" style="padding: 40px;">No orders yet</td></tr>'
        );
        return;
      }

      let html = "";
      orders.forEach((order, index) => {
        html += `
          <tr style="cursor: pointer;" onclick="showOrderDetails(${index})">
            <td>${index + 1}</td>
            <td>#${order.id}</td>
            <td>${formatDate(order.created_at)}</td>
            <td>
              <span class="badge bg-info">View Items</span>
            </td>
            <td>${formatPrice(order.subtotal)}</td>
            <td>${formatPrice(order.discount_total)}</td>
            <td><strong>${formatPrice(order.total)}</strong></td>
            <td>
              <span class="badge bg-success">${order.status}</span>
            </td>
          </tr>
        `;
      });

      $("#ordersTableBody").html(html);
      window.allOrders = orders;
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Failed to load orders";
      $("#ordersTableBody").html(
        `<tr><td colspan="8" class="text-center text-danger">${error}</td></tr>`
      );
    },
  });
}

function showOrderDetails(orderIndex) {
  const order = window.allOrders[orderIndex];
  if (!order.items) {
    // Fetch order details with items
    const token = localStorage.getItem("authToken");
    $.ajax({
      url: window.API_URL + `/orders/${order.id}`,
      type: "GET",
      headers: {
        Authorization: "Bearer " + token,
      },
      success: function (response) {
        const orderData = response.data;
        displayOrderDetails(orderData);
      },
      error: function () {
        showAlert("Failed to load order details", "danger");
      },
    });
  } else {
    displayOrderDetails(order);
  }
}

function displayOrderDetails(order) {
  $("#orderDetailsTitle").text(
    `Order #${order.id} - ${formatDate(order.created_at)}`
  );

  // Display items
  let itemsHtml = "";
  if (order.items && order.items.length > 0) {
    order.items.forEach((item) => {
      const colorValue = (item.color_name || "").toLowerCase();
      const colorSquare = `<span style="display: inline-block; width: 20px; height: 20px; background-color: ${colorValue}; border: 1px solid #ddd; border-radius: 3px; vertical-align: middle;"></span>`;

      itemsHtml += `
        <tr>
          <td>${item.product_name}</td>
          <td>${colorSquare}</td>
          <td><span class="badge bg-secondary">${item.size_name}</span></td>
          <td>${item.qty}</td>
          <td>${formatPrice(item.price)}</td>
          <td><strong>${formatPrice(item.subtotal)}</strong></td>
        </tr>
      `;
    });
  }
  $("#orderItemsTable").html(itemsHtml);

  // Display summary
  $("#detailsSubtotal").text(formatPrice(order.subtotal));
  $("#detailsTotal").text(formatPrice(order.total));

  if (order.discount_total > 0) {
    $("#detailsDiscountRow").show();
    $("#detailsDiscount").text(`-${formatPrice(order.discount_total)}`);
  } else {
    $("#detailsDiscountRow").hide();
  }

  // Show details section
  $("#orderDetails").show();
  // Scroll to details
  $("html, body").animate(
    { scrollTop: $("#orderDetails").offset().top - 100 },
    800
  );
}

function hideOrderDetails() {
  $("#orderDetails").hide();
}
