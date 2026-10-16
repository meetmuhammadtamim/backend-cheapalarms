/**
 * Hero depth, scrubbed to scroll (reverses with it, skipped for reduced motion).
 *
 * - The cabinet photo grows about 22% over the first few hundred pixels of
 *   scroll, anchored at its bottom edge so no gap opens beneath it.
 * - From 960px, where the copy sits over the photo, the copy lifts slightly
 *   faster than the page so the layers separate.
 */
(function () {
  'use strict';

  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  var hero = document.querySelector('.sg-rack-hero');
  var img = hero && hero.querySelector('.sg-rack-hero__img');
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!img || reduced || !gsap || !ScrollTrigger) {
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  var scrub = { trigger: hero, start: 0, end: '+=560', scrub: 0.5 };

  gsap.fromTo(
    img,
    { scale: 1, transformOrigin: '62% 100%' },
    { scale: 1.22, ease: 'none', scrollTrigger: scrub }
  );

  gsap.matchMedia().add('(min-width: 960px)', function () {
    gsap.fromTo(
      hero,
      { '--sg-hero-lift': '0px' },
      { '--sg-hero-lift': '-56px', ease: 'none', scrollTrigger: scrub }
    );
  });
})();
