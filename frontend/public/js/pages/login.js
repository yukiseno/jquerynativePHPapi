$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    login();
  });
});

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
      if (response.access_token) {
        localStorage.setItem("authToken", response.access_token);
        localStorage.setItem("authUser", JSON.stringify(response.user));

        // Set PHP session
        setPhpSession(response.access_token, response.user);
      }
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Login failed";
      showAlert(error, "danger");
    },
  });
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
