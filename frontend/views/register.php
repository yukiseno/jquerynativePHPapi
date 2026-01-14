<div class="d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card border-0 shadow-sm" style="width: 100%; max-width: 400px">
        <div class="card-body p-5">
            <h2 class="text-center fw-bold mb-4">Create Account</h2>

            <div id="alertContainer" class="mb-3"></div>

            <form id="registerForm">
                <div class="mb-3">
                    <label for="name" class="form-label fw-500">Full Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        placeholder="Enter your full name"
                        required />
                </div>

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

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label fw-500">Confirm Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="confirmPassword"
                        placeholder="Confirm your password"
                        required />
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>

            <div class="text-center mt-3 small text-muted">
                Already have an account?
                <a href="<?= BASE_URL ?>/login" class="text-decoration-none fw-500">Login here</a>
            </div>
        </div>
    </div>
</div>