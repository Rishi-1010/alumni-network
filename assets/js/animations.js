import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

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
