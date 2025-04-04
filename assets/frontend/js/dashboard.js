(function () {
  "use strict";
  var rootHtml = document.documentElement;
  var deviceWidth =
    window.innerWidth ||
    document.documentElement.clientWidth ||
    document.body.clientWidth;

  const preloader = document.querySelector(".preloader");
  window.addEventListener("load", () => {
    if (preloader) {
      preloader.remove();
    }
  });


  
  const sidebarTrigger = document.querySelector(".sidebar-trigger");
  if (sidebarTrigger != null) {
    var sidebaroverlay = document.querySelector(".sidebaroverlay");
    const sidebars = document.querySelector(".aside");


    sidebaroverlay = document.createElement("div");
    sidebaroverlay.setAttribute("class", "sidebaroverlay");

    sidebarTrigger.addEventListener("click", () => {

      sidebars.classList.toggle("show-side-bar");
      sidebarTrigger.classList.toggle("clicked");

      if (sidebars.classList.contains("show-side-bar")) {
        document.body.appendChild(sidebaroverlay);
      } else {
        sidebaroverlay.remove();
      }
    });

    sidebaroverlay.addEventListener("click", () => {
      sidebaroverlay.remove();
      if (
        sidebars.classList.contains("show-side-bar") &&
        sidebarTrigger.classList.contains("clicked")
      ) {
        sidebars.classList.remove("show-side-bar");
        sidebarTrigger.classList.remove("clicked");
      }
    });
  }

  const sideMenuItem = document.querySelectorAll(".sidemenu-item");
  if (sideMenuItem !== null) {
    const main = document.querySelector(".main");
    sideMenuItem.forEach((item) => {
      const sideMenuLink = item.querySelector(".sidemenu-link");
      const sideBarDropdown = item.querySelector(".side-menu-dropdown");

      if (sideBarDropdown !== null) {
        sideMenuLink.addEventListener("click", () => {
          if (sideBarDropdown !== null) {
            sideMenuItem.forEach((otherItem) => {
              const otherSideBarDropdown = otherItem.querySelector(
                ".side-menu-dropdown"
              );

              const otherSideMenuLink =
                otherItem.querySelector(".sidemenu-link");
              if (otherSideBarDropdown !== null && otherSideMenuLink !== null) {
                const rotateIcon = otherSideMenuLink.querySelector(
                  ".sidemenu-link small"
                );
                if (otherSideBarDropdown !== sideBarDropdown) {
                  rotateIcon.style.transform = "";
                  otherSideBarDropdown.classList.remove("show-sideMenu");
                }
              }
            });

            sideBarDropdown.classList.toggle("show-sideMenu");

            const rotateIcon = sideMenuLink.querySelector(
              ".sidemenu-link small"
            );

            if (
              !rotateIcon.style.transform ||
              rotateIcon.style.transform !== "rotate(-180deg)"
            ) {
              rotateIcon.style.transform = "rotate(-180deg)";
            } else {
              rotateIcon.style.transform = "";
            }

          }
        });
      }
    });
  }



  const rightBtn = document.getElementById('right-sidebar-btn');
  const rightSidebar = document.querySelector('.right-side-col');
  const overlay = document.querySelector('.overlay');

  if(rightBtn){
    rightBtn.addEventListener('click', () => {
      rightSidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    });
  
  }
  if(overlay){
    overlay.addEventListener('click', ()=>{
      overlay.classList.toggle('show');
      rightSidebar.classList.toggle('show');
    })
  }

  
  const dropdownButton = document.querySelector('.dropdown.currency .dropdown-toggle');
  const dropdownMenu = document.querySelector('.dropdown.currency .dropdown-menu');

  if (!dropdownMenu && dropdownButton) {
     dropdownButton.classList.add('single-item');
  }


  document.addEventListener("DOMContentLoaded", () => {
    const templatesWrapper = document.querySelector(".template-sidebar");

    if (templatesWrapper) {
      const category = templatesWrapper.querySelector(".parent-categories");
      const subCategory = templatesWrapper.querySelector(".sub-categories");
      const back = templatesWrapper.querySelector(".back-to-prompt");

      if (category) {
        const categoryItem = document.querySelectorAll(".category-item");

        categoryItem.forEach((cate) => {
          cate.addEventListener("click", () => {
            toggleVisibility(category, false);
            toggleVisibility(subCategory, true);
          });
        });

        if (back) {
          back.addEventListener("click", () => {
            toggleVisibility(subCategory, false);
            toggleVisibility(category, true);
          });
        }
      }
    }

    /**
     * Utility function to toggle element visibility.
     * @param {Element} element 
     * @param {boolean} show 
     */
    function toggleVisibility(element, show) {
      if (show) {
        element.classList.remove("d-none");
      } else {
        element.classList.add("d-none");
      }
    }
  });



})();
