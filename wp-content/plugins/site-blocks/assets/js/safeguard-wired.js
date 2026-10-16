/**
 * "Systems should work together", scroll-built version (GSAP).
 *
 * 1. Build: the stage pins and the visitor's scroll position drives one
 *    timeline. Devices arrive, pipes draw into the infrastructure, then out to
 *    the single view, and the result card lands. Scrolling back un-builds it.
 *    If the stage is taller than the screen it is not pinned; the same
 *    timeline is then scrubbed as the diagram crosses the viewport.
 * 2. Signals: once built, pulses travel the finished pipes along their paths
 *    (MotionPath) and loop while the diagram is on screen.
 * 3. Explore: hovering a node lights the path it belongs to (orange) and dims
 *    the rest, with a pulse running only along that path.
 *
 * Desktop widths with motion allowed only. Elsewhere the static diagram (or,
 * below 900px, the text alone) is what the visitor gets.
 */
(function () {
  'use strict';

  var section = document.querySelector('[data-sg-wired]');
  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  if (!section || !gsap || !ScrollTrigger || !window.DrawSVGPlugin || !window.MotionPathPlugin) {
    return;
  }

  gsap.registerPlugin(ScrollTrigger, window.DrawSVGPlugin, window.MotionPathPlugin);

  var stage = section.querySelector('[data-sg-stage]');
  var diagram = section.querySelector('.sg-wired__diagram');
  var svg = diagram && diagram.querySelector('.sg-wired__pipes');
  if (!stage || !diagram || !svg) {
    return;
  }

  var SVG_NS = 'http://www.w3.org/2000/svg';
  // Device i travels its own pipe into the infrastructure, then pipe 5 or 6.
  var CHAINS = [[1, 5], [2, 5], [3, 6], [4, 6]];

  function all(selector) {
    return Array.prototype.slice.call(diagram.querySelectorAll(selector));
  }

  var mm = gsap.matchMedia();

  mm.add('(min-width: 900px) and (prefers-reduced-motion: no-preference)', function () {
    var cols = all('.sg-wired__col');
    var devices = all('.sg-wired__node--device');
    var cores = all('.sg-wired__node--core');
    var nodes = all('.sg-wired__node');
    var result = diagram.querySelector('.sg-wired__node--result');
    var spark = diagram.querySelector('.sg-wired__spark');
    var pipes = [1, 2, 3, 4, 5, 6].map(function (n) {
      return diagram.querySelector('.sg-wired__pipe--' + n);
    });
    var srcDots = all('.sg-wired__dot--src');
    var mergeDots = all('.sg-wired__dot--merge');
    var outDots = all('.sg-wired__dot--out');
    var endDot = diagram.querySelector('.sg-wired__dot--end');
    var pulseLayer = svg.querySelector('.sg-wired__pulses');

    // ---- Signal pulses (decorative circles, created here) ----
    var pulses = CHAINS.map(function () {
      return [0, 1].map(function () {
        var c = document.createElementNS(SVG_NS, 'circle');
        c.setAttribute('class', 'sg-wired__pulse');
        c.setAttribute('r', '5');
        c.style.opacity = '0';
        pulseLayer.appendChild(c);
        return c;
      });
    });
    var allPulses = [].concat.apply([], pulses);

    function chainTimeline(indexes, gap, repeatDelay) {
      var t = gsap.timeline({ paused: true, repeat: -1, repeatDelay: repeatDelay });
      indexes.forEach(function (ci, k) {
        var at = k * gap;
        CHAINS[ci].forEach(function (pipeNo, leg) {
          var pulse = pulses[ci][leg];
          var start = at + leg * 1.15;
          t.set(pulse, { opacity: 1 }, start)
            .to(pulse, {
              motionPath: { path: pipes[pipeNo - 1], align: pipes[pipeNo - 1], alignOrigin: [0.5, 0.5] },
              duration: 1.15,
              ease: 'power1.inOut'
            }, start)
            .set(pulse, { opacity: 0 }, start + 1.15);
        });
      });
      return t;
    }

    var ambient = chainTimeline([0, 1, 2, 3], 0.35, 1.4);
    var solo = null;

    // ---- State: building / built / visible / hovering ----
    var built = false;
    var visible = false;
    var hovering = false;

    function sync() {
      if (built && visible && !hovering) {
        ambient.play();
      } else {
        ambient.pause();
        if (!hovering) {
          gsap.set(allPulses, { opacity: 0 });
        }
      }
      diagram.classList.toggle('is-explorable', built);
    }

    // ---- The build timeline ----
    var ease = 'power2.out';
    var tl = gsap.timeline({ defaults: { ease: ease } });

    tl.fromTo(cols[0], { autoAlpha: 0, y: 10 }, { autoAlpha: 1, y: 0, duration: 0.6 }, 0)
      .fromTo(devices, { autoAlpha: 0, x: -18 }, { autoAlpha: 1, x: 0, duration: 0.6, stagger: 0.15 }, 0.15)
      .fromTo(srcDots, { scale: 0, autoAlpha: 0, transformOrigin: '50% 50%' }, { scale: 1, autoAlpha: 1, duration: 0.35, stagger: 0.15 }, 0.9)
      .fromTo(pipes.slice(0, 4), { drawSVG: '0%', autoAlpha: 0 }, { drawSVG: '100%', autoAlpha: 1, duration: 1.2, stagger: 0.18, ease: 'power1.inOut' }, 1.1)
      .fromTo(cols[1], { autoAlpha: 0, y: 10 }, { autoAlpha: 1, y: 0, duration: 0.6 }, 2.3)
      .fromTo(mergeDots, { scale: 0, autoAlpha: 0, transformOrigin: '50% 50%' }, { scale: 1, autoAlpha: 1, duration: 0.35, stagger: 0.08 }, 2.4)
      .fromTo(cores, { autoAlpha: 0, y: 14 }, { autoAlpha: 1, y: 0, duration: 0.7, stagger: 0.25 }, 2.5)
      .fromTo(outDots, { scale: 0, autoAlpha: 0, transformOrigin: '50% 50%' }, { scale: 1, autoAlpha: 1, duration: 0.35, stagger: 0.12 }, 3.6)
      .fromTo(pipes.slice(4), { drawSVG: '0%', autoAlpha: 0 }, { drawSVG: '100%', autoAlpha: 1, duration: 1.3, stagger: 0.18, ease: 'power1.inOut' }, 3.7)
      .fromTo(cols[2], { autoAlpha: 0, y: 10 }, { autoAlpha: 1, y: 0, duration: 0.6 }, 4.4)
      .fromTo(endDot, { scale: 0, autoAlpha: 0, transformOrigin: '50% 50%' }, { scale: 1, autoAlpha: 1, duration: 0.4 }, 5.0)
      .fromTo(result, { autoAlpha: 0, scale: 0.96, transformOrigin: '50% 50%' }, { autoAlpha: 1, scale: 1, duration: 0.8 }, 5.0)
      .fromTo(spark, { autoAlpha: 0, scale: 0 }, { autoAlpha: 1, scale: 1, duration: 0.4 }, 5.7)
      .to({}, { duration: 0.8 }, 5.7); // a beat of rest before the stage releases

    // ---- Scroll: pin if the stage fits the screen, else scrub in place ----
    function onUpdate(self) {
      var done = self.progress > 0.995;
      if (done !== built) {
        built = done;
        if (!built) {
          clearExplore();
        }
        sync();
      }
    }

    var fits = stage.offsetHeight <= window.innerHeight - 110;
    var buildTrigger = ScrollTrigger.create(fits
      ? {
        trigger: stage,
        start: 'top 90px',
        end: function () { return '+=' + Math.round(window.innerHeight * 1.3); },
        pin: true,
        anticipatePin: 1,
        scrub: 0.6,
        invalidateOnRefresh: true,
        animation: tl,
        onUpdate: onUpdate
      }
      : {
        trigger: diagram,
        start: 'top 80%',
        end: 'center 45%',
        scrub: 0.6,
        animation: tl,
        onUpdate: onUpdate
      });

    var viewTrigger = ScrollTrigger.create({
      trigger: diagram,
      start: 'top bottom',
      end: 'bottom top',
      onToggle: function (self) {
        visible = self.isActive;
        sync();
      }
    });

    // ---- Explore: light a node's path ----
    function intersects(a, b) {
      return a.some(function (n) { return b.indexOf(n) !== -1; });
    }

    function pipesOf(node) {
      return (node.getAttribute('data-pipes') || '').split(' ').filter(Boolean).map(Number);
    }

    function clearExplore() {
      hovering = false;
      if (solo) {
        solo.kill();
        solo = null;
      }
      nodes.forEach(function (n) { n.classList.remove('is-dim'); });
      pipes.forEach(function (p) { p.classList.remove('is-lit', 'is-dim'); });
      gsap.set(allPulses, { opacity: 0 });
    }

    function enter(node) {
      if (!built) {
        return;
      }
      var own = pipesOf(node);
      var chosen = [];
      CHAINS.forEach(function (chain, ci) {
        if (own.indexOf(chain[0]) !== -1) {
          chosen.push(ci);
        }
      });
      if (!chosen.length) {
        return;
      }
      var lit = [];
      chosen.forEach(function (ci) {
        CHAINS[ci].forEach(function (n) { if (lit.indexOf(n) === -1) { lit.push(n); } });
      });
      var firsts = chosen.map(function (ci) { return CHAINS[ci][0]; });

      hovering = true;
      ambient.pause();
      gsap.set(allPulses, { opacity: 0 });

      pipes.forEach(function (p, i) {
        var on = lit.indexOf(i + 1) !== -1;
        p.classList.toggle('is-lit', on);
        p.classList.toggle('is-dim', !on);
      });
      nodes.forEach(function (n) {
        var np = pipesOf(n);
        var isDevice = n.classList.contains('sg-wired__node--device');
        var inPath = isDevice ? firsts.indexOf(np[0]) !== -1 : intersects(np, lit);
        n.classList.toggle('is-dim', !inPath);
      });

      if (solo) {
        solo.kill();
      }
      solo = chainTimeline(chosen, 0.35, 0.5);
      solo.play();
    }

    function leave() {
      if (!hovering) {
        return;
      }
      clearExplore();
      sync();
    }

    var handlers = nodes.map(function (node) {
      var over = function () { enter(node); };
      node.addEventListener('mouseenter', over);
      node.addEventListener('mouseleave', leave);
      return { node: node, over: over };
    });

    // Runs when the media query stops matching or the page tears down.
    return function () {
      handlers.forEach(function (h) {
        h.node.removeEventListener('mouseenter', h.over);
        h.node.removeEventListener('mouseleave', leave);
      });
      clearExplore();
      ambient.kill();
      tl.kill();
      buildTrigger.kill();
      viewTrigger.kill();
      diagram.classList.remove('is-explorable');
      if (pulseLayer) {
        pulseLayer.innerHTML = '';
      }
      gsap.set(cols.concat(devices, cores, nodes, pipes, srcDots, mergeDots, outDots, [endDot, result, spark]), { clearProps: 'all' });
    };
  });
})();
