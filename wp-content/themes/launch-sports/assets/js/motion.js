/* ===================================================================
   LAUNCH SPORTS MANAGEMENT — MOTION LAYER
   GSAP 3 · ScrollTrigger · Lenis. One file, all four pages.

   Laadvolgorde (onderaan de body van elke pagina):
     js/fallbacks.js          terugval als de herofoto ontbreekt
     js/vendor/gsap.min.js    + scrolltrigger + splittext + lenis
     js/motion.js             dit bestand
     js/nav.js                hamburger, bewust los van deze laag

   Alle vier de pagina's laden dezelfde motion.js. Welke blokken draaien
   bepaalt de laag zelf: markSections() leest de sectiecommentaren en
   zet daaruit 'page' op home / what-we-do / about / lets-talk. Elk
   paginablok hieronder staat achter die schakelaar of achter een
   querySelector die op de andere pagina's niets vindt.

   It attaches itself. No data-attributes to maintain in the markup:
   the layer reads the section comments that are already there
   (<!-- ── HERO ── -->), then does its own DOM prep — line masks, word
   splits, drawable hairlines, counter spans. Re-export the pages from
   Claude Design and this still binds.

   Two-phase by design. Everything that animates is set to its start
   state the moment the script runs, and only played when its trigger
   fires. That is what stops the page flashing content in and then
   yanking it back out as you scroll into it.

   Nothing is hidden before we know GSAP is alive and motion is wanted,
   so a blocked script or prefers-reduced-motion leaves a fully readable
   page rather than a blank one.

   WHERE THE MOTION IS
   · Hero — headline rises line by line out of its own baseline, the
     portrait slot scales 1.06→1 on the same trigger so type and image
     settle together. Sub and body follow at +300ms.
   · Four pillars — the hairline draws left to right, the number counts
     in keeping its leading zero, the heading rises word by word. The
     cadence is derived from the type size, so pillar 04 at 96px is
     visibly the slowest.
   · Sticky index (What We Do) — a 1px gold rule moves between the six
     labels, animating its top and its height, never its colour.
     Inactive labels sit at 38%.
   · Five-stage progression (What We Do) — scrubbed, not fired. The
     reader draws it: stage, arrow, stage, arrow, then the rule and the
     caption underneath.
   · Wide slots parallax at roughly 0.88 of page speed, which is what
     makes the section edge read as a physical cut.

   WHEN REAL PHOTOGRAPHY LANDS
   The parallax and the 1.06 scale currently sit on the placeholder
   <figure>. Move both onto the <img> inside it and give the figure
   overflow:hidden. Nothing else changes.
   =================================================================== */
