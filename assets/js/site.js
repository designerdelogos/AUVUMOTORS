// AUTO STORE — slider infinito do hero + animações leves
(function(){
  function init(){
    const carousel = document.getElementById('hero-carousel');
    if (!carousel) return;
    const track = carousel.querySelector('.degra-hero-track');
    if (!track) return;

    const originals = Array.from(track.children);
    const total = originals.length;
    const dots = Array.from(carousel.querySelectorAll('.degra-hero-dot'));
    const prev = carousel.querySelector('.degra-hero-prev');
    const next = carousel.querySelector('.degra-hero-next');
    let index = 1;
    let timer = null;
    let moving = false;

    track.style.display = 'flex';
    track.style.willChange = 'transform';
    track.style.animation = 'none';
    originals.forEach(s => { s.style.flex = '0 0 100%'; s.style.width = '100%'; });

    if (total <= 1) {
      track.style.transform = 'translate3d(0,0,0)';
      return;
    }

    const firstClone = originals[0].cloneNode(true);
    const lastClone = originals[total - 1].cloneNode(true);
    firstClone.setAttribute('aria-hidden', 'true');
    lastClone.setAttribute('aria-hidden', 'true');
    firstClone.style.flex = '0 0 100%';
    firstClone.style.width = '100%';
    lastClone.style.flex = '0 0 100%';
    lastClone.style.width = '100%';
    track.appendChild(firstClone);
    track.insertBefore(lastClone, originals[0]);

    const setPosition = (withTransition = true) => {
      track.style.transition = withTransition ? '' : 'none';
      track.style.transform = `translate3d(-${index * 100}%,0,0)`;
      if (!withTransition) {
        track.offsetHeight;
        track.style.transition = '';
      }
    };

    const activeIndex = () => {
      if (index === 0) return total - 1;
      if (index === total + 1) return 0;
      return index - 1;
    };

    const updateDots = () => {
      const active = activeIndex();
      dots.forEach((dot, i) => dot.classList.toggle('is-active', i === active));
    };

    const goTo = (nextIndex) => {
      if (moving) return;
      moving = true;
      index = nextIndex;
      updateDots();
      setPosition(true);
    };

    const stop = () => {
      if (timer) window.clearInterval(timer);
      timer = null;
    };

    const start = () => {
      stop();
      timer = window.setInterval(() => goTo(index + 1), 4500);
    };

    const navigate = (nextIndex) => {
      goTo(nextIndex + 1);
      start();
    };

    [...dots, prev, next].forEach((el) => { if (el) el.style.display = ''; });
    dots.forEach((dot, i) => dot.addEventListener('click', () => navigate(i)));
    prev?.addEventListener('click', () => { goTo(index - 1); start(); });
    next?.addEventListener('click', () => { goTo(index + 1); start(); });
    track.addEventListener('transitionend', () => {
      if (index === 0) {
        index = total;
        setPosition(false);
      } else if (index === total + 1) {
        index = 1;
        setPosition(false);
      }
      updateDots();
      moving = false;
    });
    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', start);

    setPosition(false);
    updateDots();
    start();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

// Reveal on scroll para grades de produtos
(function(){
  if (!('IntersectionObserver' in window)) return;
  const rows = document.querySelectorAll('.degra-product-grid');
  if (!rows.length) return;
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.08 });
  rows.forEach((row) => observer.observe(row));
})();
