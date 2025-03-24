import { gsap } from "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js";
import "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js";

gsap.registerPlugin(ScrollTrigger);

function hoverEffect(selector) {
    gsap.utils.toArray(selector).forEach(button => {
        let hoverTl = gsap.timeline({ paused: true });
        let label = button.querySelector('.btn-caption');
        let icon = button.querySelector('i');

        if (label) {
            hoverTl.to(label, { duration: 0.2, xPercent: 10, ease: "power2.out" }, 0);
        }
        if (icon) {
            hoverTl.to(icon, { duration: 0.2, xPercent: -10, ease: "power2.out" }, 0);
        }

        button.addEventListener("mouseenter", () => hoverTl.play());
        button.addEventListener("mouseleave", () => hoverTl.reverse());
    });
}

// Fade in and slide up animation for sections
function fadeInSections() {
    gsap.utils.toArray(".section").forEach(section => {
        gsap.from(section, {
            opacity: 0,
            yPercent: 20,
            duration: 1,
            ease: "power2.out",
            scrollTrigger: {
                trigger: section,
                start: "top center+=200", // Start animation when top of section hits center of viewport
                toggleActions: "play none none none", // Run only once on enter
            }
        });
    });
}

// Initialize animations
function initAnimations() {
    hoverEffect('.next-btn');
    hoverEffect('.submit-btn');
    hoverEffect('.nav-links .home-btn');
    fadeInSections(); 
}

initAnimations();
