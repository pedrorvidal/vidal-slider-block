document.addEventListener("DOMContentLoaded", function () {
	const sliders = document.querySelectorAll(".vidal-slider");

	sliders.forEach(initSlider);
});

function initSlider(sliderEl) {
	const track = sliderEl.querySelector(".vidal-slider__track");
	const slides = sliderEl.querySelectorAll(".vidal-slider__slide");
	const dots = sliderEl.querySelectorAll(".vidal-slider__dot");

	const totalSlides = slides.length;

	if (totalSlides <= 1) {
		return;
	}

	const autoplay = sliderEl.dataset.autoplay === "true";
	const interval = parseInt(sliderEl.dataset.interval, 10) || 3000;

	let currentIndex = 0;
	let autoplayTimer = null;

	function goToSlide(index) {
		currentIndex = index;

		track.style.transform = "translateX(-" + index * 100 + "%)";

		dots.forEach(function (dot, dotIndex) {
			dot.classList.toggle("is-active", dotIndex === index);
		});
	}

	function goToNextSlide() {
		const nextIndex = (currentIndex + 1) % totalSlides;
		goToSlide(nextIndex);
	}

	function startAutoplay() {
		if (!autoplay) {
			return;
		}
		stopAutoplay();
		autoplayTimer = setInterval(goToNextSlide, interval);
	}

	function stopAutoplay() {
		if (autoplayTimer) {
			clearInterval(autoplayTimer);
			autoplayTimer = null;
		}
	}

	dots.forEach(function (dot) {
		dot.addEventListener("click", function () {
			const index = parseInt(dot.dataset.slideIndex, 10);
			goToSlide(index);
			startAutoplay();
		});
	});

	sliderEl.addEventListener("mouseenter", stopAutoplay);
	sliderEl.addEventListener("mouseleave", startAutoplay);

	startAutoplay();
}
