/**
 * Starts the "systems should work together" diagram animation only when the
 * diagram itself is on screen (not when the section's top edge first appears,
 * which happens well before the diagram is visible).
 *
 * - .is-armed holds the diagram in its starting state so it never flashes.
 * - .is-playing runs the animation, once.
 * - Without IntersectionObserver or with reduced motion, nothing is armed and
 *   the diagram is simply shown.
 */
(function () {
  'use strict';

  var diagram = document.querySelector('.sg-connected__diagram');
  if (!diagram || !('IntersectionObserver' in window)) {
    return;
  }

  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  diagram.classList.add('is-armed');

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          diagram.classList.add('is-playing');
          observer.unobserve(diagram);
        }
      });
    },
    // About 60% of the diagram must sit above the lower 12% of the viewport.
    { threshold: 0.6, rootMargin: '0px 0px -12% 0px' }
  );

  observer.observe(diagram);
})();
