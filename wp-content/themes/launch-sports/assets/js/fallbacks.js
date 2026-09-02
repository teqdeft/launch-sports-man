/* De foto is de standaard; ontbreekt het bestand, dan valt de hero terug op
   de getekende bal en het inzetje op zijn kader. Geen inline on*-handlers,
   dus dit overleeft een CSP met nonce.

   Dit script stond eerst inline direct achter de <figure>. Nu het onderaan
   de body geladen wordt kan de afbeelding al klaar zijn voordat de listener
   hangt, dus vangen we dat geval er apart bij op. */
(function () {
  var ball = document.querySelector('[data-ball-photo]');
  if (!ball) return;

  function terugval() {
    ball.style.display = 'none';
    var drawn = document.querySelector('[data-ball-drawn]');
    if (drawn) drawn.style.display = 'block';
  }

  ball.addEventListener('error', terugval);
  if (ball.complete && !ball.naturalWidth) terugval();
})();
