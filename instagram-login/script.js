document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");
  const loginButton = document.querySelector(".login-btn");

  // Function to check if form is valid
  function checkFormValidity() {
    if (
      usernameInput.value.trim() !== "" &&
      passwordInput.value.trim() !== ""
    ) {
      loginButton.disabled = false;
    } else {
      loginButton.disabled = true;
    }
  }

  // Add event listeners for input fields
  usernameInput.addEventListener("input", checkFormValidity);
  passwordInput.addEventListener("input", checkFormValidity);

  // Initial check
  checkFormValidity();

  // Form submission
  loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    // Show loading state
    loginButton.textContent = "Processing...";
    loginButton.disabled = true;

    // Submit form with fetch
    fetch("login.php", {
      method: "POST",
      body: new FormData(loginForm),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Show success message
          const successMessage = document.createElement("div");
          successMessage.className = "alert-success";
          successMessage.textContent = data.message;
          successMessage.style.backgroundColor = "#d4edda";
          successMessage.style.color = "#155724";
          successMessage.style.padding = "10px";
          successMessage.style.margin = "10px 0";
          successMessage.style.borderRadius = "4px";

          // Insert before the form
          loginForm.parentNode.insertBefore(successMessage, loginForm);

          // Reset form
          loginForm.reset();
          loginButton.textContent = "Log In";
          loginButton.disabled = true;

          // Redirect after delay (optional)
          setTimeout(() => {
            // You can redirect to a dashboard or another page
            // window.location.href = 'dashboard.php';

            // For demo purposes, just remove the success message
            successMessage.remove();
          }, 3000);
        } else {
          // Show error message
          alert(data.message || "An error occurred. Please try again.");
          loginButton.textContent = "Log In";
          loginButton.disabled = false;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
        loginButton.textContent = "Log In";
        loginButton.disabled = false;
      });
  });
});
