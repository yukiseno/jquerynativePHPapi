<div style="max-width: 1000px; margin: 0 auto;">
    <h1 class="mb-4">My Profile</h1>
    <div id="alertContainer"></div>

    <div class="row">
        <!-- Left Panel: User Info & Links -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">Account Information</h5>
                    <div class="mb-3">
                        <label class="form-label fw-600">Name</label>
                        <p id="userName" class="mb-0" style="padding: 10px 0; font-size: 16px;"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Email</label>
                        <p id="userEmail" class="mb-0" style="padding: 10px 0; font-size: 16px;"></p>
                    </div>

                    <hr />

                    <a href="<?= BASE_URL ?>/orders" class="btn btn-outline-primary w-100">
                        View My Orders
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Panel: Address Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">Address Information</h5>
                    <form id="profileForm">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input
                                type="text"
                                class="form-control"
                                id="phoneNumber"
                                placeholder="Enter phone number" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input
                                type="text"
                                class="form-control"
                                id="address"
                                placeholder="Enter address" />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="city"
                                    placeholder="Enter city" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="country"
                                    placeholder="Enter country" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input
                                type="text"
                                class="form-control"
                                id="zip"
                                placeholder="Enter ZIP code" />
                        </div>

                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            onclick="updateProfile()">
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>