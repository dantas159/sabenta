(() => {
  "use strict";

  // Mobile menu toggle
  const menuBtn = document.getElementById("menu-toggle-btn");
  const menu = document.getElementById("mobile-menu");
  let menuOpen = false;

  function closeMenu() {
    if (!menu) return;
    menu.style.display = "none";
    menuOpen = false;
  }

  if (menuBtn && menu) {
    menuBtn.addEventListener("click", () => {
      menuOpen = !menuOpen;
      menu.style.display = menuOpen ? "flex" : "none";
    });
    menu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", closeMenu);
    });
  }

  // Scroll-triggered reveal animations
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const revealEls = document.querySelectorAll("[data-reveal]");
  const revealAll = () => revealEls.forEach((el) => el.classList.add("is-in"));

  if (!reduceMotion && "IntersectionObserver" in window) {
    document.body.classList.add("js-reveal");
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-in");
            io.unobserve(entry.target);
          }
        });
      },
      { rootMargin: "0px 0px -12% 0px", threshold: 0.08 }
    );
    revealEls.forEach((el) => io.observe(el));
    // Fail-safe: if the observer never reports, reveal everything anyway.
    window.setTimeout(revealAll, 1400);
  } else {
    revealAll();
  }

  // Header shadow + floating WhatsApp CTA visibility on scroll
  const header = document.getElementById("site-header");
  const floatCta = document.getElementById("floating-cta");
  let ticking = false;

  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(() => {
      ticking = false;
      const y = window.scrollY || document.documentElement.scrollTop || 0;

      if (header) {
        const scrolled = y > 12;
        header.style.borderBottomColor = scrolled ? "var(--color-divider)" : "transparent";
        header.style.boxShadow = scrolled ? "0 1px 12px color-mix(in srgb, #183054 8%, transparent)" : "none";
      }

      if (floatCta) {
        // On small screens the WhatsApp CTA stays reachable at all times.
        const visible = y > 700 || window.innerWidth <= 900;
        floatCta.style.opacity = visible ? "1" : "0";
        floatCta.style.pointerEvents = visible ? "auto" : "none";
        floatCta.style.transform = visible ? "none" : "translateY(12px)";
      }
    });
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll, { passive: true });
  onScroll();
})();
