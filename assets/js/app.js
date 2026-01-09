document.addEventListener('DOMContentLoaded', () => {
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, { threshold: 0.2 });
    reveals.forEach(el => obs.observe(el));
  }

  const lb = document.getElementById('lightbox');
  const galleryThumbs = Array.from(document.querySelectorAll('[data-gallery-thumb]'));
  const gallerySources = galleryThumbs.map(btn => btn.getAttribute('data-src')).filter(Boolean);
  let lbIndex = 0;

  const openLightbox = (index) => {
    if (!lb || !gallerySources.length) return;
    lbIndex = Math.max(0, Math.min(index, gallerySources.length - 1));
    lb.querySelector('img').src = gallerySources[lbIndex];
    lb.classList.add('open');
    lb.setAttribute('aria-hidden', 'false');
  };
  const closeLightbox = () => {
    if (!lb) return;
    lb.classList.remove('open');
    lb.setAttribute('aria-hidden', 'true');
  };
  const stepLightbox = (delta) => {
    if (!lb || !gallerySources.length) return;
    lbIndex = (lbIndex + delta + gallerySources.length) % gallerySources.length;
    lb.querySelector('img').src = gallerySources[lbIndex];
  };

  galleryThumbs.forEach((btn, i) => {
    btn.addEventListener('click', () => openLightbox(i));
  });
  if (lb) {
    lb.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    const prev = lb.querySelector('.lightbox-nav.prev');
    const next = lb.querySelector('.lightbox-nav.next');
    if (prev) prev.addEventListener('click', () => stepLightbox(-1));
    if (next) next.addEventListener('click', () => stepLightbox(1));
    lb.addEventListener('click', (e) => {
      if (e.target === lb) closeLightbox();
    });
    let startX = 0;
    let startY = 0;
    lb.addEventListener('touchstart', (e) => {
      const t = e.changedTouches[0];
      startX = t.clientX;
      startY = t.clientY;
    }, { passive: true });
    lb.addEventListener('touchend', (e) => {
      const t = e.changedTouches[0];
      const dx = t.clientX - startX;
      const dy = t.clientY - startY;
      if (Math.abs(dx) < 30 || Math.abs(dx) < Math.abs(dy)) return;
      if (dx < 0) stepLightbox(1);
      if (dx > 0) stepLightbox(-1);
    });
  }

  document.querySelectorAll('[data-copy]').forEach(btn => {
    btn.addEventListener('click', () => {
      copyText(btn.getAttribute('data-copy') || '').then(() => showToast('Kopyalandi')).catch(() => showToast('Kopyalama mumkun olmadi'));
      trackEvent('copy_order_text');
    });
  });

  document.querySelectorAll('[data-track]').forEach(btn => {
    btn.addEventListener('click', () => trackEvent(btn.getAttribute('data-track')));
  });

  document.querySelectorAll('[data-track-form]').forEach(form => {
    form.addEventListener('submit', () => trackEvent(form.getAttribute('data-track-form')));
  });

  document.querySelectorAll('[data-ig-order]').forEach(btn => {
    btn.addEventListener('click', () => {
      const text = btn.getAttribute('data-product') || '';
      copyText(text).then(() => showToast('Kopyalandi')).catch(() => showToast('Kopyalama mumkun olmadi'));
      trackEvent('instagram_click');
      const ig = document.querySelector('a[data-ig-link]');
      if (ig) window.open(ig.getAttribute('href'), '_blank');
    });
  });

  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      const id = tab.getAttribute('data-tab');
      const content = document.getElementById('tab-' + id);
      if (content) content.classList.add('active');
    });
  });

  document.querySelectorAll('.acc-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.nextElementSibling.classList.toggle('open');
    });
  });

  const countdown = document.querySelector('[data-countdown]');
  if (countdown) {
    const target = new Date(countdown.getAttribute('data-countdown'));
    const tick = () => {
      const now = new Date();
      const diff = Math.max(0, target - now);
      const d = Math.floor(diff / (1000 * 60 * 60 * 24));
      const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
      const m = Math.floor((diff / (1000 * 60)) % 60);
      const s = Math.floor((diff / 1000) % 60);
      countdown.querySelector('[data-days]').textContent = String(d).padStart(2, '0');
      countdown.querySelector('[data-hours]').textContent = String(h).padStart(2, '0');
      countdown.querySelector('[data-mins]').textContent = String(m).padStart(2, '0');
      countdown.querySelector('[data-secs]').textContent = String(s).padStart(2, '0');
    };
    tick();
    setInterval(tick, 1000);
  }

  const menuToggle = document.querySelector('.mobile-toggle');
  const drawer = document.getElementById('mobile-drawer');
  const closeTargets = document.querySelectorAll('[data-mobile-close]');
  const openMenu = () => {
    document.body.classList.add('menu-open');
    if (drawer) drawer.setAttribute('aria-hidden', 'false');
    if (menuToggle) menuToggle.setAttribute('aria-expanded', 'true');
  };
  const closeMenu = () => {
    document.body.classList.remove('menu-open');
    if (drawer) drawer.setAttribute('aria-hidden', 'true');
    if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
  };
  if (menuToggle && drawer) {
    menuToggle.addEventListener('click', openMenu);
  }
  closeTargets.forEach(el => {
    el.addEventListener('click', closeMenu);
  });
  document.querySelectorAll('.mobile-nav a').forEach(link => {
    link.addEventListener('click', closeMenu);
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });

  const favKey = 'sj_favs';
  const favButtons = Array.from(document.querySelectorAll('[data-fav]'));
  const favCount = document.querySelector('[data-fav-count]');
  const favPage = document.querySelector('[data-favs-page]');
  const favList = document.querySelector('[data-favs-list]');
  const favEmpty = document.querySelector('[data-favs-empty]');
  const favWa = document.querySelector('[data-fav-wa]');
  const favIg = document.querySelector('[data-fav-ig]');

  const loadFavs = () => {
    try {
      const items = JSON.parse(localStorage.getItem(favKey) || '[]');
      return Array.isArray(items) ? items : [];
    } catch {
      return [];
    }
  };
  const saveFavs = (items) => {
    localStorage.setItem(favKey, JSON.stringify(items));
  };
  const updateFavCount = (items) => {
    if (favCount) favCount.textContent = String(items.length);
  };
  const buildFavMessage = (items) => {
    if (!items.length) return 'Sevdiklerim siyahisi bosdur.';
    const lines = items.map((item, i) => {
      const priceText = item.priceCurrent || item.price;
      return `${i + 1}) ${item.title} - ${priceText}`;
    });
    return `Salam! Sevdiklerim siyahisi:\n${lines.join('\n')}`;
  };
  const renderFavsPage = (items) => {
    if (!favPage || !favList) return;
    favList.innerHTML = '';
    if (!items.length) {
      if (favEmpty) favEmpty.style.display = 'block';
    } else if (favEmpty) {
      favEmpty.style.display = 'none';
    }
    items.forEach(item => {
      const card = document.createElement('div');
      card.className = 'card product-card';
      card.innerHTML = `
        <button class="fav-toggle active" type="button" data-fav-remove data-id="${item.id}">
          <span class="fav-icon">\u2764</span>
        </button>
        <a href="product.php?id=${item.id}">
          <img src="${item.image}" alt="${item.title}" loading="lazy" />
        </a>
        <a href="product.php?id=${item.id}">
          <h3>${item.title}</h3>
        </a>
        <div class="price">
          <span>${item.priceCurrent || item.price}</span>${item.priceOld ? ` <span class=\"old\">${item.priceOld}</span>` : ''}
        </div>
      `;
      favList.appendChild(card);
    });
    const msg = buildFavMessage(items);
    if (favWa) {
      const waBase = favWa.getAttribute('data-wa') || '';
      favWa.setAttribute('href', waBase + encodeURIComponent(msg));
      favWa.classList.toggle('disabled', !items.length);
    }
    if (favIg) {
      favIg.classList.toggle('disabled', !items.length);
    }
  };
  const setButtonState = (items) => {
    favButtons.forEach(btn => {
      const id = btn.getAttribute('data-id');
      const active = items.some(item => item.id === id);
      btn.classList.toggle('active', active);
      const icon = btn.querySelector('.fav-icon');
      if (icon) icon.textContent = active ? '\u2764' : '\u2661';
    });
  };

  let favs = loadFavs().map(item => {
    if (!item || typeof item !== 'object') return item;
    if (!item.priceCurrent) {
      if (item.priceHtml) {
        const currentMatch = item.priceHtml.match(/<span>([^<]+)<\/span>/);
        const oldMatch = item.priceHtml.match(/old\">([^<]+)</);
        item.priceCurrent = currentMatch ? currentMatch[1].trim() : (item.price || '');
        item.priceOld = oldMatch ? oldMatch[1].trim() : (item.priceOld || '');
      } else {
        item.priceCurrent = item.price || '';
      }
    }
    if (!item.priceOld) item.priceOld = '';
    return item;
  });
  saveFavs(favs);
  updateFavCount(favs);
  renderFavsPage(favs);
  setButtonState(favs);

  favButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const id = btn.getAttribute('data-id');
      const title = btn.getAttribute('data-title') || '';
      const image = btn.getAttribute('data-image') || '';
      const price = btn.getAttribute('data-price') || '';
      const priceCurrent = btn.getAttribute('data-price-current') || price;
      const priceOld = btn.getAttribute('data-price-old') || '';
      const exists = favs.some(item => item.id === id);
      if (exists) {
        favs = favs.filter(item => item.id !== id);
      } else {
        favs = [...favs, { id, title, image, price, priceCurrent, priceOld }];
      }
      saveFavs(favs);
      updateFavCount(favs);
      renderFavsPage(favs);
      setButtonState(favs);
    });
  });

  document.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('[data-fav-remove]');
    if (!removeBtn) return;
    const id = removeBtn.getAttribute('data-id');
    favs = favs.filter(item => item.id !== id);
    saveFavs(favs);
    updateFavCount(favs);
    renderFavsPage(favs);
    setButtonState(favs);
  });

});

function trackEvent(name) {
  if (window.gtag) {
    gtag('event', name);
  }
}

