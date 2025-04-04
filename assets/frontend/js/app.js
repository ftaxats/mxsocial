(function () {
  "use strict";

  AOS.init();

  const preloader = document.querySelector(".preloader");
  window.addEventListener("load", () => {
    if (preloader) {
      preloader.remove();
    }
  });

  const header = document.querySelector(".header");
  if (header) {
    const checkScroll = () => {
      if (window.scrollY > 0) {
        header.classList.add("sticky");
      } else {
        header.classList.remove("sticky");
      }
    };

    window.addEventListener("scroll", checkScroll);

    window.addEventListener("load", checkScroll);
  }

  const backToTop = document.querySelector(".back-to-top");
  if (backToTop != null) {
    backToTop.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  new VenoBox({
    selector: "#video-link",
    numeration: false,
    infinigall: true,
    ratio: "16x9",
    spinner: "wave",
    maxWidth: "100%",
    overlayColor: "rgba(0,0,0,0.5)",
    toolsColor: "#000",
  });


    const sponsor = document.querySelector('.sponsor-slider');
    if (sponsor) {
      new Swiper(sponsor, {
        slidesPerView: "auto",
        spaceBetween: 15,
        speed: 1000,
        autoplay: {
          delay: 3000,
        },
        breakpoints: {
          0: {
            slidesPerView: 2,
            spaceBetween: 15,
          },
          425: {
            slidesPerView: 3,
          },
          768: {
            slidesPerView: 4,
            spaceBetween: 20,
          },
          1200: {
            slidesPerView: 4,
            spaceBetween: 25,
          },
        },
      });
    }

  const reviewSlider = document.querySelector(".review-slider");
  if (reviewSlider) {
    new Swiper(reviewSlider, {
      slidesPerView: 1,
      spaceBetween: 15,
      loop: true,
      navigation: {
        nextEl: '.review-button-next',
        prevEl: '.review-button-prev',
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
    });
  }

  const blogSlider = document.querySelector(".blog-slider");
  if (blogSlider) {
    new Swiper(blogSlider, {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: true,
      navigation: {
        nextEl: ".blog-button-next",
        prevEl: ".blog-button-prev",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        577: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 2,
          spaceBetween: 25,
        },
        1200: {
          slidesPerView: 3,
          spaceBetween: 30,
        },
      },
    });
  }


  const authSlider = document.querySelectorAll(".auth-slider");
  if (authSlider) {
    authSlider.forEach((item) => {
      new Swiper(item, {
        slidesPerView: 1,
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
      });
    });
  }


  const cookie = document.querySelector(".cookie");
  if (cookie) {
    const cookieBtns = cookie.querySelectorAll(".cookie-action button");
    cookieBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        cookie.classList.add("d-none");
      });
    });
  }


  const sidebarTrigger = document.querySelectorAll(".sidebar-trigger");
  if (sidebarTrigger != null) {
    Array.from(sidebarTrigger).forEach((item) => {
      item.addEventListener("click", (ele) => {
        var attr = item.getAttribute("data-trigger");
        const sidebars = document.querySelectorAll(".sidebar");
        Array.from(sidebars).forEach((sidebar) => {
          const overlay = sidebar.querySelector(".sidebar-overlay");
          if (attr == sidebar.getAttribute("id")) {
            sidebar.classList.add("show-sidebar");
            if (sidebar.classList.contains("show-sidebar")) {
              document.body.classList.add("overflow-hidden");
              overlay.addEventListener("click", () => {
                sidebar.classList.remove("show-sidebar");
                document.body.classList.remove("overflow-hidden");
              });
            }
            if (sidebar.classList.contains("show-sidebar")) {
              var sideBarDissmis = sidebar.querySelector(".closer-sidebar");
              sideBarDissmis.addEventListener("click", () => {
                sidebar.classList.remove("show-sidebar");
                document.body.classList.remove("overflow-hidden");
              });
            }
          }
        });
      });
    });
  }

  const menuList = document.querySelector(".menu-list");
  if (menuList) {
    const menu = menuList.querySelectorAll(".menu-link");
    menu.forEach((menuItem) =>
      menuItem.addEventListener("click", () => {
        menuItem.classList.toggle("active");
      })
    );

    const menuFeatureItem = document.querySelectorAll(".menu-feature-item");
    menuFeatureItem.forEach((item) => {
      item.addEventListener("click", () => {
        menuFeatureItem.forEach((otherItem) => {
          if (otherItem !== item) {
            otherItem.classList.remove("hover");
          }
        });

        item.classList.toggle("hover");
      });

      const megaMenu = document.querySelector(".mega-menu");
      if (megaMenu) {
        megaMenu.addEventListener("mouseleave", () => {
          item.classList.remove("hover");
        });
      }
    });
  }






  const inputs = document.querySelectorAll(".otp-field >input");
  inputs.forEach((input, index) => {
    input.dataset.index = index;
    input.addEventListener("keyup", handleOtp);
    input.addEventListener("paste", handleOnPasteOtp);
  });

  function handleOtp(e) {
    const input = e.target;
    let value = input.value;
    let isValidInput = value.match(/[0-9a-z]/gi);
    input.value = "";
    input.value = isValidInput ? value[0] : "";

    let fieldIndex = input.dataset.index;
    if (fieldIndex < inputs.length - 1 && isValidInput) {
      input.nextElementSibling.focus();
    }

    if (e.key === "Backspace" && fieldIndex > 0) {
      input.previousElementSibling.focus();
    }

    if (fieldIndex == inputs.length - 1 && isValidInput) {
      submit();
    }
  }

  function handleOnPasteOtp(e) {
    const data = e.clipboardData.getData("text");
    const value = data.split("");
    if (value.length === inputs.length) {
      inputs.forEach((input, index) => (input.value = value[index]));
      submit();
    }
  }

  function submit() {
    let otp = "";
    inputs.forEach((input) => {
      otp += input.value;
      input.disabled = true;
      input.classList.add("disabled");
    });

    const otpField = document.querySelector("#otpCode");
    otpField.value = otp;

    var form = document.getElementById("otpForm");

    form.submit();
  }

  function hasClass(element, className) {
    return element.classList.contains(className);
  }


  function hasClass(element, className) {
    return element && element.classList.contains(className);
  }

  const sectionList = document.querySelectorAll("section");
  sectionList.forEach((sectionListItem) => {
    window.addEventListener("DOMContentLoaded", () => {
      const current = sectionListItem;
      const prevEl = current.previousElementSibling;
      const nextEl = current.nextElementSibling;

      const platform = current.classList.contains("platform");
      const integration = current.classList.contains("integration");

      const prevBg = hasClass(prevEl, "sectionWithBg");
      const currentBg = hasClass(current, "sectionWithBg");
      const nextBg = hasClass(nextEl, "sectionWithBg");

      if (prevBg && !currentBg) {
        current.classList.add("pt-110");
      }

      if (integration && !currentBg) {
        nextEl.classList.add("pt-110");
      }

      if (platform && !currentBg) {
        nextEl.classList.add("pt-110");
      }

      if (prevBg && platform) {
        current.classList.add("pt-110");
      }
      if (prevBg && currentBg) {
        current.classList.remove("pt-110");
      }
    });
  });


  var swiper = new Swiper(".team-slider", {
    slidesPerView: 1,
    loop: true, 
    spaceBetween: 10,
    breakpoints: {
      320: {
        slidesPerView: 2,
      },
      480: {
        slidesPerView: 2,
      },
      640: {
        slidesPerView: 3, 
      },
      1024: {
        slidesPerView: 4, 
      },
      1400: {
        slidesPerView: 5, 
      },

    }
  });


  const service = document.querySelector(".service-slider-one");
  if (service) {
    new Swiper(service, {
      slidesPerView: 1,
      spaceBetween: 20,
      speed: 500,
      loop: true,
      mousewheel: {
        invert: false,
      },
      grabCursor: true,
      loop: true,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      on: {
        reachEnd: function () {
            scrollToNextSlide();
        },
      },

      breakpoints: {
        0: {
          spaceBetween: 20,
          slidesPerView: 1,
        },
        768: {
          spaceBetween: 20,
          slidesPerView: 2,
        },
        1200: {
          spaceBetween: 25,
          slidesPerView: 3,
        },
      },
    });
  }

  function scrollToNextSlide() {
    const nextSection = document.querySelector('#service-slider-two-sec');

    if (nextSection) {
        nextSection.scrollIntoView({ behavior: 'smooth' });
    }
}

  const service2 = document.querySelector(".service-slider-two");
  if (service2) {
    new Swiper(service2, {
      slidesPerView: 1,
      spaceBetween: 20,
      speed: 500,
      loop: true,
      grabCursor: true,
      mousewheel: {
        invert: false,
      },
      loop: false,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },

      on: {
        reachEnd: function () {
          
        },
      },

      breakpoints: {
        0: {
          spaceBetween: 20,
          slidesPerView: 1,
        },
        768: {
          spaceBetween: 20,
          slidesPerView: 2,
        },
        1200: {
          spaceBetween: 25,
          slidesPerView: 3,
        },
      },
    });
  }


  $('.mega-menu-tab').hover(function(){
      $(this).tab('show');
  });
  $('.mega-menu-tab').click(function(e){
      e.preventDefault();
      var link = $(this).attr('href');
      window.location.href = link;
  });

  const dropdownButton = document.querySelector('.currency -toggle');
  const dropdownMenu = document.querySelector('.currency .dropdown-menu');

  if (!dropdownMenu && dropdownButton) {
    dropdownButton.classList.add('single-item');
  }


  document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.glider-nav .nav-link');
    const glider = document.querySelector('.glider-nav .glider');
    const tabWidth = 100;

    if (tabs && glider) {
      glider.style.transition = '0.45s ease';
  
      tabs.forEach((tab, index) => {
          tab.addEventListener('click', () => {
              tabs.forEach(t => t.classList.remove('active'));
              tab.classList.add('active');
              const gliderPosition = index * tabWidth;
              glider.style.transform = `translateX(${gliderPosition}px)`;
          });
      });
    }
  });


})(); 






