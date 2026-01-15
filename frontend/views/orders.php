<div style="max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>My Orders</h1>
        <a href="<?= BASE_URL ?>/profile" class="btn btn-outline-secondary">Back to Profile</a>
    </div>

    <div id="alertContainer"></div>

    <!-- Orders Table -->
    <div style="overflow-x: auto;">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 40px;">
                        Loading orders...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Order Details View -->
    <div id="orderDetails" style="display: none; margin-top: 40px; border-top: 1px solid #ddd; padding-top: 40px;">
        <h3 id="orderDetailsTitle" style="margin-bottom: 20px;"></h3>
        <div class="row">
            <div class="col-md-8">
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Color</th>
                                <th>Size</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsTable"></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="detailsSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="detailsDiscountRow" style="display: none;">
                            <span>Discount:</span>
                            <span id="detailsDiscount" class="text-success">-$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold border-top pt-2" style="font-size: 18px;">
                            <span>Total:</span>
                            <span id="detailsTotal">$0.00</span>
                        </div>
                        <button class="btn btn-outline-secondary w-100 mt-3" onclick="hideOrderDetails()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>