<div class="d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card border-0 shadow-sm" style="width: 100%; max-width: 400px">
        <div class="card-body p-5">
            <h2 class="text-center fw-bold mb-4">Login</h2>

            <div id="alertContainer" class="mb-3"></div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label fw-500">Email</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        placeholder="Enter your email"
                        required />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-500">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        placeholder="Enter your password"
                        required />
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3 small text-muted">
                Don't have an account?
                <a href="<?= BASE_URL ?>/register" class="text-decoration-none fw-500">Register here</a>
            </div>
        </div>
    </div>
</div>