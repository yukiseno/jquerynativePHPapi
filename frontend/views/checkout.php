<div style="max-width: 1200px; margin: 0 auto;">
    <h1 class="mb-4">Checkout</h1>
    <div id="alertContainer"></div>

    <div class="row">
        <!-- Left: Billing Address & Coupon -->
        <div class="col-md-7">
            <!-- Billing Address Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Billing Address</h5>
                    <form id="addressForm">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" required />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="city" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" required />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" id="zip" required />
                        </div>
                    </form>
                </div>
            </div>

            <!-- Coupon Section -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Promo Code</h5>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="couponInput" placeholder="Enter promo code" />
                        <button type="button" onclick="applyCoupon()" id="applyCouponBtn" class="btn btn-primary">
                            Apply
                        </button>
                        <button type="button" onclick="removeCoupon()" id="removeCouponBtn" class="btn btn-outline-danger" style="display: none;">
                            Remove
                        </button>
                    </div>
                    <small class="text-muted d-block mb-2">Try: WELCOME10 or SUMMER20</small>
                    <div id="couponMessage"></div>
                </div>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Order Summary</h5>

                    <!-- Cart Items -->
                    <div id="orderItems" style="max-height: 300px; overflow-y: auto; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 15px;"></div>

                    <!-- Summary Details -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (10%):</span>
                        <span id="tax">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 d-none" id="discountRow">
                        <span>Discount:</span>
                        <span id="discount" class="text-success">-$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 d-none" id="couponRow">
                        <span>Coupon:</span>
                        <span>
                            <span id="couponCode" class="badge bg-success"></span>
                            <button type="button" onclick="removeCoupon()" class="btn btn-sm btn-outline-danger ms-2" style="padding: 2px 8px; font-size: 12px;">
                                ✕
                            </button>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between fw-bold border-top pt-3" style="font-size: 18px;">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>

                    <button id="placeOrderBtn" class="btn btn-primary w-100 mt-3" onclick="placeOrder()">
                        Place Order
                    </button>
                    <a href="<?= BASE_URL ?>/cart" class="btn btn-outline-secondary w-100 mt-2">
                        Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>