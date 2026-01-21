$(document).ready(function () {
  // Check if user is logged in
  const token = localStorage.getItem("authToken");
  if (!token) {
    window.location.href = window.BASE_URL + "/login";
    return;
  }

  // Setup button
  $("#startSetupBtn").click(function () {
    generateQRCode();
  });

  // Next step button
  $("#nextStepBtn").click(function () {
    $("#setupStep2").hide();
    $("#setupStep3").show();
  });

  // Enable 2FA button
  $("#enableTwoFactorBtn").click(function () {
    const code = $("#verificationCode").val().trim();

    if (!code || code.length !== 6) {
      showError("Please enter a valid 6-digit code");
      return;
    }

    enable2FA(code);
  });

  // Disable 2FA button
  $("#disableTwoFactorBtn").click(function () {
    if (
      confirm(
        "Are you sure? You will need to set up 2FA again to re-enable it.",
      )
    ) {
      disable2FA();
    }
  });

  // Allow only numbers in verification code
  $("#verificationCode").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
});

let currentSecret = null;

function generateQRCode() {
  const token = localStorage.getItem("authToken");

  $.ajax({
    url: window.API_URL + "/user/2fa/setup",
    type: "GET",
    headers: {
      Authorization: "Bearer " + token,
    },
    success: function (response) {
      const data = response.data || {};
      currentSecret = data.secret;

      // Display QR code
      $("#qrCode").attr("src", data.qrCodeURL);
      $("#secretKey").val(data.secret);

      // Show step 2
      $("#setupStep1").hide();
      $("#setupStep2").show();
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Failed to generate QR code";
      showError(error);
    },
  });
}

function enable2FA(code) {
  const token = localStorage.getItem("authToken");

  $.ajax({
    url: window.API_URL + "/user/2fa/enable",
    type: "POST",
    contentType: "application/json",
    headers: {
      Authorization: "Bearer " + token,
    },
    data: JSON.stringify({
      secret: currentSecret,
      code: code,
    }),
    success: function (response) {
      // Update localStorage with new 2FA status
      const authUser = JSON.parse(localStorage.getItem("authUser"));
      authUser.two_factor_enabled = 1;
      localStorage.setItem("authUser", JSON.stringify(authUser));

      // Update PHP session
      updatePhpSession(authUser);

      showSuccess("2FA enabled successfully! Your account is now more secure.");

      // Redirect after 2 seconds
      setTimeout(function () {
        window.location.href = window.BASE_URL + "/twofactor";
      }, 2000);
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Failed to enable 2FA";
      showError(error);
    },
  });
}

function disable2FA() {
  const token = localStorage.getItem("authToken");

  $.ajax({
    url: window.API_URL + "/user/2fa/disable",
    type: "POST",
    headers: {
      Authorization: "Bearer " + token,
    },
    success: function (response) {
      // Update localStorage with new 2FA status
      const authUser = JSON.parse(localStorage.getItem("authUser"));
      authUser.two_factor_enabled = 0;
      localStorage.setItem("authUser", JSON.stringify(authUser));

      // Update PHP session
      updatePhpSession(authUser);

      showAlert("2FA disabled successfully", "success");

      // Reload page after 1 second
      setTimeout(function () {
        location.reload();
      }, 1000);
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Failed to disable 2FA";
      showError(error);
    },
  });
}

function showError(message) {
  $("#setupError").text(message).show();
  $("#setupSuccess").hide();

  // Auto-hide after 5 seconds
  setTimeout(function () {
    $("#setupError").fadeOut();
  }, 5000);
}

function showSuccess(message) {
  $("#setupSuccess").text(message).show();
  $("#setupError").hide();
}

function copyToClipboard(selector) {
  const element = document.querySelector(selector);
  element.select();
  document.execCommand("copy");
  alert("Secret key copied to clipboard!");
}

function updatePhpSession(user) {
  const token = localStorage.getItem("authToken");

  $.ajax({
    url: window.BASE_URL + "/api.php",
    type: "POST",
    data: {
      action: "set_session",
      token: token,
      user: JSON.stringify(user),
    },
    error: function () {
      // Silently fail - session update is not critical
    },
  });
}