(function () {
  'use strict';

  var REDUCED = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var GOLD = '#CC9D39', INK = '#0B0B0B';

  /* ================= DRAWN MOTIFS =====================================
     Three small canvas pieces, hand-built, no library. They are here
     because they carry an argument the copy is already making, not as
     decoration: what arrives at once, what presses on a seventeen-year-old,
     and what each of the six process words actually means.
     Each pauses off-screen and falls back to a single still frame under
     prefers-reduced-motion.
     =================================================================== */

  var MOTIF = (function () {
    var PAPER = '#F2F0EA', INK = '#0B0B0B';

    function eO(t) { return 1 - Math.pow(1 - t, 3); }
    function eIO(t) { return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2; }
    function lp(a, b, t) { return a + (b - a) * t; }

    /* ---- PILEUP — everything landing on one line at the same time ---- */
    var DEFAULT_WORDS = [
      { t: 'NIL', x: 0.06 }, { t: 'CONTRACTS', x: 0.15 }, { t: 'TRANSFER', x: 0.25 },
      { t: 'COACHES', x: 0.33 }, { t: 'BRAND DEALS', x: 0.42 }, { t: 'SOCIAL', x: 0.52 },
      { t: 'PLAYING TIME', x: 0.59 }, { t: 'AGENTS', x: 0.685 }, { t: 'MONEY', x: 0.735 },
      { t: 'SCHOOL', x: 0.775 }, { t: 'FAMILY', x: 0.86 }, { t: 'EXPECTATIONS', x: 0.955 }
    ];
    var pileup = {
      /* The word list and caption come from the markup when it supplies them
         (data-motif-words / data-motif-caption), so they can be edited in the
         CMS. Without those attributes the built-in DEFAULT_WORDS are used, which
         is what the static build relies on. */
      init: function (el) {
        var words = DEFAULT_WORDS, caption = "ARRIVING AT ONCE";
        if (el) {
          var raw = el.getAttribute("data-motif-words");
          if (raw) {
            try {
              var parsed = JSON.parse(raw);
              if (parsed && parsed.length) {
                words = parsed.map(function (row, i, all) {
                  var x = parseFloat(row.x);
                  /* No position given? Spread the words evenly across the rule. */
                  if (isNaN(x)) x = all.length === 1 ? 0.5 : 0.06 + (0.89 * i) / (all.length - 1);
                  return { t: String(row.t || ""), x: Math.max(0, Math.min(1, x)) };
                });
              }
            } catch (e) { /* malformed JSON: keep the defaults rather than blank the diagram */ }
          }
          caption = el.getAttribute("data-motif-caption") || caption;
        }
        return { t: 0, n: 0, next: 700, gap: 700, out: 0, items: [], words: words, caption: caption };
      },
      resize: function (s) { s.items = []; s.n = 0; s.next = 700; s.gap = 700; s.out = 0; s.t = 0; },
      frame: function (ctx, s, w, h, dt) {
        s.t += dt; ctx.clearRect(0, 0, w, h);
        var base = h * 0.60, x0 = w * 0.03, x1 = w * 0.97;

        var draw = Math.min(1, s.t / 900);
        ctx.strokeStyle = 'rgba(242,240,234,.4)'; ctx.lineWidth = 1.6;
        ctx.beginPath(); ctx.moveTo(x0, base); ctx.lineTo(lp(x0, x1, eIO(draw)), base); ctx.stroke();

        if (s.out === 0 && s.t > 900) {
          s.next -= dt;
          if (s.next <= 0 && s.n < s.words.length) {
            s.items.push({ i: s.n, p: 0, up: s.n % 2 === 0 });
            s.n++;
            s.gap = Math.max(120, s.gap * 0.78);
            s.next = s.gap;
          }
          if (s.n >= s.words.length) { s.out += dt; }
        } else if (s.out > 0) {
          s.out += dt;
          if (s.out > 3400) { s.items = []; s.n = 0; s.gap = 700; s.next = 300; s.out = 0; s.t = 901; }
        }

        var fade = s.out > 1900 ? 1 - Math.min(1, (s.out - 1900) / 900) : 1;
        ctx.font = "600 12.5px 'IBM Plex Mono',monospace";
        for (var k = 0; k < s.items.length; k++) {
          var it = s.items[k], W = s.words[it.i];
          it.p = Math.min(1, it.p + dt / 380);
          var e = eO(it.p), a = it.p * fade;
          var x = x0 + (x1 - x0) * W.x;
          var from = it.up ? base - h * 0.5 : base + h * 0.45;
          var y = lp(from, base, e);
          var late = it.i >= s.words.length - 3;
          var col = late ? '204,157,57' : '242,240,234';

          ctx.strokeStyle = 'rgba(' + col + ',' + (0.8 * a).toFixed(3) + ')';
          ctx.lineWidth = 1.8;
          ctx.beginPath();
          ctx.moveTo(x, base);
          ctx.lineTo(x, it.up ? base - 17 : base + 17);
          ctx.stroke();

          ctx.fillStyle = 'rgba(' + col + ',' + ((late ? 1 : 0.9) * a).toFixed(3) + ')';
          ctx.textAlign = W.x > 0.9 ? 'right' : 'left';
          ctx.fillText(W.t, W.x > 0.9 ? x - 4 : x + 7, it.up ? base - 23 : base + 30);

          ctx.beginPath(); ctx.arc(x, base, 3.4, 0, 6.2832);
          ctx.fillStyle = 'rgba(' + col + ',' + a.toFixed(3) + ')'; ctx.fill();
        }

        ctx.textAlign = 'left';
        ctx.font = "500 11px 'IBM Plex Mono',monospace";
        ctx.fillStyle = 'rgba(242,240,234,.86)';
        ctx.fillText(s.caption, x0, h - 12);
        ctx.textAlign = 'right';
        ctx.fillStyle = s.n >= s.words.length ? '#CC9D39' : 'rgba(242,240,234,.7)';
        ctx.font = "600 11px 'IBM Plex Mono',monospace";
        ctx.fillText(('0' + s.n).slice(-2) + ' / ' + s.words.length, x1, h - 12);
        ctx.textAlign = 'left';
      },
      still: function (ctx, s, w, h) { s.t = 5000; s.n = s.words.length; s.items = s.words.map(function (_, i) { return { i: i, p: 1, up: i % 2 === 0 }; }); this.frame(ctx, s, w, h, 16); }
    };

    /* ---- PROCESS — one figure per word, driven by the sticky index ---- */
    var process = {
      init: function () { return { step: 0, prev: -1, t: 0, fade: 1 }; },
      resize: function () {},
      setStep: function (s, i) { if (i === s.step) return; s.prev = s.step; s.step = i; s.t = 0; s.fade = 0; },
      frame: function (ctx, s, w, h, dt) {
        s.t += dt; ctx.clearRect(0, 0, w, h);
        s.fade = Math.min(1, s.fade + dt / 320);
        ctx.save();
        ctx.globalAlpha = s.fade;
        ctx.translate(0, (1 - eO(s.fade)) * 10);
        (this.draws[s.step] || this.draws[0]).call(this, ctx, w, h, s.t / 1000);
        ctx.restore();
      },
      still: function (ctx, s, w, h) { s.t = 1200; s.fade = 1; this.frame(ctx, s, w, h, 16); },

      draws: [
        // WE LISTEN — it travels toward her, and she takes it in
        function (ctx, w, h, ts) {
          var cy = h / 2, dx = w * 0.76;
          for (var i = 0; i < 3; i++) {
            var p = ((ts * 0.45 + i / 3) % 1);
            var x = lp(w * 0.10, dx - 14, p);
            var a = Math.sin(p * Math.PI) * 0.75;
            ctx.strokeStyle = 'rgba(11,11,11,' + a.toFixed(3) + ')';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(x, cy, 14 + i * 3, -0.85, 0.85);
            ctx.stroke();
          }
          var pulse = 0.5 + Math.sin(ts * 2.6) * 0.5;
          ctx.beginPath(); ctx.arc(dx, cy, 8 + pulse * 3, 0, 6.2832);
          ctx.fillStyle = '#CC9D39'; ctx.fill();
          ctx.strokeStyle = 'rgba(204,157,57,' + (0.4 * (1 - pulse)).toFixed(3) + ')';
          ctx.lineWidth = 1;
          ctx.beginPath(); ctx.arc(dx, cy, 10 + pulse * 12, 0, 6.2832); ctx.stroke();
        },
        // WE EVALUATE — everything measured before anything is recommended
        function (ctx, w, h, ts) {
          var n = 6, bw = 18, gap = (w * 0.76 - n * bw) / (n - 1), x0 = w * 0.12;
          var hs = [0.42, 0.68, 0.34, 0.86, 0.55, 0.48], best = 3;
          var cycle = (ts % 3.4), scan = Math.min(1, cycle / 1.6);
          var sx = x0 + eIO(scan) * (w * 0.76);
          for (var i = 0; i < n; i++) {
            var x = x0 + i * (bw + gap), bh = (h - 52) * hs[i];
            var passed = sx > x + bw;
            var win = passed && i === best && cycle > 1.7;
            ctx.fillStyle = win ? '#CC9D39' : 'rgba(11,11,11,' + (passed ? 0.16 : 0.3) + ')';
            ctx.fillRect(x, h - 30 - bh, bw, bh);
            if (win) {
              ctx.strokeStyle = '#CC9D39'; ctx.lineWidth = 1.4;
              ctx.beginPath();
              ctx.moveTo(x + 1, h - 40 - bh); ctx.lineTo(x + 5, h - 36 - bh); ctx.lineTo(x + 11, h - 46 - bh);
              ctx.stroke();
            }
          }
          if (cycle < 1.7) {
            ctx.strokeStyle = 'rgba(11,11,11,.5)'; ctx.lineWidth = 2;
            ctx.beginPath(); ctx.moveTo(sx, 8); ctx.lineTo(sx, h - 26); ctx.stroke();
          }
          ctx.strokeStyle = 'rgba(11,11,11,.34)'; ctx.lineWidth = 1.8;
          ctx.beginPath(); ctx.moveTo(x0, h - 29); ctx.lineTo(x0 + w * 0.76, h - 29); ctx.stroke();
        },
        // WE PLAN — the right order, not whichever order they arrive in
        function (ctx, w, h, ts) {
          var n = 5, sw = 34, x0 = w * 0.09, span = w * 0.82, gap = (span - n * sw) / (n - 1), y = h / 2;
          ctx.strokeStyle = 'rgba(11,11,11,.3)'; ctx.lineWidth = 1.8; 
          ctx.beginPath(); ctx.moveTo(x0, y + 26); ctx.lineTo(x0 + span, y + 26); ctx.stroke();
          var cycle = ts % 5.2;
          for (var i = 0; i < n; i++) {
            var start = i * 0.55, p = Math.max(0, Math.min(1, (cycle - start) / 0.75));
            if (p <= 0) continue;
            var tx = x0 + i * (sw + gap);
            var x = lp(x0 + span + 30, tx, eO(p));
            var a = Math.min(1, p * 2);
            if (cycle > 4.4) a *= Math.max(0, 1 - (cycle - 4.4) / 0.8);
            ctx.fillStyle = 'rgba(11,11,11,' + (0.16 * a).toFixed(3) + ')';
            ctx.fillRect(x, y - 18, sw, 44);
            ctx.strokeStyle = (p >= 1 ? 'rgba(204,157,57,' + a.toFixed(3) + ')' : 'rgba(11,11,11,' + (0.3 * a).toFixed(3) + ')');
            ctx.lineWidth = 2;
            ctx.strokeRect(x + 1, y - 17, sw - 2, 42);
          }
        },
        // WE ADVOCATE — someone goes through the door for her
        function (ctx, w, h, ts) {
          var y = h / 2, wall = w * 0.62, cycle = ts % 3.6;
          var p = Math.min(1, cycle / 1.9);
          var x = lp(w * 0.12, w * 0.86, eIO(p));
          for (var i = 0; i < 3; i++) {
            ctx.beginPath(); ctx.arc(w * 0.12 - 2 - i * 13, y, 2.4, 0, 6.2832);
            ctx.fillStyle = 'rgba(11,11,11,.2)'; ctx.fill();
          }
          var push = Math.max(0, 1 - Math.abs(x - wall) / 40);
          ctx.strokeStyle = 'rgba(11,11,11,.5)'; ctx.lineWidth = 2;
          ctx.beginPath();
          ctx.moveTo(wall, 10);
          ctx.quadraticCurveTo(wall + push * 16, y, wall, h - 10);
          ctx.stroke();
          ctx.save();
          if (x > wall) { ctx.shadowColor = 'rgba(204,157,57,.6)'; ctx.shadowBlur = 10; }
          ctx.beginPath(); ctx.arc(x, y, 7, 0, 6.2832);
          ctx.fillStyle = '#CC9D39'; ctx.fill();
          ctx.restore();
          if (cycle > 2.2) {
            ctx.font = "500 10.5px 'IBM Plex Mono',monospace";
            ctx.fillStyle = 'rgba(11,11,11,' + Math.min(0.82, (cycle - 2.2)).toFixed(2) + ')';
            ctx.fillText('THROUGH', w * 0.62 + 8, y - 16);
          }
        },
        // WE SUPPORT — the weight arrives, and it is carried
        function (ctx, w, h, ts) {
          var x0 = w * 0.14, bw = w * 0.72, cycle = ts % 4.2;
          var press = cycle < 1.2 ? eO(cycle / 1.2) : (cycle < 3.0 ? 1 : 1 - eO((cycle - 3.0) / 1.2));
          var top = 26 + press * 16, floor = h - 30;
          ctx.fillStyle = 'rgba(11,11,11,.16)';
          ctx.fillRect(x0, top, bw, 28);
          ctx.strokeStyle = 'rgba(11,11,11,.42)'; ctx.lineWidth = 2;
          ctx.strokeRect(x0 + 1, top + 1, bw - 2, 26);
          for (var i = 0; i < 3; i++) {
            var px = x0 + 14 + i * ((bw - 28) / 2);
            var bend = press * (i === 1 ? 5 : 3);
            ctx.strokeStyle = i === 1 ? '#CC9D39' : 'rgba(11,11,11,.34)';
            ctx.lineWidth = i === 1 ? 2.6 : 1.6;
            ctx.beginPath();
            ctx.moveTo(px, top + 28);
            ctx.quadraticCurveTo(px + bend, (top + 20 + floor) / 2, px, floor);
            ctx.stroke();
          }
          ctx.strokeStyle = 'rgba(11,11,11,.34)';
          ctx.lineWidth = 1.8;
          ctx.beginPath(); ctx.moveTo(x0 - 8, floor); ctx.lineTo(x0 + bw + 8, floor); ctx.stroke();
        },
        // WE EVOLVE — the plan changes shape as she does
        function (ctx, w, h, ts) {
          var cx = w * 0.48, cy = h / 2, R = Math.min(w, h) * 0.30;
          var sides = [3, 4, 5, 6];
          var seg = 1.6, k = (ts / seg) % sides.length;
          var a = sides[Math.floor(k)], b = sides[(Math.floor(k) + 1) % sides.length];
          var m = eIO(k % 1);
          var N = 72;
          function radius(n, ang) {
            var step = 6.2832 / n, half = step / 2;
            var loc = ((ang % step) + step) % step - half;
            return Math.cos(half) / Math.cos(loc);
          }
          ctx.beginPath();
          for (var i = 0; i <= N; i++) {
            var ang = (i / N) * 6.2832 - Math.PI / 2;
            var r = R * lp(radius(a, ang), radius(b, ang), m);
            var x = cx + Math.cos(ang) * r, y = cy + Math.sin(ang) * r;
            i ? ctx.lineTo(x, y) : ctx.moveTo(x, y);
          }
          ctx.closePath();
          ctx.strokeStyle = '#CC9D39'; ctx.lineWidth = 2.4; ctx.stroke();
          ctx.fillStyle = 'rgba(204,157,57,.12)'; ctx.fill();
          ctx.beginPath(); ctx.arc(cx, cy, 2.2, 0, 6.2832);
          ctx.fillStyle = 'rgba(11,11,11,.7)'; ctx.fill();
        }
      ]
    };


    /* ---- REPLY — a message goes in, a person sends one back ---------- */
    var reply = {
      init: function () { return { t: 0 }; },
      resize: function () {},
      frame: function (ctx, s, w, h, dt) {
        s.t += dt; ctx.clearRect(0, 0, w, h);
        var y = h * 0.52, x0 = w * 0.10, x1 = w * 0.90;
        var CYCLE = 5200, p = (s.t % CYCLE) / CYCLE;

        ctx.strokeStyle = 'rgba(11,11,11,.28)'; ctx.lineWidth = 1.6;
        ctx.beginPath(); ctx.moveTo(x0, y); ctx.lineTo(x1, y); ctx.stroke();

        [[x0, 'YOU'], [x1, 'LAUNCH']].forEach(function (e, i) {
          ctx.strokeStyle = 'rgba(11,11,11,.45)'; ctx.lineWidth = 1.8;
          ctx.strokeRect(e[0] - 7, y - 7, 14, 14);
          ctx.font = "500 11px 'IBM Plex Mono',monospace";
          ctx.fillStyle = 'rgba(11,11,11,.78)';
          ctx.textAlign = i ? 'right' : 'left';
          ctx.fillText(e[1], e[0] + (i ? 6 : -6), y + 26);
        });
        ctx.textAlign = 'left';

        // out
        if (p < 0.34) {
          var a = eIO(p / 0.34);
          var x = lp(x0, x1, a);
          ctx.beginPath(); ctx.arc(x, y, 4.6, 0, 6.2832);
          ctx.fillStyle = 'rgba(11,11,11,.75)'; ctx.fill();
        } else if (p < 0.46) {
          var pulse = (p - 0.34) / 0.12;
          ctx.strokeStyle = 'rgba(204,157,57,' + (0.6 * (1 - pulse)).toFixed(3) + ')';
          ctx.lineWidth = 1.4;
          ctx.beginPath(); ctx.arc(x1, y, 8 + pulse * 22, 0, 6.2832); ctx.stroke();
        } else if (p < 0.80) {
          var b = eIO((p - 0.46) / 0.34);
          var xb = lp(x1, x0, b);
          ctx.save(); ctx.shadowColor = 'rgba(204,157,57,.7)'; ctx.shadowBlur = 10;
          ctx.beginPath(); ctx.arc(xb, y, 5.2, 0, 6.2832);
          ctx.fillStyle = '#CC9D39'; ctx.fill(); ctx.restore();
        } else {
          var f = 1 - (p - 0.80) / 0.20;
          ctx.font = "600 11.5px 'IBM Plex Mono',monospace";
          ctx.fillStyle = 'rgba(204,157,57,' + f.toFixed(2) + ')';
          ctx.textAlign = 'left';
          ctx.fillText('ANSWERED', x0, y - 16);
        }

        ctx.font = "500 11px 'IBM Plex Mono',monospace";
        ctx.fillStyle = 'rgba(11,11,11,.7)';
        ctx.textAlign = 'right';
        ctx.fillText('NO OPEN INBOX, NO QUEUE', x1, h - 10);
        ctx.textAlign = 'left';
      },
      still: function (ctx, s, w, h) { s.t = 4600; this.frame(ctx, s, w, h, 16); }
    };

    var MODS = { pileup: pileup, process: process, reply: reply };
    var LIST = [];

    function mount(el) {
      var kind = el.getAttribute('data-motif'), mod = MODS[kind];
      if (!mod) return;
      var canvas = document.createElement('canvas');
      canvas.setAttribute('aria-hidden', 'true');
      canvas.style.cssText = 'display:block;width:100%;height:100%;pointer-events:none';
      el.appendChild(canvas);
      var ctx = canvas.getContext('2d'), w = 0, h = 0, st = mod.init(el), vis = false;

      function fit() {
        var r = el.getBoundingClientRect();
        var nw = Math.max(1, Math.floor(r.width)), nh = Math.max(1, Math.floor(r.height));
        var dpr = Math.min(2, window.devicePixelRatio || 1);
        canvas.width = nw * dpr; canvas.height = nh * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        w = nw; h = nh;
        if (mod.resize) mod.resize(st, w, h);
      }
      fit();

      el.__motif = { setStep: function (i) { if (mod.setStep) mod.setStep(st, i); } };

      if (REDUCED) { mod.still(ctx, st, w, h); return; }
      if ('ResizeObserver' in window) new ResizeObserver(fit).observe(el);
      else window.addEventListener('resize', fit);
      if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (e) {
          e.forEach(function (x) { vis = x.isIntersecting; });
        }, { threshold: 0.05 }).observe(el);
      } else vis = true;

      el.__tick = function (dt) { if (vis) mod.frame(ctx, st, w, h, dt); };
      LIST.push(el);
    }

    return {
      start: function () {
        Array.prototype.forEach.call(document.querySelectorAll('[data-motif]'), mount);
        if (REDUCED || !LIST.length) return;
        var last = performance.now();
        (function tick(now) {
          var dt = Math.min(48, now - last); last = now;
          for (var i = 0; i < LIST.length; i++) LIST[i].__tick(dt);
          requestAnimationFrame(tick);
        })(last);
      }
    };
  })();

  // The drawn motifs do not need GSAP and are safe under reduced motion,
  // where each falls back to one still frame. Start them either way.
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', MOTIF.start);
  else MOTIF.start();

  /* Zolang er geen endpoint is ingesteld, versturen we niet: liever niets dan
     een herlaadde pagina waarin de ingevulde gegevens verdwijnen. Geen inline
     onsubmit meer, dat overleeft een CSP met nonce niet.

     Staat bewust vóór de return hieronder. Het formulier heeft niets met GSAP
     te maken, en toen dit erachter stond verloor iedereen met beperkte
     beweging — of iedereen bij wie een vendor-bestand niet laadde — bij het
     verzenden alles wat hij had ingetypt. */
  (function formulierWacht() {
    var f = document.querySelector('form[data-form-endpoint="TODO"]');
    if (!f) return;
    f.addEventListener('submit', function (e) { e.preventDefault(); });
  })();

  if (REDUCED || !window.gsap || !window.ScrollTrigger) return;

  var gsap = window.gsap, ST = window.ScrollTrigger;
  gsap.registerPlugin(ST);

  /* ---------- reading the page --------------------------------------- */

  /* De sectiesleutel staat als data-sec="…" in de markup. Dat is de bron.
     Vroeger werd hij alleen afgeleid uit de opmaakcommentaren (<!-- ── HERO ── -->)
     door het volgende broer-element te taggen. Elke HTML-minifier — en die zit
     in zo'n beetje elke WordPress-cacheplugin — gooit commentaar weg, en dan
     valt de hele kaart om: 'page' viel terug op 'home' en drie van de vier
     pagina's verloren al hun animaties.

     De commentaarwandeling blijft als terugval staan, voor markup die de
     attributen nog niet draagt. Hij overschrijft een bestaand attribuut niet,
     zodat een element dat door twee markers wordt geclaimd (RUNNING BAND en
     THE LAUNCH APPROACH wijzen op Home naar dezelfde sectie) zijn sleutel
     houdt en beide namen toch in de kaart komen. */
  function markSections(root) {
    var map = {};

    Array.prototype.forEach.call(root.querySelectorAll('[data-sec]'), function (el) {
      var key = el.getAttribute('data-sec');
      if (key && !map[key]) map[key] = el;
    });

    var walker = document.createNodeIterator(root, NodeFilter.SHOW_COMMENT), c;
    while ((c = walker.nextNode())) {
      var m = /──\s*([A-Z0-9 ,'’+-]+?)\s*(?:──|—)/.exec(c.nodeValue);
      if (!m) continue;
      var el = c.nextElementSibling;
      if (!el) continue;
      var key = m[1].trim().toLowerCase().replace(/[^a-z0-9]+/g, '-');
      if (map[key]) continue;
      if (!el.hasAttribute('data-sec')) el.setAttribute('data-sec', key);
      map[key] = el;
    }
    return map;
  }

  function q(root, sel) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  /* ---------- splitting ---------------------------------------------- */

  // Word spans that keep nested markup and colour.
  // outer clips, inner is the thing that rises.
  function splitWords(el) {
    if (el.__words) return el.__words;
    var words = [];
    (function walk(node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (n) {
        if (n.nodeType === 3) {
          var parts = n.nodeValue.split(/(\s+)/).filter(function (s) { return s.length; });
          var frag = document.createDocumentFragment();
          parts.forEach(function (p) {
            if (/^\s+$/.test(p)) { frag.appendChild(document.createTextNode(' ')); return; }
            var outer = document.createElement('span');
            outer.style.cssText = 'display:inline-block;overflow:hidden;vertical-align:bottom;padding:.22em 0 .16em;margin:-.22em 0 -.16em';
            var inner = document.createElement('span');
            inner.style.cssText = 'display:inline-block;will-change:transform';
            inner.textContent = p;
            outer.appendChild(inner);
            frag.appendChild(outer);
            words.push(inner);
          });
          node.replaceChild(frag, n);
        } else if (n.nodeType === 1 && n.tagName !== 'BR') {
          walk(n);
        }
      });
    })(el);
    el.__words = words;
    return words;
  }

  // Poster-scale headings read better line by line. Splits on <br>.
  function splitLines(el) {
    if (el.__lines) return el.__lines;
    var lines = [], out = [];
    function fresh() {
      var outer = document.createElement('span');
      outer.style.cssText = 'display:block;overflow:hidden;padding:.20em 0 .10em;margin:-.20em 0 -.10em';
      var inner = document.createElement('span');
      inner.style.cssText = 'display:block;will-change:transform';
      outer.appendChild(inner);
      out.push(outer); lines.push(inner);
      return inner;
    }
    var inner = fresh();
    Array.prototype.slice.call(el.childNodes).forEach(function (n) {
      if (n.nodeType === 1 && n.tagName === 'BR') inner = fresh();
      else inner.appendChild(n.cloneNode(true));
    });
    el.innerHTML = '';
    out.forEach(function (o) { el.appendChild(o); });
    el.__lines = lines;
    return lines;
  }

  /* ---------- prepared animations -------------------------------------
     Each of these sets the start state now and returns a play(). Call
     play() inside a trigger to build the tween at the right moment.   */

  function riseWords(el, o) {
    o = o || {};
    if (!el) return null;
    var w = splitWords(el);
    if (!w.length) return null;
    gsap.set(w, { yPercent: 118, scaleY: 1.16, transformOrigin: 'left bottom' });
    return function () {
      return gsap.to(w, {
        yPercent: 0, scaleY: 1,
        duration: o.duration || 0.6,
        stagger: o.stagger || 0.026,
        ease: o.ease || 'expo.out'
      });
    };
  }

  function riseLines(el, o) {
    o = o || {};
    if (!el) return null;
    var l = splitLines(el);
    if (!l.length) return null;
    gsap.set(l, { yPercent: 104, scaleY: 1.1, transformOrigin: 'left bottom' });
    return function () {
      return gsap.to(l, {
        yPercent: 0, scaleY: 1,
        duration: o.duration || 0.78,
        stagger: o.stagger || 0.065,
        ease: o.ease || 'expo.out'
      });
    };
  }

  // Quiet fade and rise, for body copy, captions, cards, form rows.
  function rise(targets, o) {
    o = o || {};
    targets = (targets && targets.length !== undefined) ? Array.prototype.slice.call(targets) : (targets ? [targets] : []);
    targets = targets.filter(Boolean);
    if (!targets.length) return null;
    gsap.set(targets, { y: o.y != null ? o.y : 34, opacity: 0 });
    return function () {
      return gsap.to(targets, {
        y: 0, opacity: 1,
        duration: o.duration || 0.7,
        stagger: o.stagger || 0.05,
        ease: 'expo.out'
      });
    };
  }

  // A slot wipes open from its own edge and settles out of a slight
  // overscale. Harder than a fade, and it survives a real photograph.
  function scaleIn(el, o) {
    o = o || {};
    if (!el) return null;
    gsap.set(el, { clipPath: 'inset(0% 0% 100% 0%)', scale: o.from || 1.09, transformOrigin: 'center center' });
    return function () {
      var tl = gsap.timeline();
      tl.to(el, { clipPath: 'inset(0% 0% 0% 0%)', duration: o.duration || 0.9, ease: 'expo.inOut' }, 0);
      tl.to(el, { scale: 1, duration: (o.duration || 0.9) + 0.5, ease: 'expo.out' }, 0);
      // clip-path clips descendants too, so it has to go once the wipe is
      // done or anything breaking out of the slot gets cut off with it
      tl.set(el, { clearProps: 'clipPath' });
      return tl;
    };
  }

  // Turn an existing border into something that can be drawn. The border
  // stays put so the layout never shifts; it just goes transparent and a
  // 1px element takes its place.
  function drawableRule(el, side, o) {
    if (!el) return null;
    side = side || 'bottom';
    o = o || {};
    var cap = side[0].toUpperCase() + side.slice(1);
    var cs = getComputedStyle(el);
    if (cs['border' + cap + 'Width'] === '0px' && !o.force) return null;
    var colour = o.colour || cs['border' + cap + 'Color'];
    if (cs.position === 'static') el.style.position = 'relative';
    el.style['border' + cap + 'Color'] = 'transparent';
    var vertical = (side === 'left' || side === 'right');
    var i = document.createElement('i');
    i.setAttribute('aria-hidden', 'true');
    i.style.cssText = 'position:absolute;background:' + colour + ';pointer-events:none;' +
      (vertical
        ? 'top:0;bottom:0;width:1px;' + side + ':-1px;transform-origin:top center'
        : 'left:0;right:0;height:1px;' + side + ':-1px;transform-origin:left center');
    el.appendChild(i);
    gsap.set(i, vertical ? { scaleY: 0 } : { scaleX: 0 });
    return function (dur) {
      return gsap.to(i, vertical
        ? { scaleY: 1, duration: (dur || o.duration || 0.6) * 0.7, ease: 'expo.inOut' }
        : { scaleX: 1, duration: (dur || o.duration || 0.6) * 0.7, ease: 'expo.inOut' });
    };
  }

  // "01" counts in and keeps its leading zero. "185+" keeps its plus.
  function counter(el, o) {
    o = o || {};
    if (!el) return null;
    var m = /^(\d+)(\D*)$/.exec((el.textContent || '').trim());
    if (!m) return null;
    var target = parseInt(m[1], 10), suffix = m[2] || '', pad = m[1].length;
    function write(v) {
      var s = String(Math.round(v));
      while (s.length < pad) s = '0' + s;
      el.textContent = s + suffix;
    }
    write(0);
    return function () {
      var obj = { v: 0 }, tl = gsap.timeline();
      tl.to(obj, {
        v: target, duration: (o.duration || 0.9) * 0.8, ease: 'power2.out',
        onUpdate: function () { write(obj.v); }
      }, 0);
      tl.fromTo(el, { scale: 1.32, transformOrigin: 'left bottom' },
                    { scale: 1, duration: 0.7, ease: 'back.out(2.2)' }, 0);
      return tl;
    };
  }


  /* ---- the one loud moment per page ---------------------------------
     The closing headline is printed rather than revealed: a gold slab
     wipes across each line and takes the line with it as it leaves.
     Real line splitting needs the real font, so this one is built after
     document.fonts settles, and it sits far enough down the page that
     its trigger has not fired by then.                                */
  function barLines(el, o) {
    if (!el) return;
    o = o || {};
    var lines;
    if (window.SplitText) {
      lines = new window.SplitText(el, { type: 'lines', mask: 'lines' }).lines;
    } else {
      lines = splitLines(el);
    }
    if (!lines || !lines.length) return;

    gsap.set(lines, { yPercent: 106, scaleY: 1.08, transformOrigin: 'left bottom' });

    var bars = lines.map(function (ln) {
      var host = ln.parentNode || ln;
      if (getComputedStyle(host).position === 'static') host.style.position = 'relative';
      var bar = document.createElement('i');
      bar.setAttribute('aria-hidden', 'true');
      bar.style.cssText = 'position:absolute;left:0;top:0;bottom:0;width:100%;background:' + GOLD +
        ';transform:scaleX(0);transform-origin:left center;pointer-events:none;z-index:4';
      host.appendChild(bar);
      return bar;
    });

    ST.create({
      trigger: el, start: o.start || 'top 82%', once: true,
      onEnter: function () {
        var tl = gsap.timeline();
        bars.forEach(function (bar, i) {
          var at = i * 0.1;
          tl.to(bar, { scaleX: 1, duration: 0.34, ease: 'expo.out' }, at);
          tl.to(lines[i], { yPercent: 0, scaleY: 1, duration: 0.5, ease: 'expo.out' }, at + 0.16);
          tl.set(bar, { transformOrigin: 'right center' }, at + 0.34);
          tl.to(bar, { scaleX: 0, duration: 0.46, ease: 'expo.inOut' }, at + 0.36);
        });
        return tl;
      }
    });
  }

  function onEnter(trigger, build, start) {
    if (!trigger) return;
    ST.create({ trigger: trigger, start: start || 'top 86%', once: true, onEnter: build });
  }

  function parallax(fig, amount) {
    if (!fig) return;
    var a = amount == null ? 6 : amount;
    gsap.fromTo(fig, { yPercent: -a }, {
      yPercent: a, ease: 'none',
      scrollTrigger: { trigger: fig, start: 'top bottom', end: 'bottom top', scrub: 0.6 }
    });
  }

  function play(tl, fn, at) { if (fn) tl.add(fn(), at); return tl; }

  /* ---------- Lenis --------------------------------------------------- */

  var lenis = null;
  if (window.Lenis) {
    lenis = new window.Lenis({ duration: 1.05, smoothWheel: true });
    lenis.on('scroll', ST.update);
    gsap.ticker.add(function (t) { lenis.raf(t * 1000); });
    gsap.ticker.lagSmoothing(0);
  }
  // handle for the production build and for debugging
  window.launchMotion = { lenis: lenis, ScrollTrigger: ST };

  /* ---------- page wiring --------------------------------------------- */

  // Absolutely positioned slots carry z-index:1 inside sections that are
  // only position:relative, so they escape and paint over whatever comes
  // next. Giving every section its own stacking context keeps a slot that
  // bleeds past the section edge underneath the next section, which is the
  // clipped-by-the-boundary effect the composition is after anyway.
  Array.prototype.forEach.call(document.querySelectorAll('section, header, footer'), function (el) {
    el.style.isolation = 'isolate';
  });

  // Anything that needs true line boxes waits for the real font.
  var LOUD = [];

  var SEC = markSections(document.body);
  var page = SEC['the-launch-approach'] ? 'home'
    : SEC['six-steps-spine'] ? 'what-we-do'
    : document.querySelector('[data-hscroll]') ? 'about'
    : SEC['form-what-happens-next'] ? 'lets-talk' : 'home';
  document.body.setAttribute('data-page', page);

  /* header — settles once, then stays out of the way */
  var header = document.querySelector('header');
  if (header) {
    gsap.from(header.querySelectorAll(':scope > div > a, :scope > div > div'), {
      opacity: 0, y: -10, duration: 0.7, stagger: 0.08, ease: 'power2.out', delay: 0.05
    });
  }

  /* --- hero, all four pages ------------------------------------------- */
  (function () {
    var hero = SEC['hero'];
    if (!hero) return;
    var h1 = hero.querySelector('h1');
    var fig = hero.querySelector('figure');
    var body = q(hero, 'p, a[href]').filter(function (el) {
      return (!h1 || !h1.contains(el)) && !el.closest('figure');
    });

    var pFig = scaleIn(fig, { from: 1.06, duration: 1.4 });
    // MORE / THAN NIL. is set as two deliberate lines, so it rises line by
    // line out of its own baseline. The other three heroes are one
    // sentence that wraps, and those read better word by word.
    var pH1 = h1 && h1.querySelector('br')
      ? riseLines(h1, { duration: 0.9, stagger: 0.08 })
      : riseWords(h1, { duration: 0.82, stagger: 0.035 });
    var pBody = rise(body, { y: 18, stagger: 0.06 });

    var tl = gsap.timeline({ delay: 0.15 });
    play(tl, pFig, 0);
    play(tl, pH1, 0);
    play(tl, pBody, 0.3);
  })();

  /* ================= HOME ============================================= */
  if (page === 'home') {

    // The hero photo slot became a drawn basketball + impact burst — no
    // background box, so it needs its own reveal instead of scaleIn's
    // clip-path wipe (there's no edge to wipe from). The ball drops in
    // with weight, the burst pops out after it lands, and neither one
    // ever fully sits still once it has.
    (function heroBasketball() {
      var wrap = document.querySelector('[data-basketball]'); if (!wrap) return;

      // Ligt er een echte foto in het slot, dan is dat het onderwerp en niet
      // de tekening. Een gefotografeerde hand hoort niet te stuiteren, dus
      // geen back.out hier: hij komt omhoog en komt tot rust. Daarna ademt
      // hij door, zodat de hero nooit helemaal stilstaat.
      var photo = wrap.querySelector('[data-ball-photo]');
      if (photo && photo.style.display !== 'none') {
        gsap.set(photo, { autoAlpha: 0, y: -34, scale: 0.9, transformOrigin: '50% 42%' });
        gsap.timeline({ delay: 0.4 })
          .to(photo, { autoAlpha: 1, y: 0, scale: 1, duration: 1.05, ease: 'expo.out' });
        gsap.to(photo, { y: -7, duration: 4.6, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 1.9 });
        parallax(photo, 5);
        return;
      }

      var ball = wrap.querySelector('[data-bb-ball]');
      var glow = q(wrap, '[data-bb-glow] > *');
      var particles = q(wrap, '[data-bb-p]');
      if (!ball) return;

      gsap.set(glow, { opacity: 0 });
      gsap.set(ball, { scale: 0.5, opacity: 0, rotate: -18, y: -30, transformOrigin: '50% 50%' });
      gsap.set(particles, { scale: 0, opacity: 0 });

      var tl = gsap.timeline({ delay: 0.5 });
      tl.to(ball, { scale: 1, opacity: 1, rotate: 0, y: 0, duration: 1, ease: 'back.out(1.7)' }, 0);
      tl.to(glow, { opacity: 1, duration: 0.6, stagger: 0.1, ease: 'power1.out' }, 0.35);
      tl.to(particles, {
        scale: 1,
        opacity: function (i, t) { return parseFloat(t.getAttribute('data-bb-op')) || 1; },
        duration: 0.5, stagger: 0.026, ease: 'back.out(2.6)'
      }, 0.55);

      // the ball never fully settles — a slow, small breathing rotation
      gsap.to(ball, { rotate: 5, y: -5, duration: 4.4, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 1.7 });

      // scroll: the burst drifts apart at its own pace, the ball barely moves
      parallax(ball, 5);
      particles.forEach(function (p) {
        var sp = parseFloat(p.getAttribute('data-bb-speed')) || 1;
        parallax(p, 9 * sp);
      });
    })();

    (function whyLaunch() {
      var s = SEC['why-launch']; if (!s) return;
      var h2 = s.querySelector('h2');
      var paras = q(s, ':scope > div > div > div > p');
      var loose = q(s, ':scope > div > p');
      var big = loose[0], close = loose[1];
      var fig = s.querySelector('figure');

      var pH2 = riseWords(h2, { duration: 0.8, stagger: 0.04 });
      var pParas = rise(paras, { y: 18 });
      onEnter(s, function () { play(play(gsap.timeline(), pH2, 0), pParas, 0.15); });

      // "Opportunity is exciting." lands, and the turn arrives a beat later.
      if (big) {
        var all = splitWords(big);
        var spanEl = big.querySelector('span');
        var inSpan = spanEl ? all.filter(function (w) { return spanEl.contains(w); }) : [];
        var outSpan = all.filter(function (w) { return inSpan.indexOf(w) === -1; });
        gsap.set(all, { yPercent: 110 });
        onEnter(big, function () {
          var tl = gsap.timeline();
          if (outSpan.length) tl.to(outSpan, { yPercent: 0, duration: 0.85, stagger: 0.045, ease: 'power4.out' }, 0);
          if (inSpan.length) tl.to(inSpan, { yPercent: 0, duration: 0.85, stagger: 0.045, ease: 'power4.out' }, 0.3);
        }, 'top 82%');
      }

      var pClose = rise(close, { y: 18 });
      onEnter(close, function () { play(gsap.timeline(), pClose, 0); });

      // Ligt er een foto in het kader, dan beweegt die en niet het kader:
      // het kader heeft overflow:hidden, dus de randen blijven staan.
      parallax((fig && fig.querySelector('img')) || fig, 6);
    })();

    // Four pillars, four weights, so four speeds. 04 is the biggest and
    // the slowest, which is the whole point of setting them differently.
    (function pillars() {
      var s = SEC['the-launch-approach']; if (!s) return;
      var head = s.querySelector('h2');
      var sub = head ? head.parentNode.querySelector('p') : null;

      var pHead = riseWords(head, { duration: 0.7 });
      var pSub = rise(sub, { y: 14 });
      var pHeadRule = head ? drawableRule(head.parentNode, 'bottom') : null;
      onEnter(s, function () {
        var tl = gsap.timeline();
        play(tl, pHead, 0); play(tl, pSub, 0.1);
        if (pHeadRule) tl.add(pHeadRule(0.7), 0.05);
      });

      var arts = q(s, 'article');
      if (!arts.length) return;

      /* Eén doorlopende maatlat langs de linkerrail.

         De vier stappen zijn nu typografisch identiek — zelfde korps, zelfde
         regelhoogte, zelfde gewicht, zelfde ritme. Het onderscheid tussen ze
         moet dan uit de scroll komen en niet meer uit de opmaak: de lat tekent
         zichzelf mee met de scroll (scaleY, ease none, scrub 0.8) en het nummer
         van de stap die op leeshoogte staat kleurt goud. Vier gelijke blokken,
         één beweging die ze aan elkaar rijgt.

         De wikkel wordt hier gemaakt en niet in de markup, zodat het strippen
         van deze laag gewoon vier gelijke blokken achterlaat.               */
      var rail = document.createElement('div');
      rail.style.cssText = 'position:relative';
      arts[0].parentNode.insertBefore(rail, arts[0]);
      arts.forEach(function (a) { rail.appendChild(a); });

      var spoor = document.createElement('i');
      spoor.setAttribute('aria-hidden', 'true');
      spoor.style.cssText = 'position:absolute;left:-28px;top:0;bottom:0;width:1px;background:rgba(11,11,11,.14)';
      var loper = document.createElement('i');
      loper.setAttribute('aria-hidden', 'true');
      loper.style.cssText = 'position:absolute;left:-28px;top:0;bottom:0;width:1px;background:' + GOLD + ';transform:scaleY(0);transform-origin:top center';
      rail.appendChild(spoor);
      rail.appendChild(loper);

      gsap.to(loper, {
        scaleY: 1, ease: 'none',
        scrollTrigger: { trigger: rail, start: 'clamp(top 62%)', end: 'clamp(bottom 62%)', scrub: 0.8 }
      });

      arts.forEach(function (art) {
        var h3 = art.querySelector('h3');
        // Pillar 04 inverts the order — body copy first, label and heading
        // after — so find the label by what it says, not by where it sits.
        var ps = q(art, 'p');
        var label = ps.filter(function (p) { return /^\s*\d{2}\s*·/.test(p.textContent); })[0] || ps[0];
        var body = ps.filter(function (p) { return p !== label; });
        var fig = art.querySelector('figure');

        // pull the leading-zero number out of "01 · The Player"
        var numEl = null;
        if (label && /^\s*\d{2}\s*·/.test(label.textContent)) {
          var txt = label.textContent.trim();
          var n = document.createElement('span');
          n.textContent = txt.slice(0, 2);
          label.textContent = '';
          label.appendChild(n);
          label.appendChild(document.createTextNode(txt.slice(2)));
          numEl = n;
        }

        var pRule = drawableRule(art, 'bottom');
        var pLabel = rise(label, { y: 12, duration: 0.5 });
        var pNum = counter(numEl, { duration: 0.66 });
        var pH3 = riseWords(h3, { duration: 0.62, stagger: 0.03 });
        var pBody = rise(body, { y: 18 });
        var pFig = scaleIn(fig, { from: 1.05, duration: 1.1 });

        onEnter(art, function () {
          var tl = gsap.timeline();
          play(tl, pFig, 0.05);
          play(tl, pLabel, 0.05);
          play(tl, pNum, 0.12);
          play(tl, pH3, 0.18);
          play(tl, pBody, 0.3);
        });

        /* De stap op leeshoogte is de actieve.

           Dezelfde leeslijn als de maatlat (62%), zodat het gouden nummer, de
           volle kop en het uiteinde van de lat altijd op dezelfde stap staan.
           Expliciete callbacks in plaats van onToggle: die vuurt alleen bij een
           wissel, dus de begintoestand werd nooit gezet.

           Alleen de kop dimt. Het label staat al op 55% inkt en zit daarmee net
           onder AA voor kleine tekst; verder dimmen maakt dat erger. Een kop van
           52px telt als grote tekst (drempel 3:1) en haalt op 55% nog 3,9:1.

           De streep onder de stap tekent zich pas bij het verlaten: hij sluit de
           stap af die je net gelezen hebt, dwars op de lat die omlaag loopt.   */
        var DOF = 'rgba(11,11,11,.55)';
        var getekend = false;
        if (numEl) gsap.set(numEl, { color: DOF });
        if (h3) gsap.set(h3, { color: DOF });

        var aan = function () {
          if (numEl) gsap.to(numEl, { color: GOLD, duration: 0.35, ease: 'power2.out' });
          if (h3) gsap.to(h3, { color: INK, duration: 0.35, ease: 'power2.out' });
        };
        var uit = function (sluit) {
          if (numEl) gsap.to(numEl, { color: DOF, duration: 0.35, ease: 'power2.out' });
          if (h3) gsap.to(h3, { color: DOF, duration: 0.35, ease: 'power2.out' });
          if (sluit && pRule && !getekend) { getekend = true; pRule(0.6).play(); }
        };
        ST.create({
          trigger: art, start: 'top 62%', end: 'bottom 62%',
          onEnter: aan, onEnterBack: aan,
          onLeave: function () { uit(true); }, onLeaveBack: function () { uit(false); }
        });
      });
    })();

    (function philosophy() {
      var s = SEC['our-philosophy']; if (!s) return;
      var h2 = s.querySelector('h2');
      var para = h2 ? h2.parentNode.querySelector('p') : null;
      var big = q(s, ':scope > div > p')[0];

      // De foto beweegt, niet het kader: dat heeft overflow:hidden.
      var pf = s.querySelector('figure');
      parallax((pf && pf.querySelector('img')) || pf, 6);

      var pH2 = riseWords(h2, { duration: 0.8, stagger: 0.032 });
      var pPara = rise(para, { y: 18 });
      onEnter(s, function () { play(play(gsap.timeline(), pH2, 0), pPara, 0.2); });

      var pBig = riseLines(big, { duration: 0.95, stagger: 0.09 });
      onEnter(big, function () { play(gsap.timeline(), pBig, 0); }, 'top 84%');
    })();

    (function roster() {
      var s = SEC['our-players']; if (!s) return;
      var h2 = s.querySelector('h2');
      var count = h2 ? h2.parentNode.querySelector('p') : null;
      var pRule = h2 ? drawableRule(h2.parentNode, 'bottom') : null;
      var pH2 = riseWords(h2, { duration: 0.7 });
      var pCount = rise(count, { y: 12, duration: 0.5 });
      onEnter(s, function () {
        var tl = gsap.timeline();
        play(tl, pH2, 0); play(tl, pCount, 0.1);
        if (pRule) tl.add(pRule(0.7), 0.05);
      });

      // The cards wipe up out of their own frame rather than just fading,
      // and each one answers to the cursor, because a roster of people
      // should feel like it can be looked through.
      var cards = q(s, 'li');
      if (!cards.length) return;
      var slots = cards.map(function (li) { return li.querySelector('div'); });
      var names = cards.map(function (li) { return q(li, 'p'); });

      gsap.set(slots, { clipPath: 'inset(100% 0% 0% 0%)' });
      // Alleen bijschriften faden in; een echte foto wipe't gewoon mee open
      // met het kader, anders zie je eerst een gat en dan pas het beeld.
      var caps = slots.map(function (d) { return d.firstElementChild; })
        .filter(function (e) { return e && e.tagName !== 'IMG'; });
      if (caps.length) gsap.set(caps, { opacity: 0 });
      var pNames = rise([].concat.apply([], names), { y: 14, duration: 0.6, stagger: 0.03 });

      cards.forEach(function (li) {
        var slot = li.querySelector('div');
        li.style.cursor = 'pointer';
        var rule = document.createElement('i');
        rule.setAttribute('aria-hidden', 'true');
        rule.style.cssText = 'display:block;height:1px;background:' + GOLD + ';transform:scaleX(0);transform-origin:left center;margin-top:14px';
        li.appendChild(rule);
        li.addEventListener('mouseenter', function () {
          gsap.to(slot, { scale: 1.035, duration: 0.5, ease: 'power2.out' });
          gsap.to(rule, { scaleX: 1, duration: 0.45, ease: 'power2.out' });
        });
        li.addEventListener('mouseleave', function () {
          gsap.to(slot, { scale: 1, duration: 0.5, ease: 'power2.out' });
          gsap.to(rule, { scaleX: 0, duration: 0.35, ease: 'power2.in' });
        });
      });

      onEnter(cards[0].parentNode, function () {
        var tl = gsap.timeline();
        tl.to(slots, {
          clipPath: 'inset(0% 0% 0% 0%)', duration: 0.85,
          stagger: { each: 0.06, from: 'start' }, ease: 'power3.inOut'
        }, 0);
        if (caps.length) tl.to(caps, { opacity: 1, duration: 0.5, stagger: 0.06 }, 0.5);
        play(tl, pNames, 0.28);
      }, 'top 88%');
    })();

    (function closing() {
      var s = SEC['closing']; if (!s) return;
      var pCta = rise(s.querySelector('a'), { y: 22 });
      LOUD.push(function () { barLines(s.querySelector('h2'), { start: 'top 80%' }); });
      onEnter(s, function () { play(gsap.timeline(), pCta, 0.55); }, 'top 78%');
    })();
  }

  /* ================= WHAT WE DO ======================================= */
  if (page === 'what-we-do') {

    // Six moments on one line. The line draws across, the markers land on
    // it in order, then the numbers and words arrive. A sequence, which is
    // what it is, rather than six things you could click.
    (function strip() {
      var s = SEC['six-part-strip']; if (!s) return;
      var track = s.querySelector('[data-track]');
      var dots = q(s, '[data-dot]');
      var nums = q(s, 'span');
      var words = q(s, 'p');

      if (track) gsap.set(track, { scaleX: 0 });
      gsap.set(dots, { scale: 0, transformOrigin: 'center center' });
      var pNums = rise(nums, { y: 8, duration: 0.45, stagger: 0.055 });
      var pWords = rise(words, { y: 12, duration: 0.55, stagger: 0.055 });

      onEnter(s, function () {
        var tl = gsap.timeline();
        if (track) tl.to(track, { scaleX: 1, duration: 1.0, ease: 'power2.inOut' }, 0);
        tl.to(dots, { scale: 1, duration: 0.4, stagger: 0.075, ease: 'back.out(2.4)' }, 0.18);
        play(tl, pNums, 0.24);
        play(tl, pWords, 0.3);
      }, 'top 90%');
    })();

    // The process as a spine. The word sits on the left of the line, a
    // numbered chip on it, the claim and the copy on the right. Whichever
    // row is at reading height is the live one; the rest sit back. The
    // drawn motif in the sticky column follows it.
    (function spine() {
      var sec = SEC['six-steps-spine']; if (!sec) return;
      var rows = q(sec, '[data-step]');
      var spineEl = sec.querySelector('[data-spine]');
      var panel = sec.querySelector('[data-motif="process"]');
      var active = sec.querySelector('[data-active]');
      if (!rows.length) return;

      /* De bal rolt de index af, aan de scroll gekoppeld.

         Eerst sprong hij alleen bij een stapwissel en stond hij daartussen
         stil — dat leest als schokken, niet als een baan. Nu hangt zijn hele
         pad aan de scrub, dus hij beweegt precies zo vloeiend als jij scrollt
         en staat nooit stil te wachten.

         De boog is |sin| over vijf sprongen tussen de zes stations: scherp bij
         de grond, rond op het hoogtepunt — de vorm van een stuit. De squash
         komt alleen in de buurt van de grond, met transformOrigin onderaan.
         Nog steeds geen bounce-easing: er is hier helemaal geen easing, de
         scroll is de easing.                                                */
      var balPaneel = sec.querySelector('[data-ball-index]');
      var bal = balPaneel ? balPaneel.querySelector('[data-ball-img]') : null;
      var balSchaduw = balPaneel ? balPaneel.querySelector('[data-ball-shadow]') : null;

      if (bal && balPaneel) {
        // Om het eigen middelpunt draaien. Stond op '50% 100%' voor de squash die
        // er niet meer is, en daardoor zwaaide de bal zijwaarts mee met de rotatie.
        gsap.set(bal, { transformOrigin: '50% 50%' });
        var stuiten = Math.max(1, rows.length - 1);
        ST.create({
          trigger: rows[0], start: 'top 55%',
          endTrigger: rows[rows.length - 1], end: 'bottom 55%',
          scrub: 0.5,
          // Ook op onRefresh, anders staat de bal tot de eerste scroll nog op
          // zijn CSS-positie (y=0) en springt hij bij de eerste beweging.
          onRefresh: teken, onUpdate: teken
        });

        function teken(self) {
            var p = Math.max(0, Math.min(1, self.progress));
            /* De stations liggen op het hart van elke rij, zodat de bal echt
               naast de stap staat die op dat moment leest. Eerder liep de reis
               van boven naar beneden door het paneel en stond hij bij stap 01
               nergens naast — dat was de misuitlijning. */
            var rijH = rows[0].offsetHeight;
            var stap = rijH;
            var start = rijH / 2 - bal.offsetHeight / 2;

            // In welk segment tussen twee stations zitten we, en hoe ver erin.
            var s = Math.min(stuiten - 1e-6, p * stuiten);
            var k = Math.floor(s), t = s - k;

            // De vloer daalt lineair; de boog is een parabool die per stuit
            // lager wordt, zoals een bal energie verliest. Bewust laag gehouden
            // (0.30 van een rijhoogte): met een grote bal wordt een hoge boog
            // een sprong door de sectie erboven heen.
            var vloer = start + (k + t) * stap;
            var hoogte = stap * 0.30 * Math.pow(0.72, k);
            var boog = 4 * t * (1 - t) * hoogte;
            var reis = stuiten * stap;

            // Geen squash: een gefotografeerde bal die vervormt leest als nep.
            gsap.set(bal, { y: vloer - boog, rotation: p * 360 * 1.6 });

            /* De schaduw blijft op de vloer liggen en reageert alleen op de
               hoogte: hoe hoger de bal, hoe kleiner en zachter. Dat is wat een
               contactschaduw echt doet en het is het enige wat de bal op de
               grond zet in plaats van ervoor te laten zweven. */
            if (balSchaduw) {
              var vlucht = hoogte > 0 ? boog / hoogte : 0;
              gsap.set(balSchaduw, {
                y: vloer + bal.offsetHeight - 20,
                scaleX: 1 - vlucht * 0.42,
                scaleY: 1 - vlucht * 0.30,
                opacity: 1 - vlucht * 0.62,
                transformOrigin: '50% 50%'
              });
            }
        }
      }

      var WORDS = rows.map(function (r) { return (r.querySelector('[data-word]').textContent || '').trim(); });

      if (spineEl) {
        ST.create({
          trigger: sec, start: 'top 78%', once: true,
          onEnter: function () { gsap.to(spineEl, { scaleY: 1, duration: 1.1, ease: 'expo.inOut' }); }
        });
      }

      // the dim lives on the row itself and the reveal only moves things,
      // so the two never fight over the same opacity
      gsap.set(rows, { opacity: 0.3 });

      function light(i) {
        rows.forEach(function (r, k) {
          var on = k === i;
          gsap.to(r, { opacity: on ? 1 : 0.3, duration: 0.4, ease: 'expo.out', overwrite: 'auto' });
          gsap.to(r.querySelector('[data-word]'), { x: on ? 0 : -10, duration: 0.45, ease: 'expo.out', overwrite: 'auto' });
          gsap.to(r.querySelector('[data-chip]'), {
            borderColor: on ? GOLD : 'rgba(11,11,11,.28)',
            color: on ? GOLD : 'rgba(11,11,11,.6)',
            scale: on ? 1.12 : 1, duration: 0.4, ease: 'expo.out', overwrite: 'auto'
          });
        });
        if (active) active.textContent = WORDS[i];
      }

      rows.forEach(function (r, i) {
        var pWord = riseWords(r.querySelector('[data-word]'), { duration: 0.55, stagger: 0.03 });
        var inner = q(r, '[data-claim], [data-body]');
        gsap.set(inner, { y: 20 });
        var chip = r.querySelector('[data-chip]');
        gsap.set(chip, { scale: 0, transformOrigin: 'center center' });

        onEnter(r, function () {
          var tl = gsap.timeline();
          tl.to(chip, { scale: 1, duration: 0.42, ease: 'back.out(2.6)' }, 0);
          play(tl, pWord, 0.06);
          tl.to(inner, { y: 0, duration: 0.6, stagger: 0.06, ease: 'expo.out' }, 0.16);
        }, 'top 88%');

        ST.create({
          trigger: r, start: 'top 58%', end: 'bottom 58%',
          onEnter: function () { light(i); }, onEnterBack: function () { light(i); }
        });
      });

      requestAnimationFrame(function () { light(0); });
    })();

    // the closing line steps across the page instead of filling it
    (function closingLine() {
      var sec = SEC['closing-line']; if (!sec) return;
      var parts = q(sec.querySelector('h2'), 'span');
      gsap.set(parts, { yPercent: 108, opacity: 0 });
      onEnter(sec, function () {
        gsap.to(parts, { yPercent: 0, opacity: 1, duration: 0.85, stagger: 0.13, ease: 'expo.out' });
      }, 'top 76%');
    })();


    /* Eén sprong zodra deze sectie in beeld komt. De schaduw springt niet mee:
       hij blijft op de grond liggen en wordt kleiner en zachter naarmate zij
       hoger komt — dat is wat een schaduw doet als het onderwerp loskomt.
       Omhoog vertraagt (power2.out), omlaag versnelt (power2.in); geen bounce,
       die leest als tekenfilm. */
    (function sprong() {
      var fig = document.querySelector('[data-register-figure]');
      if (!fig || REDUCED) return;
      var speler = fig.querySelector('img');
      var schaduw = fig.querySelector('[data-register-shadow]');
      if (!speler) return;

      var hoog = 84;
      var bezig = false;
      function springen() {
        if (bezig) return;
        bezig = true;
        var tl = gsap.timeline({ delay: 0.25, onComplete: function () { bezig = false; } });
        tl.to(speler, { y: -hoog, duration: 0.44, ease: 'power2.out' }, 0)
          .to(speler, { y: 0, duration: 0.36, ease: 'power2.in' }, 0.52);
        if (schaduw) {
          // de schaduw blijft staan: alleen kleiner en lichter op het hoogste punt
          tl.to(schaduw, { scaleX: 0.72, scaleY: 0.6, opacity: 0.42,
              transformOrigin: '50% 50%', duration: 0.44, ease: 'power2.out' }, 0)
            .to(schaduw, { scaleX: 1, scaleY: 1, opacity: 1,
              duration: 0.36, ease: 'power2.in' }, 0.52);
        }
      }
      // Niet één keer: wie terugscrollt omdat hij iets zag bewegen, krijgt het
      // opnieuw te zien. Alleen bij binnenkomen, niet doorlopend.
      // Een expliciete end is nodig: zonder end loopt het bereik door tot
      // 'bottom top' en vuurt onEnterBack pas ver onder de sectie.
      ST.create({ trigger: fig.closest('section'), start: 'top 74%', end: 'bottom 40%',
        onEnter: springen, onEnterBack: springen });
    })();

    // the five things, drawn as a register: the rail runs, the ticks drop,
    // the words land under them
    (function fiveRegister() {
      var sec = SEC['five-stage-register']; if (!sec) return;
      var rail = sec.querySelector('[data-rail]');
      var stages = q(sec, '[data-stage]');
      var loose = q(sec, ':scope > div > p');
      var head = loose[0], caption = loose[1];

      var pHead = rise(head, { y: 14, duration: 0.5 });
      var pCaption = rise(caption, { y: 22, duration: 0.65 });
      var prepared = stages.map(function (st) {
        return {
          tick: st.querySelector('[data-tick]'),
          num: st.querySelector('span'),
          word: riseWords(st.querySelector('p'), { duration: 0.55, stagger: 0.03 })
        };
      });
      prepared.forEach(function (pp) { gsap.set(pp.num, { opacity: 0, y: -6 }); });

      onEnter(sec, function () {
        var tl = gsap.timeline();
        play(tl, pHead, 0);
        if (rail) tl.to(rail, { scaleX: 1, duration: 1.0, ease: 'expo.inOut' }, 0.1);
        prepared.forEach(function (pp, i) {
          tl.to(pp.tick, { scaleY: 1, duration: 0.35, ease: 'expo.out' }, 0.32 + i * 0.09);
          tl.to(pp.num, { opacity: 1, y: 0, duration: 0.35, ease: 'expo.out' }, 0.34 + i * 0.09);
          play(tl, pp.word, 0.4 + i * 0.09);
        });
        play(tl, pCaption, 0.4 + prepared.length * 0.09);
      }, 'top 82%');
    })();

    (function closingCta() {
      var sec = SEC['closing-cta']; if (!sec) return;
      var pCta = rise(sec.querySelector('a'), { y: 22 });
      LOUD.push(function () { barLines(sec.querySelector('h2'), { start: 'top 80%' }); });
      onEnter(sec, function () { play(gsap.timeline(), pCta, 0.55); }, 'top 78%');
    })();
  }

  /* ================= ABOUT ============================================ */
  if (page === 'about') {

    // Four people on one line. The page pins and the run moves sideways
    // with the scroll, so nobody is hidden behind a click and the black
    // and paper panels slide past as blocks of colour.
    /* De horizontale run wacht op de echte fonts.

       Gemeten: gebouwd met de fallback-snit komt de pin-afstand op 1840px en
       de pagina op 4744px; zodra de fonts landen is dat 1760 en 4530. Die 80px
       corrigeerde zichzelf wel, maar tot dat moment stond de sectie zichtbaar
       verkeerd — precies het rare beeld dat je alleen bij een koude cache ziet.
       Nu wordt hij pas opgebouwd met de juiste regelmaten.                  */
    function horizontalRun() {
      var sec = document.querySelector('[data-hscroll]'); if (!sec) return;
      var vp = sec.querySelector('[data-viewport]');
      var track = sec.querySelector('[data-track]');
      var panels = q(track, '[data-panel]');
      var count = sec.querySelector('[data-count]');
      var prog = sec.querySelector('[data-progress]');
      if (!track || !panels.length) return;

      // the native horizontal scrollbar is the no-JS fallback; from here
      // the scroll position drives it instead
      vp.style.overflowX = 'hidden';

      function dist() { return Math.max(0, track.scrollWidth - window.innerWidth); }

      var run = gsap.to(track, { x: function () { return -dist(); }, ease: 'none' });

      ST.create({
        trigger: sec,
        start: 'top top',
        end: function () { return '+=' + dist(); },
        pin: true,
        scrub: 0.6,
        invalidateOnRefresh: true,
        anticipatePin: 1,
        animation: run,
        onUpdate: function (self) {
          gsap.set(prog, { scaleX: self.progress });
          var x = self.progress * dist() + window.innerWidth * 0.5;
          var acc = 0, idx = 0;
          for (var i = 0; i < panels.length; i++) {
            acc += panels[i].offsetWidth;
            if (x > acc) idx = Math.min(panels.length - 1, i + 1);
          }
          if (count) count.textContent = ('0' + (idx + 1)) + ' / 0' + panels.length;
        }
      });

      // each panel lands as it arrives, driven by the horizontal tween
      panels.forEach(function (panel) {
        var fig = panel.querySelector('figure');
        var idx = panel.querySelector('p');
        var h2 = panel.querySelector('h2');
        var role = q(panel, 'p')[1];
        var body = q(panel, 'p')[2];
        var tail = panel.querySelector('[data-tail]');

        var pFig = scaleIn(fig, { from: 1.1, duration: 0.8 });
        var pIdx = rise(idx, { y: 18, duration: 0.5 });
        var pH2 = riseWords(h2, { duration: 0.62, stagger: 0.03 });
        var pRole = rise(role, { y: 16, duration: 0.5 });
        var pBody = rise(body, { y: 26, duration: 0.7 });
        var pTailRule = drawableRule(tail, 'top');
        var pTail = rise(tail, { y: 12, duration: 0.5 });

        ST.create({
          trigger: panel, containerAnimation: run,
          start: 'left 82%', once: true,
          onEnter: function () {
            var tl = gsap.timeline();
            play(tl, pFig, 0);
            play(tl, pIdx, 0.08);
            play(tl, pH2, 0.14);
            play(tl, pRole, 0.3);
            play(tl, pBody, 0.38);
            if (pTailRule) tl.add(pTailRule(0.5), 0.5);
            play(tl, pTail, 0.6);
          }
        });
      });

      // Carl's numbers as key figures: one tile lit at a time, the rest
      // held back. The cursor takes the cycle over while it is on them.
      var tiles = q(sec, '[data-fig]');
      if (tiles.length) {
        var live = -1, hold = false, timer = null;
        function light(i) {
          if (i === live) return;
          live = i;
          tiles.forEach(function (t, k) {
            var on = k === i;
            gsap.to(t, { backgroundColor: on ? '#F2F0EA' : '#131313',
                         borderColor: on ? 'rgba(242,240,234,0)' : 'rgba(242,240,234,.10)',
                         duration: 0.34, ease: 'expo.out', overwrite: 'auto' });
            gsap.to(q(t, '[data-num], [data-cap]'), { color: on ? '#0B0B0B' : '#6A6A66',
                         duration: 0.34, ease: 'expo.out', overwrite: 'auto' });
            gsap.to(t.firstElementChild, { color: on ? 'rgba(11,11,11,.45)' : 'rgba(242,240,234,.34)',
                         duration: 0.34, overwrite: 'auto' });
          });
        }
        function cycle() {
          timer = gsap.delayedCall(1.7, function () {
            if (!hold) light((live + 1) % tiles.length);
            cycle();
          });
        }
        tiles.forEach(function (t, i) {
          t.addEventListener('mouseenter', function () { hold = true; light(i); });
          t.addEventListener('mouseleave', function () { hold = false; });
        });
        ST.create({
          trigger: tiles[0], containerAnimation: run, start: 'left 85%', once: true,
          onEnter: function () {
            gsap.from(tiles, { yPercent: 40, opacity: 0, duration: 0.6, stagger: 0.08, ease: 'expo.out' });
            light(0); cycle();
          }
        });
      }
    }
    // Alleen op de ontworpen breedte. Onder 1440px is de sectie via CSS een
    // gewone verticale lijst; dan mogen er geen pins of transforms op.
    function startHorizontalRun() {
      if (!window.matchMedia('(min-width: 1024px)').matches) return;
      horizontalRun();
    }
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(startHorizontalRun);
    else startHorizontalRun();

    (function band() {
      var s = SEC['closing-band']; if (!s) return;
      var pCta = rise(s.querySelector('a'), { y: 22 });
      LOUD.push(function () { barLines(s.querySelector('h2'), { start: 'top 80%' }); });
      onEnter(s, function () { play(gsap.timeline(), pCta, 0.5); }, 'top 80%');
    })();
  }

  /* ================= LET'S TALK ======================================= */
  if (page === 'lets-talk') {
    (function form() {
      var s = SEC['form-what-happens-next']; if (!s) return;
      var formEl = s.querySelector('form');
      var aside = s.querySelector('aside');

      if (formEl) {
        // The field underline is a background gradient rather than the
        // border, because an input cannot hold a child element to scale.
        // Same 1px, same colour, and it can be drawn and recoloured.
        var css = document.createElement('style');
        css.textContent =
          '.field{border-bottom-color:transparent !important;' +
          'background-image:linear-gradient(rgba(11,11,11,.28),rgba(11,11,11,.28));' +
          'background-repeat:no-repeat;background-position:left bottom;background-size:0% 1px;' +
          'transition:background-size .55s cubic-bezier(.22,1,.36,1),background-image .22s ease}' +
          '.field.is-drawn{background-size:100% 1px}' +
          '.field:focus{background-image:linear-gradient(' + GOLD + ',' + GOLD + ');background-size:100% 1px}';
        document.head.appendChild(css);

        var pRows = rise(Array.prototype.slice.call(formEl.children), { y: 20, stagger: 0.07 });
        onEnter(formEl, function () { play(gsap.timeline(), pRows, 0); }, 'top 84%');

        q(formEl, '.field').forEach(function (f, i) {
          ST.create({
            trigger: f, start: 'top 88%', once: true,
            onEnter: function () { gsap.delayedCall(0.12 + i * 0.05, function () { f.classList.add('is-drawn'); }); }
          });
        });
      }

      // the radio row reads as a choice, so the four options land one by one
      var roles = q(formEl || s, 'fieldset label');
      if (roles.length) {
        var pRoles = rise(roles, { y: 12, duration: 0.5, stagger: 0.07 });
        onEnter(roles[0], function () { play(gsap.timeline(), pRoles, 0); }, 'top 90%');
      }

      // the submit button gets a gold sweep the first time it appears
      var btn = formEl ? formEl.querySelector('button') : null;
      if (btn) {
        btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
        var sweep = document.createElement('i');
        sweep.setAttribute('aria-hidden', 'true');
        sweep.style.cssText = 'position:absolute;inset:0;background:linear-gradient(100deg,rgba(255,255,255,0) 38%,rgba(255,255,255,.55) 50%,rgba(255,255,255,0) 62%);transform:translateX(-110%);pointer-events:none';
        btn.appendChild(sweep);
        onEnter(btn, function () {
          gsap.to(sweep, { x: '220%', duration: 1.1, ease: 'power2.inOut', delay: 0.35 });
        }, 'top 92%');
      }

      if (aside) {
        var pRule = drawableRule(aside, 'left', { duration: 0.8 });
        var pHead = rise(aside.querySelector('p'), { y: 12, duration: 0.5 });
        var items = q(aside, 'li').map(function (li) {
          return { row: rise(li, { y: 16, duration: 0.6 }), num: counter(li.querySelector('p'), { duration: 0.5 }) };
        });
        onEnter(aside, function () {
          var tl = gsap.timeline();
          if (pRule) tl.add(pRule(0.8), 0);
          play(tl, pHead, 0.1);
          items.forEach(function (it, i) {
            play(tl, it.row, 0.25 + i * 0.12);
            play(tl, it.num, 0.32 + i * 0.12);
          });
        }, 'top 84%');
      }
    })();

    (function band() {
      var s = SEC['closing-band']; if (!s) return;
      LOUD.push(function () { barLines(s.querySelector('h2'), { start: 'top 80%' }); });
    })();
  }


  /* ---- running band ---------------------------------------------------
     Two identical sets side by side; the track walks left by exactly one
     set and loops, so the seam never shows. Slow on purpose: it is a
     texture, not a ticker.                                             */
  /* ---- marquees ---------------------------------------------------------
     Eén mechanisme voor beide banden, op dezelfde snelheid in px/s, zodat de
     gouden band bovenin en de regel onderin exact gelijk lopen.

     De set wordt zo vaak gekloond tot de track breder is dan het venster plus
     één set. Zonder dat valt er een gat: bij x = -setbreedte schuift de eerste
     kopie eruit terwijl de tweede het venster niet vult. Gemeten was set 1117px
     en track 2234px, terwijl er op 1920 al 3037px nodig is.               */
  var MQ_PX_PER_SEC = 33;

  function bouwMarquee(wrap) {
    var track = wrap.querySelector('[data-mq-track]');
    var set = wrap.querySelector('[data-mq-set]');
    if (!track || !set) return;
    var loop = null, traag = false;

    function vul() {
      if (loop) { loop.kill(); loop = null; }
      gsap.set(track, { x: 0 });
      q(track, '[data-mq-set]').forEach(function (el) { if (el !== set) track.removeChild(el); });
      var breedte = set.offsetWidth;
      if (!breedte) return;
      var nodig = window.innerWidth + breedte;
      var veiligheid = 24;
      while (track.offsetWidth < nodig && veiligheid--) track.appendChild(set.cloneNode(true));
      loop = gsap.to(track, {
        x: -breedte, duration: breedte / MQ_PX_PER_SEC, ease: 'none', repeat: -1,
        modifiers: { x: function (x) { return (parseFloat(x) % breedte) + 'px'; } }
      });
      wrap.__mqLoop = loop;
      if (window.__motionPaused) loop.pause();
      if (traag) loop.timeScale(0.25);
    }
    vul();
    // Opnieuw meten zodra de echte fonts er zijn: met de fallback is de set
    // smaller, en dan worden er te weinig kopieën gemaakt en valt er alsnog
    // een gat. Dit was niet stabiel te reproduceren — soms wel goed, soms niet.
    // Beelden landen later dan de fonts en veranderen de maten van gepinde
  // en horizontaal scrollende secties. Zonder deze meting blijven die op de
  // afmetingen van vóór het laden staan.
  window.addEventListener('load', function () { ST.refresh(); });

  if (document.fonts && document.fonts.ready) document.fonts.ready.then(vul);

    var wacht = null;
    window.addEventListener('resize', function () {
      clearTimeout(wacht); wacht = setTimeout(vul, 180);
    });

    wrap.addEventListener('mouseenter', function () {
      traag = true; if (loop) gsap.to(loop, { timeScale: 0.25, duration: 0.5 });
    });
    wrap.addEventListener('mouseleave', function () {
      traag = false; if (loop) gsap.to(loop, { timeScale: 1, duration: 0.7 });
    });
  }

  q(document, '[data-marquee], [data-mq-plain]').forEach(bouwMarquee);

    /* ---- register marks --------------------------------------------------
     One faint vertical rule down the whole page and a cross where each
     section starts. Print register, not decoration: it gives the eye
     something fixed to measure the big type against.                    */
  (function registerMarks() {
    // Let's Talk krijgt geen register: geen raster, geen rail, geen kruisjes.
    if (page === 'lets-talk') return;
    var grey = 'rgba(128,128,128,';
    var rail = document.createElement('div');
    rail.setAttribute('aria-hidden', 'true');
    rail.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:2';
    if (window.matchMedia('(min-width: 1024px)').matches) {
      rail.innerHTML = '<i style="position:absolute;left:50%;top:0;bottom:0;width:1px;background:' + grey + '.13);transform:translateX(-.5px)"></i>';
    }
    document.body.appendChild(rail);

    function cross(x) {
      return '<i style="position:absolute;left:' + x + ';top:0;width:15px;height:1px;background:' + grey + '.45);transform:translate(-7px,-.5px)"></i>' +
             '<i style="position:absolute;left:' + x + ';top:0;width:1px;height:15px;background:' + grey + '.45);transform:translate(-.5px,-7px)"></i>';
    }

    /* Het raster: 320px-module, alleen over donkere secties, hero inbegrepen.
       Eén getegelde SVG per sectie — horizontale lijn, verticale lijn en het
       kruis zitten in dezelfde tegel, met het kruis in het midden zodat hij
       heel wordt getekend en niet op een tegelrand afbreekt. z-index -1 houdt
       hem boven de sectieachtergrond en onder alle tekst; de secties hebben
       al isolation:isolate, dus hij ontsnapt niet.                        */
    var TEGEL = 320;
    var rasterSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='320' height='320'%3E"
      + "%3Cg stroke='%23808080' stroke-width='1' shape-rendering='crispEdges'%3E"
      + "%3Cpath d='M160.5 0V320' stroke-opacity='.10'/%3E"
      + "%3Cpath d='M0 160.5H320' stroke-opacity='.10'/%3E"
      + "%3Cpath d='M153 160.5h15M160.5 153v15' stroke-opacity='.42'/%3E"
      + "%3C/g%3E%3C/svg%3E";

    function isDonker(el) {
      var m = getComputedStyle(el).backgroundColor.match(/[\d.]+/g);
      if (!m) return false;
      if (m[3] !== undefined && parseFloat(m[3]) < 0.5) return false;
      return (0.2126 * m[0] + 0.7152 * m[1] + 0.0722 * m[2]) / 255 < 0.15;
    }

    // Let's Talk krijgt geen raster: daar leidt het af van het formulier.
    if (page !== 'lets-talk') q(document, 'section').forEach(function (sec) {
      if (!isDonker(sec)) return;
      if (getComputedStyle(sec).position === 'static') sec.style.position = 'relative';
      var raster = document.createElement('div');
      raster.setAttribute('aria-hidden', 'true');
      raster.setAttribute('data-grid', '');
      raster.style.cssText =
        'position:absolute;inset:0;pointer-events:none;z-index:-1;' +
        'background-image:url("' + rasterSvg + '");' +
        'background-size:' + TEGEL + 'px ' + TEGEL + 'px;' +
        'background-position:calc(50% - ' + (TEGEL / 2) + 'px) -' + (TEGEL / 2) + 'px;';
      sec.appendChild(raster);

      // Beeld staat altijd bovenop het raster, nooit andersom. De slots zijn
      // doorschijnende gradienten, dus zonder dekkende ondergrond schijnt het
      // raster erdoorheen. background-color is een longhand na de shorthand
      // in dezelfde inline declaratie, dus die wint zonder de gradient te raken.
      var vlak = getComputedStyle(sec).backgroundColor;
      q(sec, 'figure, .slot').forEach(function (el) { el.style.backgroundColor = vlak; });
    });

    q(document, 'section').forEach(function (sec, i) {
      if (i === 0) return;
      if (getComputedStyle(sec).position === 'static') sec.style.position = 'relative';
      var mark = document.createElement('div');
      mark.setAttribute('aria-hidden', 'true');
      mark.setAttribute('data-mark', '');
      mark.style.cssText = 'position:absolute;left:0;right:0;top:0;height:0;pointer-events:none;z-index:6;opacity:0';
      mark.innerHTML =
        '<i style="position:absolute;left:0;right:0;top:0;height:1px;background:' + grey + '.16)"></i>' +
        cross('50%') + cross('calc(50% - 320px)') + cross('calc(50% + 320px)');
      sec.appendChild(mark);
      ST.create({
        trigger: sec, start: 'top 96%', once: true,
        onEnter: function () { gsap.to(mark, { opacity: 1, duration: 0.6, ease: 'power2.out' }); }
      });
    });
  })();

  /* Op mobiel is het register een horizontale slider. De vijf punten staan in
     de opmaak in twee kolommen (01/03/05 en 02/04) omdat dat de desktopvorm is;
     hier zetten we ze één keer in leesvolgorde achter elkaar. Met de juiste
     DOM-volgorde is 'order' niet nodig — en zonder 'order' verspringt de
     scroller niet meer bij het laden. */
  (function registerMobiel() {
    if (!window.matchMedia('(max-width: 1023px)').matches) return;
    var fig = document.querySelector('[data-register-figure]');
    if (!fig) return;
    var rij = fig.parentElement;
    /* de foto hangt aan de sectie, niet aan de rij: die rij is op mobiel
       zelf absoluut geplaatst en zou anders haar ankerpunt worden. */
    var sectie = rij.closest('section');
    sectie.insertBefore(fig, sectie.firstChild);
    Array.prototype.slice.call(rij.querySelectorAll('[data-stage]'))
      .sort(function (a, b) { return a.dataset.stage - b.dataset.stage; })
      .forEach(function (k) { rij.appendChild(k); });
    var zin = rij.querySelector('[data-register-note]');
    if (zin) rij.closest('section').appendChild(zin);
    rij.scrollLeft = 0;
    /* De browser schuift een horizontale scroller tijdens het verticaal
       scrollen soms zelf op. Tot de bezoeker hem zelf aanraakt houden we
       hem op de eerste kaart. */
    var vrij = false;
    ['pointerdown', 'touchstart', 'wheel', 'keydown'].forEach(function (t) {
      rij.addEventListener(t, function () { vrij = true; }, { passive: true, once: true });
    });
    rij.addEventListener('scroll', function () { if (!vrij) rij.scrollLeft = 0; }, { passive: true });
  })();


  /* ---- pauzeknop ---------------------------------------------------------
     SC 2.2.2 is Level A: de gouden band, de brede tekstband en de rook in de
     footer bewegen uit zichzelf en moeten stil te zetten zijn. De marquees
     bewaren hun tween op de wrap, de rook leest de vlag in zijn eigen lus. */
  (function bewegingsknop() {
    var knop = document.querySelector('[data-motion-toggle]');
    if (!knop) return;
    var uit = false;
    function zet(stil) {
      uit = stil;
      window.__motionPaused = stil;
      q(document, '[data-marquee], [data-mq-plain]').forEach(function (wrap) {
        var loop = wrap.__mqLoop;
        if (loop) stil ? loop.pause() : loop.resume();
      });
      knop.textContent = stil ? 'Play motion' : 'Pause motion';
      knop.setAttribute('aria-pressed', stil ? 'true' : 'false');
    }
    knop.addEventListener('click', function () { zet(!uit); });
    // Wie beperkte beweging heeft ingesteld, krijgt de knop al ingedrukt.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) zet(true);
    else zet(false);
  })();



  /* ---- footer smoke ----------------------------------------------------
     A single fullscreen quad with an fbm shader. No three.js: there is no
     camera, no geometry and no light here, so a library would be 600kb to
     draw one rectangle. Falls back to plain black if WebGL is missing.  */
  (function smoke() {
    var foot = document.querySelector('footer'); if (!foot) return;
    var cvs = document.createElement('canvas');
    cvs.setAttribute('aria-hidden', 'true');
    cvs.style.cssText = 'position:absolute;inset:-1px 0 -1px 0;width:100%;height:calc(100% + 2px);display:block;pointer-events:none';
    if (getComputedStyle(foot).position === 'static') foot.style.position = 'relative';
    foot.insertBefore(cvs, foot.firstChild);
    Array.prototype.forEach.call(foot.children, function (el) {
      if (el !== cvs && getComputedStyle(el).position === 'static') {
        el.style.position = 'relative'; el.style.zIndex = '2';
      }
    });

    var gl = cvs.getContext('webgl', { antialias: false, alpha: false });
    if (!gl) return;

    var VS = 'attribute vec2 p;void main(){gl_Position=vec4(p,0.,1.);}';
    var FS = [
      'precision highp float;',
      'uniform vec2 r; uniform float t;',
      'float h(vec2 p){return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453);}',
      'float n(vec2 p){vec2 i=floor(p),f=fract(p);f=f*f*(3.-2.*f);',
      ' return mix(mix(h(i),h(i+vec2(1,0)),f.x),mix(h(i+vec2(0,1)),h(i+vec2(1,1)),f.x),f.y);}',
      'float fbm(vec2 p){float v=0.,a=.5;for(int k=0;k<6;k++){v+=a*n(p);p*=2.03;a*=.5;}return v;}',
      'void main(){',
      ' vec2 uv=gl_FragCoord.xy/r;',
      ' vec2 p=vec2(uv.x*(r.x/r.y),uv.y)*1.35;',
      ' float tt=t*0.0042;',
      ' vec2 q=vec2(fbm(p+vec2(tt*1.3,tt*0.7)),fbm(p+vec2(3.2,1.7)-tt));',
      ' float f=fbm(p+q*1.9+vec2(-tt*0.8,tt*0.5));',
      ' f=smoothstep(0.19,0.70,f);',
      ' vec3 base=vec3(0.0);',
      ' vec3 hi=mix(vec3(0.15,0.145,0.135),vec3(0.52,0.390,0.160),0.66);',
      ' vec3 col=mix(base,hi,f*1.0);',
      ' col*=1.0-smoothstep(0.30,1.0,uv.y);',
      ' col+=(h(gl_FragCoord.xy)-0.5)*0.012;',
      ' gl_FragColor=vec4(col,1.0);',
      '}'
    ].join('\n');

    function sh(type, src) {
      var o = gl.createShader(type); gl.shaderSource(o, src); gl.compileShader(o);
      return gl.getShaderParameter(o, gl.COMPILE_STATUS) ? o : null;
    }
    var vs = sh(gl.VERTEX_SHADER, VS), fs = sh(gl.FRAGMENT_SHADER, FS);
    if (!vs || !fs) return;
    var pr = gl.createProgram();
    gl.attachShader(pr, vs); gl.attachShader(pr, fs); gl.linkProgram(pr);
    if (!gl.getProgramParameter(pr, gl.LINK_STATUS)) return;
    gl.useProgram(pr);

    var buf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1, 3,-1, -1,3]), gl.STATIC_DRAW);
    var loc = gl.getAttribLocation(pr, 'p');
    gl.enableVertexAttribArray(loc);
    gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
    var uR = gl.getUniformLocation(pr, 'r'), uT = gl.getUniformLocation(pr, 't');

    var W = 0, H = 0;
    function fit() {
      var b = foot.getBoundingClientRect();
      var dpr = Math.min(1.5, window.devicePixelRatio || 1);
      W = Math.max(1, Math.floor(b.width * dpr)); H = Math.max(1, Math.floor(b.height * dpr));
      cvs.width = W; cvs.height = H;
      gl.viewport(0, 0, W, H);
    }
    fit();
    if ('ResizeObserver' in window) new ResizeObserver(fit).observe(foot);
    else window.addEventListener('resize', fit);

    var vis = false, T = 0;
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (e) { e.forEach(function (x) { vis = x.isIntersecting; }); },
        { threshold: 0.02 }).observe(foot);
    } else vis = true;

    function draw() {
      if (vis) {
        if (window.__motionPaused) { requestAnimationFrame(draw); return; }
        T += REDUCED ? 0 : 1;
        gl.uniform2f(uR, W, H);
        gl.uniform1f(uT, T);
        gl.drawArrays(gl.TRIANGLES, 0, 3);
      }
      requestAnimationFrame(draw);
    }
    draw();
  })();

  /* --- gold CTAs answer to the cursor -------------------------------- */
  q(document, 'a[href], button[type="submit"]').forEach(function (el) {
    var bg = getComputedStyle(el).backgroundColor;
    if (bg !== 'rgb(204, 157, 57)') return;
    el.addEventListener('mouseenter', function () {
      gsap.to(el, { y: -3, backgroundColor: '#DCAE4B', duration: 0.28, ease: 'power2.out' });
    });
    el.addEventListener('mouseleave', function () {
      gsap.to(el, { y: 0, backgroundColor: GOLD, duration: 0.32, ease: 'power2.out' });
    });
  });

  /* --- fonts land late and change every line box ----------------------- */
  function runLoud() {
    LOUD.splice(0).forEach(function (fn) { try { fn(); } catch (e) {} });
    ST.refresh();
  }
  if (document.fonts && document.fonts.ready) {
    var armed = false;
    var arm = function () { if (armed) return; armed = true; runLoud(); };
    document.fonts.ready.then(arm);
    setTimeout(arm, 1600);
  } else runLoud();
  window.addEventListener('load', function () { ST.refresh(); });
})();
