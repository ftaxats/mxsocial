(function () {
  ("use strict");

  const rootHtml = document.documentElement;
  const verticalMenuBtn = document.querySelector(".vertical-menu-btn");
  if (verticalMenuBtn != null) {
    const dashboardWrapper = document.querySelector(".dashboard-wrapper");
    const overlay = document.createElement("div");
    overlay.setAttribute("class", "overlay-bg");

    function checkDeviceWidth() {
      const deviceWidth = window.innerWidth;
      if (deviceWidth <= 992) {
        setSidebarAttribute("close");
        verticalMenuBtn.addEventListener("click", toggleSidebar);
        dashboardWrapper.appendChild(overlay);
      } else {
        setSidebarAttribute("open");
        verticalMenuBtn.addEventListener("click", toggleSidebar);
        overlay.remove();
      }
    }

    function setSidebarAttribute(value) {
      rootHtml.setAttribute("data-sidebar", value);
    }

    function toggleSidebar() {
      const currentAttribute = rootHtml.getAttribute("data-sidebar");
      const newAttribute = currentAttribute === "open" ? "close" : "open";
      setSidebarAttribute(newAttribute);
      if (newAttribute === "open") {
        overlay.addEventListener("click", function () {
          overlay.style.display = "none";
          setSidebarAttribute("close");
        });
      }

      overlay.style.display = newAttribute === "open" ? "block" : "none";
    }

    window.addEventListener("resize", checkDeviceWidth);
    checkDeviceWidth();
  }


  if (document.querySelectorAll(".sidebar-menu .collapse")) {
    var collapses = document.querySelectorAll(".sidebar-menu .collapse");
    Array.from(collapses).forEach(function (collapse) {

      var collapseInstance = new bootstrap.Collapse(collapse, {
        toggle: false,
      });

      collapse.addEventListener("show.bs.collapse", function (e) {
        e.stopPropagation();
        var closestCollapse = collapse.parentElement.closest(".collapse");
        if (closestCollapse) {
          var siblingCollapses = closestCollapse.querySelectorAll(".collapse");
          Array.from(siblingCollapses).forEach(function (siblingCollapse) {
            var siblingCollapseInstance =
              bootstrap.Collapse.getInstance(siblingCollapse);
            if (siblingCollapseInstance === collapseInstance) {
              return;
            }
            siblingCollapseInstance.hide();
          });
        } else {
          var getSiblings = function (elem) {

            var siblings = [];
            var sibling = elem.parentNode.firstChild;
    
            while (sibling) {
              if (sibling.nodeType === 1 && sibling !== elem) {
                siblings.push(sibling);
              }
              sibling = sibling.nextSibling;
            }
            return siblings;
          };
          var siblings = getSiblings(collapse.parentElement);
          Array.from(siblings).forEach(function (item) {
            if (item.childNodes.length > 2)
              item.firstElementChild.setAttribute("aria-expanded", "false");
            var ids = item.querySelectorAll("*[id]");
            Array.from(ids).forEach(function (item1) {
              item1.classList.remove("show");
              if (item1.childNodes.length > 2) {
                var val = item1.querySelectorAll("ul li a");
                Array.from(val).forEach(function (subitem) {
                  if (subitem.hasAttribute("aria-expanded"))
                    subitem.setAttribute("aria-expanded", "false");
                });
              }
            });
          });
        }
      });


      collapse.addEventListener("hide.bs.collapse", function (e) {
        e.stopPropagation();
        var childCollapses = collapse.querySelectorAll(".collapse");
        Array.from(childCollapses).forEach(function (childCollapse) {
          childCollapseInstance = bootstrap.Collapse.getInstance(childCollapse);
          childCollapseInstance.hide();
        });
      });
    });
  }

  let fullscreenBtn = document.querySelector(".fullscreen-btn");
  if (fullscreenBtn != null) {
    fullscreenBtn.innerHTML = `<i class="las la-expand"></i>`;
    fullscreenBtn.addEventListener("click", () => {
      if (fullscreenBtn.innerHTML == `<i class="las la-expand"></i>`) {
        if (rootHtml.requestFullscreen) {
          rootHtml.requestFullscreen();
        } else if (rootHtml.msRequestFullscreen) {
          rootHtml.msRequestFullscreen();
        } else if (rootHtml.mozRequestFullScreen) {
          rootHtml.mozRequestFullScreen();
        } else if (rootHtml.webkitRequestFullscreen) {
          rootHtml.webkitRequestFullscreen();
        }
        fullscreenBtn.innerHTML = `<i class="las la-compress"></i>`;
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.msexitFullscreen) {
          document.msexitFullscreen();
        } else if (document.mozexitFullscreen) {
          document.mozexitFullscreen();
        } else if (document.webkitexitFullscreen) {
          document.webkitexitFullscreen();
        }
        fullscreenBtn.innerHTML = `<i class="las la-expand"></i>`;
      }
    });
  }

  Array.from(document.querySelectorAll('[data-anim="ripple"]'), (el) => {
    el.addEventListener("click", (e) => {
      e = e.touches ? e.touches[0] : e;
      const r = el.getBoundingClientRect(),
        d = Math.sqrt(Math.pow(r.width, 2) + Math.pow(r.height, 2)) * 2;
      el.style.cssText = `--s: 0; --o: 1;`;
      el.offsetTop;
      el.style.cssText = `--t: 1; --o: 0; --d: ${d}; --x:${
        e.clientX - r.left
      }; --y:${e.clientY - r.top};`;
    });
  });

  const appSearchBtn = document.querySelector(".app-search-btn");
  if (appSearchBtn) {
    const appSearch = document.querySelector(".topbar-search");
    appSearchBtn.addEventListener("click", () => {
      appSearch.style.cssText = "transform:translateY(0);transition:0.3s";
      const overlay = document.createElement("div");
      overlay.setAttribute("class", "overlay");
      appSearch.appendChild(overlay);
      if (overlay) {
        overlay.addEventListener("click", () => {
          appSearch.style.cssText =
            "transform:translateY(-130%);transition:0.3s";
          overlay.remove();
        });
      }
    });
  }


  $(document).on("click", ".close", function (e) {
    $(this).closest(".modal").modal("hide");
  });


  const filterBtn = document.querySelector(".filter-btn");
  const filterDropdown = document.querySelector(".filter-dropdown");

  const dropdownOverlay = document.createElement("div");
  dropdownOverlay.classList.add("dropdownOverlay");

  if (filterBtn) {
    filterBtn.addEventListener("click", () => {
      if (filterDropdown.classList.contains("show")) {
        filterBtn.style.cssText = "";
        filterDropdown.style.cssText = "";
        filterDropdown.classList.remove("show");
        const isDropdownOverlay = document.querySelector(".dropdownOverlay");
        if (isDropdownOverlay) {
          isDropdownOverlay.remove();
        }
      } else {
        filterBtn.style.cssText = "position:relative; z-index:210";
        filterDropdown.style.cssText = "z-index:210;";
        filterDropdown.classList.add("show");
        const isDropdownOverlay = document.querySelector(".dropdownOverlay");
        if (!isDropdownOverlay) {
          document.body.appendChild(dropdownOverlay);
        }
      }
    });
  }

  document.body.addEventListener("click", function (e) {
    if (e.target.classList.contains("dropdownOverlay")) {
      e.target.remove();
      filterBtn.style.cssText = "";
      filterDropdown.style.cssText = "";
      filterDropdown.classList.remove("show");
    }
  });

var layoutRightSideBtn = document.querySelector(".layout-rightsidebar-btn");
layoutRightSideBtn &&
    (Array.from(document.querySelectorAll(".layout-rightsidebar-btn")).forEach(
        function (e) {
            var o = document.querySelector(".layout-rightside-col");
            e.addEventListener("click", function () {
                o.classList.contains("d-block")
                    ? (o.classList.remove("d-block"), o.classList.add("d-none"))
                    : (o.classList.remove("d-none"), o.classList.add("d-block"));
            });
        }
    ),
    
    window.addEventListener("resize", function () {
        var e = document.querySelector(".layout-rightside-col");
        e &&
            Array.from(document.querySelectorAll(".layout-rightsidebar-btn")).forEach(
                function () {
                    window.outerWidth < 1699 || 3440 < window.outerWidth
                        ? e.classList.remove("d-block")
                        : 1699 < window.outerWidth && e.classList.add("d-block");
                }
            );
    }),
    
    (overlay = document.querySelector(".overlay"))) &&
    document.querySelector(".overlay").addEventListener("click", function () {
        document.querySelector(".layout-rightside-col").classList.contains("d-block") &&
            document.querySelector(".layout-rightside-col").classList.remove("d-block");
    }),

    window.addEventListener("load", function () {
        var e = document.querySelector(".layout-rightside-col");
        e &&
            Array.from(document.querySelectorAll(".layout-rightsidebar-btn")).forEach(
                function () {
                    window.outerWidth < 1699 || 3440 < window.outerWidth
                        ? e.classList.remove("d-block")
                        : 1699 < window.outerWidth && e.classList.add("d-block");
                }
            );
    });

})();



