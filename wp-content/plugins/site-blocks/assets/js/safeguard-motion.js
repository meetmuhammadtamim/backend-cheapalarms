/**
 * Scroll reveals for the homepage sections.
 *
 * Elements marked [data-sg-reveal] fade/slide in once, when they scroll into
 * view. Children of [data-sg-stagger] get an index so they enter in sequence.
 *
 * Nothing is hidden unless this script runs (the html element only gets
 * .sg-motion-ready here), so no-JS and reduced-motion visitors see all content.
 */
(function () {
  'use strict';

  var items = document.querySelectorAll('[data-sg-reveal]');
  if (!items.length || !('IntersectionObserver' in window)) {
    return;
  }

  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  // Stagger order inside each group.
  Array.prototype.forEach.call(document.querySelectorAll('[data-sg-stagger]'), function (group) {
    var children = group.querySelectorAll('[data-sg-reveal]');
    Array.prototype.forEach.call(children, function (child, index) {
      child.style.setProperty('--sg-i', String(index));
    });
  });

  document.documentElement.classList.add('sg-motion-ready');

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          observer.unobserve(entry.target);
        }
      });
    },
    // Fire once the element is clearly on screen: its top has to rise above the
    // lower quarter of the viewport, not merely touch the bottom edge.
    { threshold: 0, rootMargin: '0px 0px -25% 0px' }
  );

  Array.prototype.forEach.call(items, function (item) {
    observer.observe(item);
  });
})();
