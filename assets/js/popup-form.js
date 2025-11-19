// Popup Lead Form with Timed Intervals
(function () {
  "use strict";

  // Configuration
  const POPUP_INTERVALS = [10000, 20000, 30000, 60000]; // 10s, 20s, 30s, 60s
  let popupShownCount = 0;
  let currentInterval = 0;

  // Check if popup was closed in this session
  function isPopupClosed() {
    return sessionStorage.getItem("leadPopupClosed") === "true";
  }

  // Check if form was submitted in this session
  function isFormSubmitted() {
    return sessionStorage.getItem("leadFormSubmitted") === "true";
  }

  // Create popup HTML
  function createPopup() {
    const popupHTML = `
            <div id="lead-popup-overlay" class="lead-popup-overlay">
                <div class="lead-popup-content">
                    <button class="lead-popup-close" id="lead-popup-close">&times;</button>
                    <div class="lead-popup-header">
                        <h2>GET EXCLUSIVE OFFERS!</h2>
                        <p>Fill the form below and get special discounts on plots</p>
                    </div>
                    <div class="lead-popup-body">
                        <form id="lead-popup-form" class="lead-popup-form">
                            <div class="form-group">
                                <label for="popup-name">Full Name *</label>
                                <input type="text" id="popup-name" name="name" required placeholder="Enter your name">
                            </div>
                            <div class="form-group">
                                <label for="popup-email">Email Address *</label>
                                <input type="email" id="popup-email" name="email" required placeholder="Enter your email">
                            </div>
                            <div class="form-group">
                                <label for="popup-phone">Phone Number *</label>
                                <input type="tel" id="popup-phone" name="phone" required placeholder="Enter your phone">
                            </div>
                            <div class="form-group">
                                <label for="popup-subject">Interest In</label>
                                <select id="popup-subject" name="subject">
                                    <option value="">Select your interest</option>
                                    <option value="Property Inquiry">Property Inquiry</option>
                                    <option value="Plot Booking">Plot Booking</option>
                                    <option value="Site Visit">Site Visit Request</option>
                                    <option value="Investment Query">Investment Query</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="popup-message">Message</label>
                                <textarea id="popup-message" name="message" placeholder="Tell us more about your requirements"></textarea>
                            </div>
                            <button type="submit" class="lead-popup-submit">Submit Inquiry</button>
                            <div id="popup-message" class="lead-popup-message"></div>
                        </form>
                    </div>
                </div>
            </div>
        `;

    document.body.insertAdjacentHTML("beforeend", popupHTML);
  }

  // Show popup
  function showPopup() {
    // Don't show if already closed or form submitted
    if (isPopupClosed() || isFormSubmitted()) {
      return;
    }

    const overlay = document.getElementById("lead-popup-overlay");
    if (overlay) {
      overlay.classList.add("active");
      popupShownCount++;
      document.body.style.overflow = "hidden"; // Prevent background scrolling
    }
  }

  // Hide popup
  function hidePopup() {
    const overlay = document.getElementById("lead-popup-overlay");
    if (overlay) {
      overlay.classList.remove("active");
      document.body.style.overflow = ""; // Restore scrolling
    }
  }

  // Close popup and mark as closed for session
  function closePopup() {
    hidePopup();
    sessionStorage.setItem("leadPopupClosed", "true");
  }

  // Schedule next popup
  function scheduleNextPopup() {
    if (
      currentInterval < POPUP_INTERVALS.length &&
      !isPopupClosed() &&
      !isFormSubmitted()
    ) {
      setTimeout(function () {
        showPopup();
        currentInterval++;
        scheduleNextPopup();
      }, POPUP_INTERVALS[currentInterval]);
    }
  }

  // Handle form submission
  function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector(".lead-popup-submit");
    const messageDiv = document.getElementById("popup-message");

    // Get form data
    const formData = new FormData(form);

    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";
    messageDiv.className = "lead-popup-message";
    messageDiv.textContent = "";

    // Submit via AJAX
    fetch("inc/contact.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.type === "success") {
          messageDiv.className = "lead-popup-message success";
          messageDiv.textContent = "✓ Thank you! We will contact you soon.";
          form.reset();
          sessionStorage.setItem("leadFormSubmitted", "true");

          // Close popup after 3 seconds
          setTimeout(function () {
            hidePopup();
          }, 3000);
        } else {
          messageDiv.className = "lead-popup-message error";
          messageDiv.textContent =
            "✗ " + (data.message || "Something went wrong. Please try again.");
          submitBtn.disabled = false;
          submitBtn.textContent = "Submit Inquiry";
        }
      })
      .catch((error) => {
        messageDiv.className = "lead-popup-message error";
        messageDiv.textContent = "✗ Network error. Please try again.";
        submitBtn.disabled = false;
        submitBtn.textContent = "Submit Inquiry";
      });
  }

  // Initialize popup
  function initPopup() {
    // Create popup element
    createPopup();

    // Setup event listeners
    const closeBtn = document.getElementById("lead-popup-close");
    const overlay = document.getElementById("lead-popup-overlay");
    const form = document.getElementById("lead-popup-form");

    // Close button click
    if (closeBtn) {
      closeBtn.addEventListener("click", closePopup);
    }

    // Click outside to close
    if (overlay) {
      overlay.addEventListener("click", function (e) {
        if (e.target === overlay) {
          closePopup();
        }
      });
    }

    // Form submission
    if (form) {
      form.addEventListener("submit", handleFormSubmit);
    }

    // Start the popup schedule
    scheduleNextPopup();
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initPopup);
  } else {
    initPopup();
  }
})();
