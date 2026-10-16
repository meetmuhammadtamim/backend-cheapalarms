/**
 * Hero focus phrase ("Day One"): Rough Notation draws an orange underline sized
 * to the phrase's real box, once it is actually on screen after the headline's
 * own rise-in. With reduced motion the underline is shown without animating.
 */
(function () {
  'use strict';

  var focus = document.querySelector('[data-sg-focus]');
  if (!focus || !window.RoughNotation) {
    return;
  }

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var annotation = window.RoughNotation.annotate(focus, {
    type: 'underline',
    color: '#FB7523',
    strokeWidth: 3,
    padding: 1,
    iterations: 1,
    animate: !reduced,
    animationDuration: 800
  });

  function play() {
    // Rough Notation reads the box, so wait for the web font to settle.
    var ready = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
    ready.then(function () {
      window.setTimeout(function () {
        annotation.show();
      }, reduced ? 0 : 750);
    });
  }

  if (!('IntersectionObserver' in window)) {
    play();
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    if (entries.some(function (entry) { return entry.isIntersecting; })) {
      observer.disconnect();
      play();
    }
  }, { threshold: 0.6 });
  observer.observe(focus);
})();
