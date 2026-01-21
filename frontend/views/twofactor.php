<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Two-Factor Authentication (2FA)</h4>
                </div>
                <div class="card-body">
                    <?php if ($data['has2FA']): ?>
                        <!-- 2FA is enabled -->
                        <div class="alert alert-success">
                            <strong>✓ 2FA is enabled</strong>
                            <p class="mb-0 mt-2">Your account is protected with two-factor authentication.</p>
                        </div>
                        <button class="btn btn-danger btn-block" id="disableTwoFactorBtn">
                            Disable 2FA
                        </button>
                    <?php else: ?>
                        <!-- 2FA is disabled -->
                        <p class="text-muted mb-4">Secure your account with two-factor authentication. You'll need an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator.</p>

                        <div id="setupStep1">
                            <h5>Step 1: Install Authenticator App</h5>
                            <p class="text-muted">
                                Download one of these apps on your phone:
                            </p>
                            <ul class="text-muted">
                                <li>Google Authenticator</li>
                                <li>Authy</li>
                                <li>Microsoft Authenticator</li>
                                <li>FreeOTP</li>
                            </ul>
                            <button class="btn btn-primary btn-block" id="startSetupBtn">
                                Continue
                            </button>
                        </div>

                        <div id="setupStep2" style="display: none;">
                            <h5>Step 2: Scan QR Code</h5>
                            <p class="text-muted mb-3">Scan this QR code with your authenticator app:</p>

                            <div class="text-center mb-4">
                                <img id="qrCode" src="" alt="QR Code" style="max-width: 250px; border: 1px solid #ddd; padding: 10px;">
                            </div>

                            <div class="alert alert-info">
                                <small>Can't scan? Enter this secret key manually:</small>
                                <div class="input-group input-group-sm mt-2">
                                    <input type="text" id="secretKey" class="form-control" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('#secretKey')">Copy</button>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary btn-block" id="nextStepBtn">
                                Next
                            </button>
                        </div>

                        <div id="setupStep3" style="display: none;">
                            <h5>Step 3: Verify</h5>
                            <p class="text-muted mb-3">Enter the 6-digit code from your authenticator app:</p>

                            <div class="form-group">
                                <input type="text" id="verificationCode" class="form-control form-control-lg text-center"
                                    placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
                            </div>

                            <button class="btn btn-success btn-block" id="enableTwoFactorBtn">
                                Enable 2FA
                            </button>
                        </div>

                        <div id="setupError" class="alert alert-danger mt-3" style="display: none;"></div>
                        <div id="setupSuccess" class="alert alert-success mt-3" style="display: none;"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>