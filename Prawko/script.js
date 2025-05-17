document.addEventListener('DOMContentLoaded', () => {
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Mobile navigation toggle
    const navToggle = document.createElement('div');
    navToggle.classList.add('nav-toggle');
    navToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.querySelector('nav').appendChild(navToggle);

    navToggle.addEventListener('click', () => {
        const navUl = document.querySelector('nav ul');
        navUl.classList.toggle('active');
        navToggle.querySelector('i').classList.toggle('fa-bars');
        navToggle.querySelector('i').classList.toggle('fa-times');
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Animate features and services
    document.querySelectorAll('.feature, .service-item').forEach(element => {
        element.classList.add('hidden');
        observer.observe(element);
    });

    // Form validation (placeholder for login/registration forms)
    const validateForm = (form) => {
        const inputs = form.querySelectorAll('input');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('error');
            } else {
                input.classList.remove('error');
            }
        });

        return isValid;
    };

    // Example of form submission handling
    const loginForm = document.getElementById('login-form');
    const registrationForm = document.getElementById('registration-form');

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (validateForm(loginForm)) {
                // Implement login logic or AJAX submission
                console.log('Login form submitted');
            }
        });
    }

    if (registrationForm) {
        registrationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (validateForm(registrationForm)) {
                // Implement registration logic or AJAX submission
                console.log('Registration form submitted');
            }
        });
    }

    // Logo click refresh
    document.querySelector('.logo').addEventListener('click', function() {
        window.location.reload();
    });

    // Animate elements on scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('.requirement-card, .pricing-table, .info-item');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementBottom = element.getBoundingClientRect().bottom;
            
            if (elementTop < window.innerHeight && elementBottom > 0) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    }

    // Initialize elements for animation
    function initializeAnimations() {
        const elements = document.querySelectorAll('.requirement-card, .pricing-table, .info-item');
        
        elements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(50px)';
            element.style.transition = 'all 0.5s ease-out';
        });
    }

    // Smooth scroll for navigation links
    document.querySelectorAll('nav a').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Add hover effect to pricing tables
    document.querySelectorAll('.pricing-table').forEach(table => {
        table.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 5px 25px rgba(0,0,0,0.2)';
        });
        
        table.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
        });
    });

    // Add parallax effect to page header
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.page-header');
        if (header) {
            const scroll = window.pageYOffset;
            header.style.backgroundPositionY = scroll * 0.5 + 'px';
        }
    });

    // Animate numbers in pricing
    function animateNumbers() {
        document.querySelectorAll('.pricing-price').forEach(price => {
            const finalPrice = parseInt(price.textContent);
            let currentPrice = 0;
            const duration = 1000; // 1 second
            const steps = 20;
            const increment = finalPrice / steps;
            const stepDuration = duration / steps;
            
            const animation = setInterval(() => {
                currentPrice += increment;
                if (currentPrice >= finalPrice) {
                    price.textContent = finalPrice + ' PLN';
                    clearInterval(animation);
                } else {
                    price.textContent = Math.round(currentPrice) + ' PLN';
                }
            }, stepDuration);
        });
    }

    // Initialize animations when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeAnimations();
        animateNumbers();
        animateOnScroll();
    });

    // Add scroll event listener for animations
    window.addEventListener('scroll', animateOnScroll);

    // Add sticky header effect
    const header = document.querySelector('header');
    const headerHeight = header.offsetHeight;
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= headerHeight) {
            header.classList.remove('scroll-up', 'scroll-down');
            return;
        }
        
        if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
            header.classList.remove('scroll-up');
            header.classList.add('scroll-down');
        } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
            header.classList.remove('scroll-down');
            header.classList.add('scroll-up');
        }
        
        lastScroll = currentScroll;
    });

    // Add ripple effect to buttons
    function createRipple(event) {
        const button = event.currentTarget;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        
        const diameter = Math.max(rect.width, rect.height);
        const radius = diameter / 2;
        
        ripple.style.width = ripple.style.height = `${diameter}px`;
        ripple.style.left = `${event.clientX - rect.left - radius}px`;
        ripple.style.top = `${event.clientY - rect.top - radius}px`;
        ripple.classList.add('ripple');
        
        const rippleContainer = document.createElement('span');
        rippleContainer.classList.add('ripple-container');
        
        rippleContainer.appendChild(ripple);
        button.appendChild(rippleContainer);
        
        setTimeout(() => {
            rippleContainer.remove();
        }, 1000);
    }

    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', createRipple);
    });

    // Add hover effect to cards
    const cards = document.querySelectorAll('.feature-card, .price-card, .service-item, .news-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 5px 25px rgba(0,0,0,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
        });
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn, .hero-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Animate numbers in stats section
    const stats = document.querySelectorAll('.stat-item h3');
    stats.forEach(stat => {
        const finalNumber = parseInt(stat.textContent);
        let currentNumber = 0;
        const increment = finalNumber / 50;
        const duration = 2000;
        const interval = duration / 50;

        const counter = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                stat.textContent = finalNumber + (stat.textContent.includes('%') ? '%' : '+');
                clearInterval(counter);
            } else {
                stat.textContent = Math.floor(currentNumber) + (stat.textContent.includes('%') ? '%' : '+');
            }
        }, interval);
    });

    // Parallax effect for hero section
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', () => {
            const scroll = window.pageYOffset;
            heroSection.style.backgroundPositionY = `${scroll * 0.5}px`;
        });
    }

    // Add hover effect to navigation links
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add hover effect to social icons
    const socialIcons = document.querySelectorAll('.social-icon');
    socialIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.1)';
            this.style.color = getComputedStyle(document.documentElement)
                .getPropertyValue('--accent-color').trim();
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.color = 'white';
        });
    });

    // Add shine effect to cards on hover
    const addShineEffect = (element) => {
        element.addEventListener('mousemove', (e) => {
            const { left, top, width, height } = element.getBoundingClientRect();
            const x = (e.clientX - left) / width;
            const y = (e.clientY - top) / height;
            
            element.style.setProperty('--mouse-x', x.toString());
            element.style.setProperty('--mouse-y', y.toString());
        });
    };

    document.querySelectorAll('.feature-card, .price-card, .service-item').forEach(addShineEffect);

    // Add floating animation to icons
    const icons = document.querySelectorAll('.feature-icon i, .service-icon i');
    icons.forEach(icon => {
        icon.style.animation = 'float 3s ease-in-out infinite';
    });

    // Add pulse animation to CTA buttons
    const ctaButtons = document.querySelectorAll('.hero-btn.primary, .cta-section .btn');
    ctaButtons.forEach(button => {
        button.style.animation = 'pulse 2s infinite';
    });
});
