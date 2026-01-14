$(document).ready(function () {
  // If user is already logged in, redirect to home
  const token = localStorage.getItem("authToken");
  if (token) {
    window.location.href = window.BASE_URL + "/";
    return;
  }

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
        showAlert("Login successful!", "success");
        setTimeout(() => {
          updateAuthNav();
          updateCartCount();
          window.location.href = window.BASE_URL + "/";
        }, 1000);
      }
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Login failed";
      showAlert(error, "danger");
    },
  });
}
