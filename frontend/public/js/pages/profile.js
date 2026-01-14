$(document).ready(function () {
  // Check if user is logged in
  const token = localStorage.getItem("authToken");
  if (!token) {
    window.location.href = window.BASE_URL + "/login";
    return;
  }

  loadProfile();
});

function loadProfile() {
  const user = JSON.parse(localStorage.getItem("authUser")) || {};
  const billingAddress =
    JSON.parse(localStorage.getItem("billingAddress")) || {};

  // Display user info
  $("#userName").text(user.name || "");
  $("#userEmail").text(user.email || "");

  // Load address info
  $("#phoneNumber").val(user.phone_number || billingAddress.phoneNumber || "");
  $("#address").val(user.address || billingAddress.address || "");
  $("#city").val(user.city || billingAddress.city || "");
  $("#country").val(user.country || billingAddress.country || "");
  $("#zip").val(user.zip_code || billingAddress.zip || "");
}

function updateProfile() {
  const phoneNumber = $("#phoneNumber").val().trim();
  const address = $("#address").val().trim();
  const city = $("#city").val().trim();
  const country = $("#country").val().trim();
  const zip = $("#zip").val().trim();
  const token = localStorage.getItem("authToken");

  if (!phoneNumber || !address || !city || !country || !zip) {
    showAlert("Please fill in all fields", "danger");
    return;
  }

  const profileData = {
    phoneNumber,
    address,
    city,
    country,
    zip,
  };

  $.ajax({
    url: window.API_URL + "/user/profile/update",
    type: "POST",
    contentType: "application/json",
    headers: {
      Authorization: "Bearer " + token,
    },
    data: JSON.stringify(profileData),
    success: function (response) {
      // Update localStorage with new address
      const billingAddress = {
        phoneNumber,
        address,
        city,
        country,
        zip,
      };
      localStorage.setItem("billingAddress", JSON.stringify(billingAddress));

      // Update authUser with new info
      const user = JSON.parse(localStorage.getItem("authUser")) || {};
      user.phone_number = phoneNumber;
      user.address = address;
      user.city = city;
      user.country = country;
      user.zip_code = zip;
      localStorage.setItem("authUser", JSON.stringify(user));

      showAlert("Profile updated successfully!", "success");
    },
    error: function (xhr) {
      const error = xhr.responseJSON?.error || "Failed to update profile";
      showAlert(error, "danger");
    },
  });
}
