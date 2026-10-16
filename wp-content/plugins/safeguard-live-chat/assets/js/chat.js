function sgLiveChatBoot() {
  var cfg = window.sgChatConfig || {};
  var root = document.getElementById("sg-chat");
  var panel = document.getElementById("sg-chat-panel");
  var toggle = document.getElementById("sg-chat-toggle");
  var closeBtn = document.getElementById("sg-chat-close");
  var log = document.getElementById("sg-chat-messages");
  var form = document.getElementById("sg-chat-form");
  var input = document.getElementById("sg-chat-input");
  var send = document.getElementById("sg-chat-send");
  if (!root || !panel || !toggle || !log || !form || !input || !send) {
    return;
  }

  var storageKey = "slcSession";
  var session = null;
  var afterId = 0;
  var pollTimer = null;

  try {
    session = JSON.parse(window.sessionStorage.getItem(storageKey) || "null");
  } catch (e) {
    session = null;
  }

  function open(isOpen) {
    root.classList.toggle("is-open", isOpen);
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    panel.setAttribute("aria-hidden", isOpen ? "false" : "true");
    if (isOpen) {
      var focusTarget = session ? input : log.querySelector("input, textarea");
      if (focusTarget) {
        focusTarget.focus();
      }
    }
  }

  function addMsg(role, text) {
    var item = document.createElement("div");
    item.className = "sg-chat__msg sg-chat__msg--" + (role === "user" ? "user" : role === "agent" ? "agent" : "system");
    item.textContent = text;
    log.appendChild(item);
    log.scrollTop = log.scrollHeight;
  }

  function setComposer(visible) {
    form.hidden = !visible;
  }

  function showStartForm() {
    log.innerHTML = "";
    var box = document.createElement("form");
    box.className = "sg-chat__lead";
    box.innerHTML =
      '<p class="sg-chat__intro">Leave your details and a message, and a member of our team will reply here in this chat.</p>' +
      '<p class="sg-chat__note">If you don\'t hear back within 10 minutes, we will reply by email within 24 hours.</p>' +
      '<label class="sg-chat__field">Name<input class="sg-chat__field-input" name="name" required maxlength="120" autocomplete="name"></label>' +
      '<label class="sg-chat__field">Email<input class="sg-chat__field-input" name="email" type="email" required maxlength="190" autocomplete="email"></label>' +
      '<label class="sg-chat__field">Phone<input class="sg-chat__field-input" name="phone" required maxlength="40" autocomplete="tel"></label>' +
      '<label class="sg-chat__field">Message<textarea class="sg-chat__field-input" name="message" required maxlength="2000" rows="3"></textarea></label>' +
      '<button class="sg-chat__lead-submit" type="submit">Send Message</button>' +
      '<p class="sg-chat__error" data-error hidden></p>';
    box.addEventListener("submit", onStart);
    log.appendChild(box);
    setComposer(false);
  }

  function onStart(event) {
    event.preventDefault();
    var data = new FormData(event.currentTarget);
    var error = event.currentTarget.querySelector("[data-error]");
    var button = event.currentTarget.querySelector("button");
    button.disabled = true;
    fetch(cfg.startUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        name: String(data.get("name") || ""),
        email: String(data.get("email") || ""),
        phone: String(data.get("phone") || ""),
        message: String(data.get("message") || ""),
        page: cfg.page || window.location.pathname
      })
    })
      .then(function (res) { return res.json().then(function (body) { return { ok: res.ok, body: body }; }); })
      .then(function (result) {
        if (!result.ok || !result.body.ok) {
          throw new Error(result.body.error || "Could not start the chat.");
        }
        session = { key: result.body.sessionKey };
        window.sessionStorage.setItem(storageKey, JSON.stringify(session));
        log.innerHTML = "";
        addMsg("user", String(data.get("message") || ""));
        addMsg("system", result.body.reply || "Thanks, your message has been sent. A member of our team will reply here.");
        setComposer(true);
        startPoll();
      })
      .catch(function (err) {
        error.hidden = false;
        error.textContent = err.message || "Could not start the chat. Please call 1300 225 276.";
        button.disabled = false;
      });
  }

  function startPoll() {
    if (pollTimer) {
      window.clearInterval(pollTimer);
    }
    pollTimer = window.setInterval(poll, 4000);
    poll();
  }

  function poll() {
    if (!session || !session.key) {
      return;
    }
    fetch(cfg.pollUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ sessionKey: session.key, afterId: afterId })
    })
      .then(function (res) { return res.json(); })
      .then(function (body) {
        if (!body || !body.ok) {
          return;
        }
        (body.messages || []).forEach(function (msg) {
          afterId = Math.max(afterId, Number(msg.id) || 0);
          addMsg(msg.role === "agent" ? "agent" : "system", msg.content || "");
        });
        if (body.status === "resolved" || body.status === "timed_out") {
          setComposer(false);
          window.clearInterval(pollTimer);
        }
      })
      .catch(function () {});
  }

  // The message box grows with the text (up to its CSS max-height).
  function autosize() {
    input.style.height = "auto";
    input.style.height = input.scrollHeight + "px";
  }

  input.addEventListener("input", autosize);

  // Enter sends the message. Shift+Enter adds a new line.
  input.addEventListener("keydown", function (event) {
    if (event.key !== "Enter" || event.shiftKey || event.isComposing) {
      return;
    }
    event.preventDefault();
    if (!input.value.trim()) {
      return;
    }
    if (typeof form.requestSubmit === "function") {
      form.requestSubmit();
    } else {
      form.dispatchEvent(new Event("submit", { cancelable: true, bubbles: true }));
    }
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    if (!session || !session.key) {
      return;
    }
    var text = input.value.trim();
    if (!text) {
      return;
    }
    send.disabled = true;
    fetch(cfg.messageUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ sessionKey: session.key, message: text })
    })
      .then(function (res) { return res.json().then(function (body) { return { ok: res.ok, body: body }; }); })
      .then(function (result) {
        if (!result.ok || !result.body.ok) {
          throw new Error((result.body && result.body.error) || "Message was not sent.");
        }
        addMsg("user", text);
        input.value = "";
        autosize();
      })
      .catch(function (err) {
        addMsg("system", err.message || "Message was not sent. Please call 1300 225 276.");
      })
      .finally(function () {
        send.disabled = false;
      });
  });

  toggle.addEventListener("click", function () {
    open(!root.classList.contains("is-open"));
  });
  if (closeBtn) {
    closeBtn.addEventListener("click", function () { open(false); });
  }
  var newer = document.getElementById("sg-chat-new");
  if (newer) {
    newer.addEventListener("click", function () {
      session = null;
      afterId = 0;
      window.sessionStorage.removeItem(storageKey);
      if (pollTimer) {
        window.clearInterval(pollTimer);
      }
      showStartForm();
    });
  }

  if (session && session.key) {
    setComposer(true);
    addMsg("system", "You are connected. New replies from the team show up here.");
    startPoll();
  } else {
    showStartForm();
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", sgLiveChatBoot);
} else {
  sgLiveChatBoot();
}
