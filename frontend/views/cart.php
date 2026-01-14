<div class="rounded" style="border: 1px solid #e0e0e0; background: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <div class="p-4">
        <!-- Empty Cart -->
        <div id="emptyCart" class="text-center py-5">
            <h3 class="mb-2">Your cart is empty</h3>
            <p class="text-muted">Add some products to get started!</p>
            <a href="<?= BASE_URL ?>/" class="btn btn-primary">Continue Shopping</a>
        </div>

        <!-- Cart Content -->
        <div id="cartContent" style="display: none;">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover" style="margin-bottom: 0;">
                    <thead style="background-color: #f8f9fa; border-bottom: 1px solid #e0e0e0;">
                        <tr style="font-weight: 600; color: #666; font-size: 14px;">
                            <th class="px-3 py-3">#</th>
                            <th class="px-3 py-3">Image</th>
                            <th class="px-3 py-3">Product</th>
                            <th class="px-3 py-3">Qty</th>
                            <th class="px-3 py-3">Price</th>
                            <th class="px-3 py-3">Color</th>
                            <th class="px-3 py-3">Size</th>
                            <th class="px-3 py-3">Subtotal</th>
                            <th class="px-3 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="cartItems" style="font-size: 14px;"></tbody>
                </table>
            </div>

            <!-- Total Section -->
            <div class="mt-4 text-center">
                <div style="display: inline-block; border: 2px solid #333; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                    Total: <span id="totalPrice">$0.00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Buttons -->
    <div id="footerButtons" style="display: none; border-top: 1px solid #e0e0e0; padding: 16px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary" style="padding: 8px 20px; font-size: 14px;">
            Continue Shopping
        </a>
        <a href="<?= BASE_URL ?>/checkout" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 8px 20px; font-size: 14px;">
            Checkout
        </a>
    </div>
</div>