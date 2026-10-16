/**
 * Coverage section ("Good installations start before anyone drills a hole").
 *
 * - The headline rises line by line out of a mask, then the paragraph fades in.
 * - The photo wipes in from the right edge (the side it sits on) and, while the
 *   section crosses the viewport, drifts a little inside its rounded frame.
 *
 * Starts only when the elements are actually on screen. Without GSAP, or with
 * reduced motion, everything is simply shown.
 */
(function () {
  'use strict';

  var section = document.querySelector('[data-sg-coverage]');
  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!section || reduced || !gsap || !ScrollTrigger) {
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  var title = section.querySelector('.sg-coverage__title');
  var text = section.querySelector('.sg-coverage__text');
  var media = section.querySelector('.sg-coverage__media');
  var img = section.querySelector('.sg-coverage__img');

  // ---- Copy ----
  if (title && text) {
    var textTrigger = { trigger: title, start: 'top 80%', once: true };

    gsap.fromTo(
      text,
      { autoAlpha: 0, y: 16 },
      { autoAlpha: 1, y: 0, duration: 0.8, delay: 0.55, ease: 'power2.out', scrollTrigger: textTrigger }
    );

    if (window.SplitText) {
      gsap.registerPlugin(window.SplitText);
      gsap.set(title, { autoAlpha: 0 });
      var played = false;

      window.SplitText.create(title, {
        type: 'lines',
        mask: 'lines',
        autoSplit: true,
        onSplit: function (self) {
          gsap.set(title, { autoAlpha: 1 });
          if (played) {
            return;
          }
          return gsap.from(self.lines, {
            yPercent: 110,
            duration: 0.9,
            ease: 'power3.out',
            stagger: 0.12,
            scrollTrigger: { trigger: title, start: 'top 80%', once: true },
            onComplete: function () { played = true; }
          });
        }
      });
    } else {
      gsap.fromTo(
        title,
        { autoAlpha: 0, y: 16 },
        { autoAlpha: 1, y: 0, duration: 0.8, ease: 'power2.out', scrollTrigger: { trigger: title, start: 'top 80%', once: true } }
      );
    }
  }

  // ---- Photo ----
  if (media && img) {
    // The photo is held slightly oversized so it can drift without showing edges.
    gsap.set(img, { scale: 1.1, transformOrigin: '50% 50%' });

    gsap.timeline({
      defaults: { ease: 'power3.out' },
      scrollTrigger: { trigger: media, start: 'top 80%', once: true }
    })
      .fromTo(
        media,
        { clipPath: 'inset(0% 0% 0% 100%)' },
        { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.1, ease: 'power3.inOut', clearProps: 'clipPath' },
        0
      )
      .fromTo(img, { scale: 1.2 }, { scale: 1.1, duration: 1.3 }, 0);

    gsap.fromTo(
      img,
      { yPercent: -3 },
      {
        yPercent: 3,
        ease: 'none',
        scrollTrigger: { trigger: media, start: 'top bottom', end: 'bottom top', scrub: 0.6 }
      }
    );
  }
})();
