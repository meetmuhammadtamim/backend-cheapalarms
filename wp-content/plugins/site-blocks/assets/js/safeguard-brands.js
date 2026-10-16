/**
 * Homepage brands showcase: logo tabs switch the stage.
 *
 * Click, keyboard (arrows, Home, End) and a short-delay hover on mouse. All
 * panels are already in the markup; this only moves the selection.
 */
(function () {
  'use strict';

  var root = document.querySelector('[data-sg-brands]');
  if (!root) {
    return;
  }

  var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
  var panels = Array.prototype.slice.call(root.querySelectorAll('[role="tabpanel"]'));
  if (tabs.length < 2 || tabs.length !== panels.length) {
    return;
  }

  var list = root.querySelector('[role="tablist"]');
  var stage = root.querySelector('.sg-brands-home__stage');
  var prevBtn = root.querySelector('[data-sg-brand-prev]');
  var nextBtn = root.querySelector('[data-sg-brand-next]');
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var current = 0;
  var hoverTimer = 0;

  // Bring the selected logo into view when the tab row scrolls sideways.
  function revealTab(tab) {
    if (!list || list.scrollWidth <= list.clientWidth) {
      return;
    }
    var l = list.getBoundingClientRect();
    var t = tab.getBoundingClientRect();
    list.scrollTo({
      left: list.scrollLeft + (t.left - l.left) - (l.width - t.width) / 2,
      behavior: reduced ? 'auto' : 'smooth'
    });
  }

  function select(index, focus) {
    if (index === current) {
      if (focus) {
        tabs[index].focus();
      }
      return;
    }
    tabs.forEach(function (tab, i) {
      var on = i === index;
      tab.classList.toggle('is-active', on);
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
      tab.setAttribute('tabindex', on ? '0' : '-1');
      panels[i].classList.toggle('is-active', on);
    });
    current = index;
    revealTab(tabs[index]);
    if (focus) {
      tabs[index].focus();
    }
  }

  function step(delta) {
    select((current + delta + tabs.length) % tabs.length, false);
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      step(-1);
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      step(1);
    });
  }

  // Swipe on the stage (touch only): a clear sideways move changes the brand.
  if (stage) {
    var startX = 0;
    var startY = 0;
    var tracking = false;
    stage.addEventListener('pointerdown', function (event) {
      if (event.pointerType !== 'touch') {
        return;
      }
      tracking = true;
      startX = event.clientX;
      startY = event.clientY;
    });
    stage.addEventListener('pointerup', function (event) {
      if (!tracking) {
        return;
      }
      tracking = false;
      var dx = event.clientX - startX;
      var dy = event.clientY - startY;
      if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.5) {
        step(dx < 0 ? 1 : -1);
      }
    });
    stage.addEventListener('pointercancel', function () {
      tracking = false;
    });
  }

  tabs.forEach(function (tab, i) {
    tab.addEventListener('click', function () {
      window.clearTimeout(hoverTimer);
      select(i, false);
    });

    // Hover switches after a short pause, so sweeping across the row does not flicker.
    tab.addEventListener('pointerenter', function (event) {
      if (event.pointerType !== 'mouse') {
        return;
      }
      window.clearTimeout(hoverTimer);
      hoverTimer = window.setTimeout(function () {
        select(i, false);
      }, 90);
    });

    tab.addEventListener('pointerleave', function () {
      window.clearTimeout(hoverTimer);
    });

    tab.addEventListener('keydown', function (event) {
      var next = -1;
      if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
        next = (i + 1) % tabs.length;
      } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
        next = (i - 1 + tabs.length) % tabs.length;
      } else if (event.key === 'Home') {
        next = 0;
      } else if (event.key === 'End') {
        next = tabs.length - 1;
      }
      if (next >= 0) {
        event.preventDefault();
        select(next, true);
      }
    });
  });
})();
