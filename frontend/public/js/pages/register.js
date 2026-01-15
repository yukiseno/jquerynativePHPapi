$(document).ready(function () {
  $("#registerForm").on("submit", function (e) {
    e.preventDefault();
    register();
  });
});

function register() {
  const name = $("#name").val().trim();
  const email = $("#email").val().trim();
  const password = $("#password").val().trim();
  const confirmPassword = $("#confirmPassword").val().trim();

  if (!name || !email || !password || !confirmPassword) {
    showAlert("Please fill in all fields", "danger");
    return;
  }

  if (password.length < 6) {
    showAlert("Password must be at least 6 characters", "danger");
    return;
  }

  if (password !== confirmPassword) {
    showAlert("Passwords do not match", "danger");
    return;
  }

  $.ajax({
    url: window.API_URL + "/user/register",
    type: "POST",
    data: JSON.stringify({ name, email, password }),
    contentType: "application/json",
    success: function (response) {
      showAlert("Registration successful! Redirecting to login...", "success");
      setTimeout(() => {
        window.location.href = window.BASE_URL + "/login";
      }, 1500);
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Registration failed";
      showAlert(error, "danger");
    },
  });
}
