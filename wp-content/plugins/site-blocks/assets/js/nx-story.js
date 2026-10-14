(function () {
  'use strict';

  var root = document.querySelector('.sg-nx-story');
  if (!root || !('IntersectionObserver' in window)) {
    return;
  }

  var steps = root.querySelectorAll('.sg-nx-story__step');
  var shots = root.querySelectorAll('.sg-nx-story__frame img');
  var marks = root.querySelectorAll('.sg-nx-story__index li');
  var quiet = window.matchMedia('(max-width: 1024px), (prefers-reduced-motion: reduce)');
  var observer = null;
  var seen = [];

  function setActive(index) {
    var i;
    for (i = 0; i < shots.length; i++) {
      var on = i === index;
      shots[i].classList.toggle('is-active', on);
      if (on) {
        shots[i].removeAttribute('aria-hidden');
      } else {
        shots[i].setAttribute('aria-hidden', 'true');
      }
    }
    for (i = 0; i < marks.length; i++) {
      var current = i === index;
      marks[i].classList.toggle('is-active', current);
      if (current) {
        marks[i].setAttribute('aria-current', 'step');
      } else {
        marks[i].removeAttribute('aria-current');
      }
    }
  }

  function stop() {
    if (observer) {
      observer.disconnect();
      observer = null;
    }
  }

  function start() {
    stop();
    if (quiet.matches || !steps.length) {
      return;
    }

    seen = [];
    Array.prototype.forEach.call(steps, function () {
      seen.push(false);
    });
    setActive(0);

    observer = new IntersectionObserver(function (entries) {
      var next = -1;
      var i;
      entries.forEach(function (entry) {
        var step = entry.target.closest('.sg-nx-story__step');
        var index = step ? Number(step.getAttribute('data-step')) : -1;
        if (index >= 0) {
          seen[index] = entry.isIntersecting;
        }
      });
      for (i = seen.length - 1; i >= 0; i--) {
        if (seen[i]) {
          next = i;
          break;
        }
      }
      if (next >= 0) {
        setActive(next);
      }
    }, {
      root: null,
      rootMargin: '-45% 0px -45% 0px',
      threshold: 0
    });

    Array.prototype.forEach.call(steps, function (step) {
      var heading = step.querySelector('h2');
      if (heading) {
        observer.observe(heading);
      }
    });
  }

  if (typeof quiet.addEventListener === 'function') {
    quiet.addEventListener('change', start);
  }
  start();
})();
