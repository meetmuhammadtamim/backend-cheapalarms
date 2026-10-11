(function () {
  var bar = document.getElementById("sg-read-progress");
  var article = document.getElementById("sg-read");

  function coveredHeight() {
    var admin = document.getElementById("wpadminbar");
    var header = document.querySelector(".sg-header");
    var adminH = admin ? admin.offsetHeight : 0;
    var headerH = header ? header.offsetHeight : 0;
    return adminH + headerH + 28;
  }

  function applyStickyOffset() {
    var gap = coveredHeight() + "px";
    document.documentElement.style.scrollPaddingTop = "0px";
    document.documentElement.style.scrollBehavior = "auto";
    document.documentElement.style.setProperty("--sg-sticky-top", gap);
    document.querySelectorAll(".sg-blog__content h2, .sg-blog__content h3, .sg-study__content h2").forEach(function (heading) {
      heading.style.scrollMarginTop = gap;
    });
  }

  function scrollToHeading(id) {
    var heading = document.getElementById(id);
    if (!heading) {
      return;
    }
    var gap = coveredHeight();
    var target = Math.max(0, heading.getBoundingClientRect().top + window.pageYOffset - gap);
    var start = window.pageYOffset;
    var change = target - start;
    var duration = 480;
    var t0 = null;
    function step(now) {
      if (t0 === null) {
        t0 = now;
      }
      var p = Math.min(1, (now - t0) / duration);
      var eased = 1 - Math.pow(1 - p, 3);
      window.scrollTo(0, start + change * eased);
      if (p < 1) {
        window.requestAnimationFrame(step);
        return;
      }
      window.scrollTo(0, target);
      if (window.history && window.history.replaceState) {
        window.history.replaceState(null, "", "#" + id);
      }
    }
    window.requestAnimationFrame(step);
  }

  applyStickyOffset();
  window.addEventListener("resize", applyStickyOffset);

  document.addEventListener(
    "click",
    function (event) {
      var link = event.target.closest(".sg-blog__toc a, .sg-blog__toc-mobile a, .sg-study__jump a");
      if (!link) {
        return;
      }
      var id = decodeURIComponent((link.getAttribute("href") || "").replace(/^#/, ""));
      if (!id || !document.getElementById(id)) {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      applyStickyOffset();
      scrollToHeading(id);
    },
    true
  );

  if (bar && article) {
    var paint = function () {
      var rect = article.getBoundingClientRect();
      var total = article.offsetHeight - window.innerHeight;
      var seen = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
      var ratio = total > 0 ? seen / total : 0;
      bar.style.transform = "scaleX(" + ratio + ")";
    };
    paint();
    window.addEventListener("scroll", paint, { passive: true });
    window.addEventListener("resize", paint);
  }

  var links = document.querySelectorAll(".sg-blog__toc a");
  if (links.length) {
    var mark = function () {
      var line = coveredHeight();
      var current = null;
      links.forEach(function (link) {
        var id = (link.getAttribute("href") || "").replace(/^#/, "");
        var heading = id ? document.getElementById(id) : null;
        if (!heading) {
          return;
        }
        if (heading.getBoundingClientRect().top <= line + 4) {
          current = link;
        }
      });
      links.forEach(function (link) {
        link.classList.toggle("is-active", link === current);
      });
    };
    mark();
    window.addEventListener("scroll", mark, { passive: true });
  }

  document.querySelectorAll("[data-sg-copy]").forEach(function (button) {
    button.addEventListener("click", function () {
      var url = button.getAttribute("data-sg-copy") || "";
      var done = function () {
        var previous = button.textContent;
        if (button.childElementCount) {
          button.setAttribute("aria-label", "Copied");
          window.setTimeout(function () {
            button.setAttribute("aria-label", "Copy link");
          }, 1600);
          return;
        }
        button.textContent = "Copied";
        window.setTimeout(function () {
          button.textContent = previous;
        }, 1600);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(done);
        return;
      }
      done();
    });
  });
})();
