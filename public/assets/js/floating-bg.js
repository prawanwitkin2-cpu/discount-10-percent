(function () {
  const layer = document.querySelector('.floating-cafe-bg');
  if (!layer) return;

  const icons = ['☕', '🧁', '🍰', '🥐', '🍪', '🫖'];
  const count = window.matchMedia('(max-width: 760px)').matches ? 14 : 22;
  const particles = [];

  function rand(min, max) {
    return Math.random() * (max - min) + min;
  }

  for (let i = 0; i < count; i += 1) {
    const el = document.createElement('span');
    el.className = 'float-icon';
    el.textContent = icons[Math.floor(Math.random() * icons.length)];

    const size = rand(44, 92);
    const left = rand(-4, 96);
    const top = rand(-8, 90);
    const alpha = rand(0.16, 0.28);

    el.style.left = left + '%';
    el.style.top = top + '%';
    el.style.fontSize = size + 'px';
    el.style.opacity = alpha.toFixed(2);

    layer.appendChild(el);
    particles.push({
      el: el,
      ampX: rand(8, 30),
      ampY: rand(10, 34),
      phaseX: rand(0, Math.PI * 2),
      phaseY: rand(0, Math.PI * 2),
      speedX: rand(0.35, 0.85),
      speedY: rand(0.22, 0.64),
      rotAmp: rand(2, 8),
      rotSpeed: rand(0.25, 0.7),
      scaleAmp: rand(0.01, 0.05),
      driftSeed: rand(0, Math.PI * 2),
    });
  }

  function tick(now) {
    const t = now * 0.001;
    for (let i = 0; i < particles.length; i += 1) {
      const p = particles[i];
      const x =
        Math.sin(t * p.speedX + p.phaseX) * p.ampX +
        Math.sin(t * (p.speedX * 0.41) + p.driftSeed) * (p.ampX * 0.38);
      const y =
        Math.cos(t * p.speedY + p.phaseY) * p.ampY +
        Math.sin(t * (p.speedY * 0.52) + p.driftSeed * 1.7) * (p.ampY * 0.32);
      const r = Math.sin(t * p.rotSpeed + p.phaseX) * p.rotAmp;
      const s = 1 + Math.sin(t * (p.speedY * 0.75) + p.phaseY) * p.scaleAmp;
      p.el.style.transform =
        'translate3d(' + x.toFixed(2) + 'px,' + y.toFixed(2) + 'px,0) rotate(' + r.toFixed(2) + 'deg) scale(' + s.toFixed(3) + ')';
    }
    window.requestAnimationFrame(tick);
  }

  window.requestAnimationFrame(tick);
})();
