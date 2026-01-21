$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    login();
  });

  // 2FA form submission
  $("#twoFactorForm").on("submit", function (e) {
    e.preventDefault();
    verify2FA();
  });

  // Only numbers in 2FA code
  $("#twoFactorCode").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
    // Hide error message when user starts typing
    $("#twoFactorError").addClass("d-none");
  });
});

let currentLoginUser = null;
let currentLoginToken = null;

function login() {
  const email = $("#email").val().trim();
  const password = $("#password").val().trim();

  if (!email || !password) {
    showAlert("Please fill in all fields", "danger");
    return;
  }

  $.ajax({
    url: window.API_URL + "/user/login",
    type: "POST",
    data: JSON.stringify({ email, password }),
    contentType: "application/json",
    success: function (response) {
      if (response.data?.access_token) {
        const user = response.data.user;

        // Check if user has 2FA enabled
        if (user.two_factor_enabled) {
          // Store for later use
          currentLoginUser = user;
          currentLoginToken = response.data.access_token;

          // Show 2FA modal
          $("#twoFactorModal").modal("show");
          $("#twoFactorCode").focus();
        } else {
          // No 2FA, proceed with login
          completeLogin(response.data.access_token, user);
        }
      }
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Login failed";
      showAlert(error, "danger");
    },
  });
}

function verify2FA() {
  const code = $("#twoFactorCode").val().trim();

  if (!code || code.length !== 6) {
    $("#twoFactorError")
      .text("Please enter a valid 6-digit code")
      .removeClass("d-none");
    return;
  }

  $.ajax({
    url: window.API_URL + "/user/verify-2fa",
    type: "POST",
    data: JSON.stringify({
      user_id: currentLoginUser.id,
      code: code,
    }),
    contentType: "application/json",
    success: function (response) {
      $("#twoFactorModal").modal("hide");
      completeLogin(currentLoginToken, currentLoginUser);
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Invalid code";
      $("#twoFactorError").text(error).removeClass("d-none");

      // Clear input
      $("#twoFactorCode").val("");
    },
  });
}

function completeLogin(token, user) {
  localStorage.setItem("authToken", token);
  localStorage.setItem("authUser", JSON.stringify(user));

  // Set PHP session
  setPhpSession(token, user);
}

function setPhpSession(token, user) {
  $.ajax({
    url: window.BASE_URL + "/api.php",
    type: "POST",
    data: {
      action: "set_session",
      token: token,
      user: JSON.stringify(user),
    },
    success: function (response) {
      showAlert("Login successful!", "success");
      setTimeout(() => {
        window.location.href = window.BASE_URL + "/";
      }, 500);
    },
    error: function () {
      showAlert("Session error", "warning");
      // Still redirect even if session failed
      setTimeout(() => {
        window.location.href = window.BASE_URL + "/";
      }, 500);
    },
  });
}
