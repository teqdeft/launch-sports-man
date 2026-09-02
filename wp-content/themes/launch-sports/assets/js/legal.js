/* Contents column: marks the section you are currently reading.
   No animation and no dependency on the motion layer - if GSAP fails to load,
   or someone has asked for reduced motion, this still works.

   Offsets are measured once and re-measured only when the layout can actually
   have changed: on resize, and once the webfonts have swapped in, which
   reflows a document this long by hundreds of pixels. That leaves the scroll
   handler as arithmetic over a cached array - no getBoundingClientRect per
   scroll, so no forced layout, and no need to throttle through rAF. */
(function () {
  'use strict';

  var links = Array.prototype.slice.call(document.querySelectorAll('.legal-toc-link'));
  if (links.length < 2) return;

  var targets = links.map(function (a) {
    var href = a.getAttribute('href');
    return href && href.charAt(0) === '#'
      ? document.getElementById(decodeURIComponent(href.slice(1)))
      : null;
  });

  /* Without this a heading only counts as current once it touches the very top
     of the window, which reads as lagging a section behind. */
  var LOOKAHEAD = 160;
  var tops = [];
  var active = -1;

  function measure() {
    var y = window.pageYOffset;
    tops = targets.map(function (t) {
      return t ? t.getBoundingClientRect().top + y : Infinity;
    });
  }

  function sync() {
    var y = window.pageYOffset + LOOKAHEAD;
    var current = 0;

    for (var i = 0; i < tops.length; i++) {
      if (tops[i] <= y) current = i;
    }

    /* The last section is often too short to ever reach the offset, so it would
       never light up. Once the page is scrolled to the end, it is the one. */
    if (window.innerHeight + window.pageYOffset >= document.documentElement.scrollHeight - 2) {
      current = links.length - 1;
    }

    if (current === active) return;
    if (active > -1) links[active].removeAttribute('aria-current');
    links[current].setAttribute('aria-current', 'true');
    active = current;
  }

  function remeasure() {
    measure();
    sync();
  }

  window.addEventListener('scroll', sync, { passive: true });
  window.addEventListener('resize', remeasure);
  window.addEventListener('load', remeasure);

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(remeasure);
  }

  remeasure();
})();
