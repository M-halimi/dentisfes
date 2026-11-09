import './patients.js';

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('motion-ready');

    document.querySelectorAll('[data-clinic-tour]').forEach((container) => {
        const video = container.querySelector('video');
        const playButton = container.querySelector('[data-tour-play]');
        const errorMessage = container.querySelector('[data-tour-error]');

        if (!video || !playButton) return;

        video.controls = false;
        playButton.hidden = false;

        playButton.addEventListener('click', async () => {
            if (errorMessage) errorMessage.hidden = true;
            playButton.hidden = true;
            video.controls = true;

            try {
                await video.play();
                video.focus();
            } catch {
                video.controls = false;
                playButton.hidden = false;
                if (errorMessage) errorMessage.hidden = false;
            }
        });

        video.addEventListener('play', () => {
            video.controls = true;
            playButton.hidden = true;
            container.classList.add('is-playing');
            if (errorMessage) errorMessage.hidden = true;
        });

        video.addEventListener('ended', () => {
            video.controls = false;
            playButton.hidden = false;
            container.classList.remove('is-playing');
        });

        const showError = () => {
            video.controls = false;
            playButton.hidden = true;
            if (errorMessage) errorMessage.hidden = false;
        };

        video.addEventListener('error', showError);
        video.querySelector('source')?.addEventListener('error', showError);
    });

    const revealItems = [...document.querySelectorAll('[data-reveal]')];
    const sectionItems = [...document.querySelectorAll('main > section')];
    const observedItems = [...new Set([...revealItems, ...sectionItems])];

    const addMotionClass = (elements, className, stagger = 0) => {
        elements.forEach((element, index) => {
            element.classList.add(className);
            if (stagger) {
                element.style.setProperty('--motion-delay', `${Math.min(index * stagger, 420)}ms`);
            }
        });
    };

    addMotionClass(revealItems, 'motion-item');
    addMotionClass(sectionItems, 'motion-section');

    document.querySelectorAll('.topbar, .site-header').forEach((element, index) => {
        element.classList.add('motion-nav-item');
        element.style.setProperty('--motion-delay', `${index * 70}ms`);
    });

    const firstSection = document.querySelector('main > section:first-of-type');
    if (firstSection) {
        addMotionClass([...firstSection.querySelectorAll(
            'h1, p, [class*="kicker"], [class*="actions"], [class*="note"], [class*="badge"]'
        )], 'motion-intro-item', 95);

        const firstHeading = firstSection.querySelector('h1');
        if (firstHeading) {
            firstHeading.classList.add('motion-heading');
            firstHeading.querySelectorAll('span').forEach((span) => span.classList.add('motion-heading-accent'));
        }

        addMotionClass([...firstSection.querySelectorAll(
            '.home-hero-frame, .services-detail-hero-image, .about-locale-hero-photo, .contact-locale-hero-art, .appointment-hero-main-image, .appointment-hero-secondary-image, .appointment-hero-detail-image, .anas-about__hero-photo, .hero-image'
        )], 'motion-image');
    }

    addMotionClass([...document.querySelectorAll(
        '.about-media-frame, .about-media-inset, .about-locale-image-card, .about-locale-specialty-art, .service-detail-media, .home-why-image, .home-process-visual, .home-case, .contact-locale-map, .appointment-aside-card, .anas-about__profile-photo, .anas-about__gallery-item'
    )], 'motion-image', 70);

    document.querySelectorAll('main h2, main h3').forEach((heading) => {
        heading.classList.add('motion-subheading');
    });

    addMotionClass([...document.querySelectorAll(
        '.button, .home-ghost-link, .about-text-link, .about-locale-link, .services-detail-contact-link, .services-detail-cta-actions a, .about-cta-phone, .anas-about__outline-link'
    )], 'motion-action', 80);

    requestAnimationFrame(() => {
        document.querySelectorAll('.motion-nav-item').forEach((element) => element.classList.add('is-visible'));
        firstSection?.classList.add('is-visible');
    });

    if ('IntersectionObserver' in window && observedItems.length) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0, rootMargin: '0px 0px -36px' });

        observedItems.forEach((item) => revealObserver.observe(item));
    } else {
        observedItems.forEach((item) => item.classList.add('is-visible'));
    }

    const toggle = document.querySelector('[data-menu-toggle]');
    const nav = document.querySelector('[data-mobile-nav]');

    if (toggle && nav) {
        const closeMenu = () => {
            nav.classList.remove('open');
            document.body.classList.remove('menu-open');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('open');
            document.body.classList.toggle('menu-open', open);
            toggle.setAttribute('aria-expanded', String(open));
        });

        nav.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeMenu();
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 820) closeMenu();
        });
    }

    const lightbox = document.querySelector('[data-lightbox-modal]');
    const lightboxImage = lightbox?.querySelector('img');
    const lightboxCaption = lightbox?.querySelector('[data-lightbox-caption]');
    const closeLightbox = () => {
        if (!lightbox) return;
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('menu-open');
        if (lightboxImage) lightboxImage.src = '';
    };

    if (lightbox && lightboxImage) {
        document.querySelectorAll('[data-lightbox]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                lightboxImage.src = trigger.dataset.lightbox;
                lightboxImage.alt = trigger.querySelector('img')?.alt || '';
                if (lightboxCaption) lightboxCaption.textContent = trigger.dataset.caption || '';
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
            });
        });

        lightbox.querySelector('[data-lightbox-close]')?.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) closeLightbox();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeLightbox();
        });
    }
});
