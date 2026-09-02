/* Hamburger. Bewust los van de motion-laag: navigatie mag niet afhangen
   van GSAP. Sluit op Escape, op een link, en bij het passeren van 1024px. */
(function () {
  var knop = document.querySelector('.burger');
  var menu = document.getElementById('menu');
  if (!knop || !menu) return;
  function zet(open) {
    knop.setAttribute('aria-expanded', open ? 'true' : 'false');
    menu.setAttribute('data-open', open ? 'true' : 'false');
    document.body.setAttribute('data-menu', open ? 'open' : 'closed');
    if (window.launchMotion && window.launchMotion.lenis) {
      open ? window.launchMotion.lenis.stop() : window.launchMotion.lenis.start();
    }
  }
  knop.addEventListener('click', function () {
    zet(knop.getAttribute('aria-expanded') !== 'true');
  });
  menu.addEventListener('click', function (e) { if (e.target.tagName === 'A') zet(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') zet(false); });
  matchMedia('(min-width: 1024px)').addEventListener('change', function (e) { if (e.matches) zet(false); });
})();
