(function () {
  'use strict';

  var rails = document.querySelectorAll('[data-sg-rail]');
  var i;

  function stepSize(track) {
    var card = track.firstElementChild;
    if (!card) {
      return 0;
    }
    var styles = window.getComputedStyle(track);
    var gap = parseFloat(styles.columnGap || styles.gap) || 0;
    return card.getBoundingClientRect().width + gap;
  }

  function bind(rail) {
    var track = rail.querySelector('.sg-tech__rail-track');
    var view = rail.querySelector('.sg-tech__rail-view');
    var prev = rail.querySelector('.sg-tech__rail-nav--prev');
    var next = rail.querySelector('.sg-tech__rail-nav--next');
    var index = 0;

    if (!track || !view || !prev || !next) {
      return;
    }

    function innerWidthOf(view) {
      var styles = window.getComputedStyle(view);
      var pad = (parseFloat(styles.paddingLeft) || 0) + (parseFloat(styles.paddingRight) || 0);
      return view.clientWidth - pad;
    }

    function maxIndex() {
      var distance = stepSize(track);
      if (!distance) {
        return 0;
      }
      var visible = Math.max(1, Math.floor(innerWidthOf(view) / distance));
      return Math.max(0, track.children.length - visible);
    }

    function go(nextIndex) {
      var limit = maxIndex();
      index = Math.max(0, Math.min(nextIndex, limit));
      track.style.transform = 'translateX(' + (-index * stepSize(track)) + 'px)';
      prev.disabled = index <= 0;
      next.disabled = index >= limit;
    }

    prev.addEventListener('click', function () {
      go(index - 1);
    });
    next.addEventListener('click', function () {
      go(index + 1);
    });
    window.addEventListener('resize', function () {
      go(index);
    });
    go(0);
  }

  for (i = 0; i < rails.length; i++) {
    bind(rails[i]);
  }
})();
