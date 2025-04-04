(function () {
  ("use strict");

  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  const postSlider = document.querySelector(".post-slider");
  if (postSlider) {
    new Swiper(postSlider, {
      slidesPerView: 2,
      spaceBetween: 25,
      loop: true,
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
      },
      navigation: {
        nextEl: ".post-next",
        prevEl: ".post-prev",
      },

      breakpoints: {
        0: {
          slidesPerView: 1,
        },

        768: {
          slidesPerView: 2,
        },
        1200: {
          slidesPerView: 2,
        },
      },
    });
  }
})();
