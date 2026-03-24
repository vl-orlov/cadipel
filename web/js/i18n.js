async function setLang(page, lang) {
  const currentLangEl = document.getElementById('current_lang');
  if (currentLangEl) {
    currentLangEl.textContent = lang.toUpperCase();
  }
  localStorage.setItem('lang', lang);

  try {
    const url = `lang/${page}/${lang}.json`;
    const res = await fetch(url);

    if (!res.ok) {
      throw new Error(`Failed to load ${url}, status ${res.status}`);
    }

    const dict = await res.json();
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (dict[key]) {
        el.textContent = dict[key];
      }
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
      const key = el.getAttribute('data-i18n-placeholder');
      if (dict[key]) {
        el.setAttribute('placeholder', dict[key]);
      }
    });
    document.querySelectorAll('[data-i18n-html]').forEach(el => {
      const key = el.getAttribute('data-i18n-html');
      if (dict[key]) {
        el.innerHTML = dict[key];
      }
    });
    document.querySelectorAll('[data-i18n-aria-label]').forEach(el => {
      const key = el.getAttribute('data-i18n-aria-label');
      if (dict[key]) {
        el.setAttribute('aria-label', dict[key]);
      }
    });

  } catch (err) {
    console.error(`Language load error for ${lang}:`, err);
  }
}

const supportedLangs = ['es', 'en'];

function initLang(page = 'landing', defaultLang = 'es') {
  const storedLang = localStorage.getItem('lang');
  const browserLang = (navigator.language || '').split('-')[0];

  const lang = storedLang ?? (supportedLangs.includes(browserLang) ? browserLang : defaultLang);

  setLang(page, lang);
}

/** Smooth scroll from hero subpages (.nosotros_page_hero) to the block directly below the hero. */
function initHeroNosotrosScroll() {
  document.querySelectorAll('.nosotros_page_hero .hero_nosotros_scroll').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const hero = this.closest('.nosotros_page_hero');
      const target = hero?.nextElementSibling;
      if (target && target.nodeType === Node.ELEMENT_NODE) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initHeroNosotrosScroll);
} else {
  initHeroNosotrosScroll();
}
