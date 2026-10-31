document.addEventListener('DOMContentLoaded', () => {
    const slides = [...document.querySelectorAll('.hero-slide')];
    const dots = [...document.querySelectorAll('.slider-dots button')];
    let current = 0;
    function showSlide(index) {
        slides.forEach((slide, i) => slide.classList.toggle('active', i === index));
        dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
        current = index;
    }
    document.querySelector('.slider-arrow.next')?.addEventListener('click', () => showSlide((current + 1) % slides.length));
    document.querySelector('.slider-arrow.prev')?.addEventListener('click', () => showSlide((current - 1 + slides.length) % slides.length));
    dots.forEach((dot, i) => dot.addEventListener('click', () => showSlide(i)));
    if (slides.length > 1) setInterval(() => showSlide((current + 1) % slides.length), 7000);

    const counters = document.querySelectorAll('[data-count]');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target, target = Number(el.dataset.count);
            let value = 0;
            const timer = setInterval(() => {
                value = Math.min(target, value + Math.max(1, Math.ceil(target / 50)));
                el.textContent = value.toLocaleString();
                if (value === target) clearInterval(timer);
            }, 25);
            observer.unobserve(el);
        });
    }, { threshold: 0.5 });
    counters.forEach(counter => observer.observe(counter));
});
