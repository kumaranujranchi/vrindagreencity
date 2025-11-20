// Chatbot Widget v2.0 - Fixed Lead Submission
(function () {
  "use strict";

  console.log("🤖 Chatbot v2.0 loaded - Using endpoint: /inc/chatbot-lead.php");

  // Chatbot conversation flow
  const conversation = {
    started: false,
    step: 0,
    userData: {
      name: "",
      email: "",
      phone: "",
      subject: "",
      message: "",
    },
  };

  // Create chatbot HTML
  function createChatbot() {
    const chatbotHTML = `
      <div class="chatbot-widget" id="chatbot-widget">
        <!-- Tooltip -->
        <div class="chatbot-tooltip show" id="chatbot-tooltip">
          <div class="chatbot-tooltip-text">
            <span class="tooltip-online-dot"></span>
            We are online
          </div>
        </div>
        
        <!-- Chat Button -->
        <button class="chatbot-button" id="chatbot-button">
          <i class="fas fa-comments"></i>
          <span class="online-indicator"></span>
        </button>
        
        <!-- Chat Window -->
        <div class="chatbot-window" id="chatbot-window">
          <div class="chatbot-header">
            <div class="chatbot-header-info">
              <div class="chatbot-avatar">🏡</div>
              <div class="chatbot-header-text">
                <h3>Vrinda Green City</h3>
                <div class="chatbot-status">
                  <span class="status-dot"></span>
                  <span>Online</span>
                </div>
              </div>
            </div>
            <button class="chatbot-close" id="chatbot-close">&times;</button>
          </div>
          
          <div class="chatbot-messages" id="chatbot-messages"></div>
          
          <div class="chatbot-input-area">
            <form class="chatbot-input-form" id="chatbot-form">
              <input 
                type="text" 
                class="chatbot-input" 
                id="chatbot-input" 
                placeholder="Type your message..."
                autocomplete="off"
              />
              <button type="submit" class="chatbot-send" id="chatbot-send">
                <i class="fas fa-paper-plane"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML("beforeend", chatbotHTML);
  }

  // Show typing indicator
  function showTyping() {
    const messagesContainer = document.getElementById("chatbot-messages");
    const typingHTML = `
      <div class="chat-message message-bot" id="typing-indicator">
        <div class="bot-avatar">🤖</div>
        <div class="typing-indicator">
          <div class="typing-dots">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
        </div>
      </div>
    `;
    messagesContainer.insertAdjacentHTML("beforeend", typingHTML);
    scrollToBottom();
  }

  // Remove typing indicator
  function removeTyping() {
    const typingIndicator = document.getElementById("typing-indicator");
    if (typingIndicator) {
      typingIndicator.remove();
    }
  }

  // Add bot message
  function addBotMessage(message, showQuickReplies = false, replies = []) {
    const messagesContainer = document.getElementById("chatbot-messages");
    const time = new Date().toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    });

    let quickRepliesHTML = "";
    if (showQuickReplies && replies.length > 0) {
      const repliesButtons = replies
        .map(
          (reply) =>
            `<button class="quick-reply-btn" data-reply="${reply}">${reply}</button>`
        )
        .join("");
      quickRepliesHTML = `<div class="quick-replies">${repliesButtons}</div>`;
    }

    const messageHTML = `
      <div class="chat-message message-bot">
        <div class="bot-avatar">🤖</div>
        <div class="message-content">
          <div class="message-bubble">${message}</div>
          ${quickRepliesHTML}
          <div class="message-time">${time}</div>
        </div>
      </div>
    `;

    messagesContainer.insertAdjacentHTML("beforeend", messageHTML);
    scrollToBottom();

    // Add event listeners to quick reply buttons
    if (showQuickReplies) {
      document.querySelectorAll(".quick-reply-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
          handleQuickReply(this.dataset.reply);
        });
      });
    }
  }

  // Add user message
  function addUserMessage(message) {
    const messagesContainer = document.getElementById("chatbot-messages");
    const time = new Date().toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    });

    const messageHTML = `
      <div class="chat-message message-user">
        <div class="message-content">
          <div class="message-bubble">${message}</div>
          <div class="message-time">${time}</div>
        </div>
      </div>
    `;

    messagesContainer.insertAdjacentHTML("beforeend", messageHTML);
    scrollToBottom();
  }

  // Scroll to bottom
  function scrollToBottom() {
    const messagesContainer = document.getElementById("chatbot-messages");
    setTimeout(() => {
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
  }

  // Handle conversation flow
  function handleConversation(userInput) {
    const step = conversation.step;

    switch (step) {
      case 0: // Welcome
        conversation.step++;
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage("Hello! 👋 Welcome to Vrinda Green City!");
            setTimeout(() => {
              showTyping();
              setTimeout(() => {
                removeTyping();
                addBotMessage(
                  "We offer premium residential plots in Bihta with clear titles and modern amenities."
                );
                setTimeout(() => {
                  showTyping();
                  setTimeout(() => {
                    removeTyping();
                    addBotMessage("May I know your name?");
                    conversation.step++;
                  }, 800);
                }, 500);
              }, 1000);
            }, 500);
          }, 1000);
        }, 500);
        break;

      case 1: // Name
        conversation.userData.name = userInput;
        const userName = userInput;
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage("Nice to meet you, " + userName + "! 🙂");
            setTimeout(() => {
              showTyping();
              setTimeout(() => {
                removeTyping();
                addBotMessage(userName + ", how can I help you today?", true, [
                  "View Plots",
                  "Book Site Visit",
                  "Get Price Info",
                  "Talk to Agent",
                ]);
                conversation.step++;
              }, 800);
            }, 500);
          }, 1000);
        }, 500);
        break;

      case 2: // Interest selection
        conversation.userData.subject = userInput;
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage(
              "Great! I'd love to help you with that, " +
                conversation.userData.name +
                ". 😊"
            );
            setTimeout(() => {
              showTyping();
              setTimeout(() => {
                removeTyping();
                addBotMessage("Could you please share your email address?");
                conversation.step++;
              }, 800);
            }, 500);
          }, 1000);
        }, 500);
        break;

      case 3: // Email
        if (!validateEmail(userInput)) {
          setTimeout(() => {
            showTyping();
            setTimeout(() => {
              removeTyping();
              addBotMessage("Please enter a valid email address. 📧");
            }, 800);
          }, 500);
          return;
        }
        conversation.userData.email = userInput;
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage("Thank you! What's your phone number?");
            conversation.step++;
          }, 1000);
        }, 500);
        break;

      case 4: // Phone
        if (!validatePhone(userInput)) {
          setTimeout(() => {
            showTyping();
            setTimeout(() => {
              removeTyping();
              addBotMessage("Please enter a valid phone number. 📱");
            }, 800);
          }, 500);
          return;
        }
        conversation.userData.phone = userInput;
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage(
              "Perfect, " +
                conversation.userData.name +
                "! Is there anything specific you'd like to know or any message you'd like to share?"
            );
            conversation.step++;
          }, 1000);
        }, 500);
        break;

      case 5: // Message
        conversation.userData.message = userInput;
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage(
              "Thank you for providing all the details, " +
                conversation.userData.name +
                "! 🎉"
            );
            setTimeout(() => {
              showTyping();
              setTimeout(() => {
                removeTyping();
                addBotMessage(
                  "Our team will contact you shortly. Have a great day! 😊"
                );
                conversation.step++;
                submitLead();
              }, 1000);
            }, 500);
          }, 1000);
        }, 500);
        break;

      default:
        setTimeout(() => {
          showTyping();
          setTimeout(() => {
            removeTyping();
            addBotMessage(
              "Thank you! We've already received your information. Our team will be in touch soon! 👍"
            );
          }, 1000);
        }, 500);
    }
  }

  // Handle quick reply
  function handleQuickReply(reply) {
    addUserMessage(reply);
    handleConversation(reply);
  }

  // Validate email
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // Validate phone
  function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]{10,}$/;
    return re.test(phone);
  }

  // Submit lead to backend
  function submitLead() {
    console.log("=== CHATBOT LEAD SUBMISSION STARTED ===");
    
    const formData = new FormData();
    formData.append("name", conversation.userData.name);
    formData.append("email", conversation.userData.email);
    formData.append("phone", conversation.userData.phone);
    formData.append("subject", conversation.userData.subject);
    formData.append(
      "message",
      conversation.userData.message || "Inquiry via chatbot"
    );

    // Log the data being sent
    console.log("Lead data to submit:", {
      name: conversation.userData.name,
      email: conversation.userData.email,
      phone: conversation.userData.phone,
      subject: conversation.userData.subject,
      message: conversation.userData.message
    });
    
    // Use dedicated chatbot endpoint
    const apiPath = window.location.origin + "/inc/chatbot-lead.php";
    console.log("Sending POST request to:", apiPath);

    fetch(apiPath, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        console.log("Response received!");
        console.log("Response status:", response.status);
        console.log("Response OK:", response.ok);
        
        return response.text().then(text => {
          console.log("Raw server response:", text);
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error("Failed to parse JSON:", text);
            throw new Error("Server returned non-JSON response: " + text);
          }
        });
      })
      .then((data) => {
        console.log("Parsed response:", data);
        
        if (data.type === 'success') {
            console.log("✅ ✅ ✅ LEAD SUBMITTED SUCCESSFULLY! ✅ ✅ ✅");
            console.log("Lead ID:", data.lead_id);
            console.log("Response message:", data.message);
            console.log("Saved data:", data.data);
        } else {
            console.error("❌ Server reported error:", data.message);
            if (data.error) {
                console.error("Error details:", data.error);
            }
        }
      })
      .catch((error) => {
        console.error("❌ ❌ ❌ Error submitting lead ❌ ❌ ❌");
        console.error("Error details:", error);
        console.error("Error message:", error.message);
      });
  }

  // Initialize chatbot
  function initChatbot() {
    createChatbot();

    const chatbotButton = document.getElementById("chatbot-button");
    const chatbotWindow = document.getElementById("chatbot-window");
    const chatbotClose = document.getElementById("chatbot-close");
    const chatbotForm = document.getElementById("chatbot-form");
    const chatbotInput = document.getElementById("chatbot-input");
    const chatbotTooltip = document.getElementById("chatbot-tooltip");

    // Show tooltip for 5 seconds, then hide
    setTimeout(() => {
      chatbotTooltip.classList.remove("show");
    }, 5000);

    // Toggle chat window
    chatbotButton.addEventListener("click", function () {
      chatbotWindow.classList.toggle("open");
      chatbotTooltip.classList.remove("show");

      if (chatbotWindow.classList.contains("open")) {
        chatbotInput.focus();
        if (!conversation.started) {
          conversation.started = true;
          handleConversation("");
        }
      }
    });

    // Close chat
    chatbotClose.addEventListener("click", function () {
      chatbotWindow.classList.remove("open");
    });

    // Handle form submission
    chatbotForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const message = chatbotInput.value.trim();

      if (message) {
        addUserMessage(message);
        chatbotInput.value = "";
        handleConversation(message);
      }
    });
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initChatbot);
  } else {
    initChatbot();
  }
})();
