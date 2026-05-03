// ── Navbar scroll ─────────────────────────────────────
const header = document.getElementById('header');
if (header) {
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 40);
  }, { passive: true });
}

// ── Mobile menu ───────────────────────────────────────
const hamburger    = document.querySelector('.hamburger');
const mobileMenu   = document.querySelector('.mobile-menu');
const mobileClose  = document.querySelector('.mobile-close');

hamburger?.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu?.classList.toggle('open');
  document.body.style.overflow = mobileMenu?.classList.contains('open') ? 'hidden' : '';
});
mobileClose?.addEventListener('click', closeMobile);
mobileMenu?.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMobile));
function closeMobile() {
  hamburger?.classList.remove('open');
  mobileMenu?.classList.remove('open');
  document.body.style.overflow = '';
}

// ── Scroll reveal ─────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
  entries.forEach((e, i) => {
    if (e.isIntersecting) {
      setTimeout(() => e.target.classList.add('revealed'), i * 80);
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el));

// ── Active nav link ───────────────────────────────────
const sections = document.querySelectorAll('section[id]');
const navLinks  = document.querySelectorAll('.nav-links a[href^="#"]');

window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(sec => {
    if (window.scrollY >= sec.offsetTop - 100) current = sec.id;
  });
  navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === `#${current}`));
}, { passive: true });

// ── Counter animation ─────────────────────────────────
function animateCounter(el) {
  const target = parseInt(el.dataset.target || el.textContent);
  const suffix = el.dataset.suffix || '';
  const prefix = el.dataset.prefix || '';
  let start    = 0;
  const step   = target / 50;
  const timer  = setInterval(() => {
    start += step;
    if (start >= target) { el.textContent = prefix + target + suffix; clearInterval(timer); return; }
    el.textContent = prefix + Math.floor(start) + suffix;
  }, 30);
}

const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      animateCounter(e.target);
      counterObserver.unobserve(e.target);
    }
  });
}, { threshold: 0.5 });

document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

// ── Load latest blog posts (homepage) ─────────────────
async function loadLatestPosts() {
  const container = document.getElementById('latest-posts');
  if (!container) return;

  try {
    const res  = await fetch('/api/posts?limit=3');
    const data = await res.json();
    const posts = data.posts || [];

    if (!posts.length) {
      container.innerHTML = '<p style="color:var(--dim);text-align:center;padding:2rem">Henüz yazı yok.</p>';
      return;
    }
    container.innerHTML = posts.map(p => blogCardHTML(p)).join('');
  } catch {
    container.innerHTML = '<p style="color:var(--dim);text-align:center;padding:2rem">Yazılar yüklenemedi.</p>';
  }
}

function blogCardHTML(p) {
  const date = new Date(p.createdAt).toLocaleDateString('tr-TR', { day:'numeric', month:'long', year:'numeric' });
  const words = p.content?.replace(/<[^>]+>/g,'').split(' ').length || 0;
  const readMin = Math.max(1, Math.ceil(words / 200));
  const img = p.coverImage
    ? `<img src="${p.coverImage}" alt="${escHtml(p.title)}" loading="lazy">`
    : `<div class="blog-card-img-placeholder">📝</div>`;

  return `
  <article class="blog-card" data-reveal>
    <a href="/blog/${p.slug}">
      <div class="blog-card-img">${img}</div>
    </a>
    <div class="blog-card-body">
      <div class="blog-card-meta">
        <span class="badge badge-accent">${escHtml(p.category)}</span>
        <time>${date}</time>
      </div>
      <a href="/blog/${p.slug}"><h3>${escHtml(p.title)}</h3></a>
      <p>${escHtml(p.excerpt || '')}</p>
      <div class="blog-card-footer">
        <a href="/blog/${p.slug}" class="read-more">Okumaya Devam <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        <span class="read-time">${readMin} dk okuma</span>
      </div>
    </div>
  </article>`;
}

function escHtml(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadLatestPosts();

// ── Contact form ──────────────────────────────────────
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn    = document.getElementById('cf-submit');
    const result = document.getElementById('cf-result');
    btn.disabled = true;
    btn.textContent = 'Gönderiliyor...';
    result.style.display = 'none';

    try {
      const res  = await fetch('/contact.php', { method: 'POST', body: new FormData(contactForm) });
      const data = await res.json();
      result.style.display = 'block';
      if (data.success) {
        result.className = 'alert alert-success';
        result.textContent = data.message || 'Mesajınız alındı!';
        contactForm.reset();
      } else {
        result.className = 'alert alert-danger';
        result.textContent = data.error || 'Bir hata oluştu.';
      }
    } catch {
      result.style.display = 'block';
      result.className = 'alert alert-danger';
      result.textContent = 'Bağlantı hatası. Lütfen tekrar deneyin.';
    }
    btn.disabled = false;
    btn.textContent = 'Mesaj Gönder ✉️';
  });
}
