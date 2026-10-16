/**
 * "Details behind a reliable system" section.
 *
 * - Each scene's illustration is revealed with a curtain wipe from the ground
 *   up, and its title and copy follow; the orange rule under the title draws in.
 * - While scrolling past, each illustration drifts slightly against the page.
 * - One phrase in the lead is underlined (Rough Notation), like the hero.
 *
 * Everything starts only when the element is actually on screen. Without GSAP,
 * or with reduced motion, content is simply shown (the underline is static).
 */
(function () {
  'use strict';

  var section = document.querySelector('[data-sg-details]');
  if (!section) {
    return;
  }

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---- Underlined phrase in the lead ----
  var mark = section.querySelector('[data-sg-mark]');
  if (mark && window.RoughNotation) {
    var annotation = window.RoughNotation.annotate(mark, {
      type: 'underline',
      color: '#FB7523',
      strokeWidth: 2.5,
      padding: 2,
      iterations: 1,
      multiline: true,
      animate: !reduced,
      animationDuration: 700
    });

    var showMark = function () {
      var ready = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
      ready.then(function () {
        window.setTimeout(function () {
          annotation.show();
        }, reduced ? 0 : 700);
      });
    };

    if ('IntersectionObserver' in window) {
      var markObserver = new IntersectionObserver(function (entries) {
        if (entries.some(function (entry) { return entry.isIntersecting; })) {
          markObserver.disconnect();
          showMark();
        }
      }, { threshold: 0.9, rootMargin: '0px 0px -15% 0px' });
      markObserver.observe(mark);
    } else {
      showMark();
    }
  }

  // ---- Scene reveal and parallax ----
  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  if (reduced || !gsap || !ScrollTrigger) {
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  var items = section.querySelectorAll('.sg-details__item');
  Array.prototype.forEach.call(items, function (item) {
    var art = item.querySelector('.sg-details__art');
    var img = item.querySelector('.sg-details__img');
    var title = item.querySelector('.sg-details__item-title');
    var desc = item.querySelector('.sg-details__item-desc');
    if (!art || !img || !title || !desc) {
      return;
    }

    var reveal = gsap.timeline({
      defaults: { ease: 'power3.out' },
      scrollTrigger: { trigger: item, start: 'top 78%', once: true }
    });

    reveal
      .fromTo(
        art,
        { clipPath: 'inset(100% 0% 0% 0%)' },
        { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.1, ease: 'power3.inOut', clearProps: 'clipPath' },
        0
      )
      .fromTo(img, { scale: 1.12, transformOrigin: '50% 100%' }, { scale: 1, duration: 1.3 }, 0)
      .fromTo(title, { autoAlpha: 0, y: 16 }, { autoAlpha: 1, y: 0, duration: 0.7 }, 0.5)
      .fromTo(title, { '--sg-rule': 0 }, { '--sg-rule': 1, duration: 0.7, ease: 'power2.out' }, 0.85)
      .fromTo(desc, { autoAlpha: 0, y: 16 }, { autoAlpha: 1, y: 0, duration: 0.7 }, 0.62);

    // Slow drift while the scene crosses the viewport.
    gsap.fromTo(
      img,
      { yPercent: -2.5 },
      {
        yPercent: 2.5,
        ease: 'none',
        scrollTrigger: { trigger: item, start: 'top bottom', end: 'bottom top', scrub: 0.6 }
      }
    );
  });
})();
