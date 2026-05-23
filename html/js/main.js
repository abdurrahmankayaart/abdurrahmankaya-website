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

// ── Admin sidebar mobil toggle ────────────────────────
const adminToggle  = document.getElementById('adminSidebarToggle');
const adminSidebar = document.getElementById('adminSidebar');
const adminOverlay = document.getElementById('adminOverlay');

function openAdminSidebar() {
  adminSidebar?.classList.add('open');
  adminOverlay?.classList.add('visible');
}
function closeAdminSidebar() {
  adminSidebar?.classList.remove('open');
  adminOverlay?.classList.remove('visible');
}

adminToggle?.addEventListener('click', openAdminSidebar);
adminOverlay?.addEventListener('click', closeAdminSidebar);
adminSidebar?.querySelectorAll('.admin-nav-item').forEach(a => a.addEventListener('click', closeAdminSidebar));

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
