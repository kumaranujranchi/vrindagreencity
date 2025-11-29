// Update header placeholder height to avoid content jumping when header becomes fixed
function updateHeaderPlaceholder() {
  var $stickyHeader = $("#sticky-header");
  var $placeholder = $("#header-top-fixed");
  if (!$stickyHeader.length || !$placeholder.length) {
    return;
  }
  if ($stickyHeader.hasClass("sticky-menu")) {
    // set placeholder height to header height
    $placeholder.css("height", $stickyHeader.outerHeight() + "px");
    $placeholder.css("display", "block");
  } else {
    $placeholder.css("height", 0);
    $placeholder.css("display", "none");
  }
}


// Ensure header placeholder is correct on load and resize
// Also handle sticky menu logic here (outside IIFE for reliability)
$(window).on("load resize scroll", function () {
  var scroll = $(window).scrollTop();
  var stickyThreshold = 10;
  var $stickyHeader = $("#sticky-header");
  
  if ($stickyHeader.length) {
    if (scroll < stickyThreshold) {
      $stickyHeader.removeClass("sticky-menu");
      $stickyHeader.css({ position: '', top: '', left: '', width: '', zIndex: '' });
      $("#header-top-fixed").removeClass("header-fixed-position");
    } else {
      $stickyHeader.addClass("sticky-menu");
      // Apply inline styles for maximum compatibility (iOS fix)
      $stickyHeader.css({ 
        position: 'fixed', 
        top: '0', 
        left: '0', 
        width: '100%', 
        zIndex: 2147483647 
      });
      $("#header-top-fixed").addClass("header-fixed-position");
    }
  }
  
  updateHeaderPlaceholder();
});
// Trigger initial scroll handler to set initial sticky state
$(function () {
  $(window).trigger("scroll");
});
(function ($) {
  "use strict";

  // Mobile smooth scrolling optimization
  if (window.innerWidth <= 767) {
    // Add CSS for smooth scrolling
    document.documentElement.style.scrollBehavior = "smooth";

    // Optimize touch scrolling
    var supportsPassive = false;
    try {
      var opts = Object.defineProperty({}, "passive", {
        get: function () {
          supportsPassive = true;
        },
      });
      window.addEventListener("testPassive", null, opts);
      window.removeEventListener("testPassive", null, opts);
    } catch (e) {}

    // Add passive event listeners for better scroll performance
    document.addEventListener(
      "touchstart",
      function () {},
      supportsPassive ? { passive: true } : false
    );
    document.addEventListener(
      "touchmove",
      function () {},
      supportsPassive ? { passive: true } : false
    );
  }

  /*=============================================
	=    		 Preloader			      =
=============================================*/
  function preloader() {
    $("#preloader").delay(0).fadeOut();
  }

  /*=============================================
	=          Windows OnLoad               =
=============================================*/
  $(window).on("load", function () {
    preloader();
    mainSlider();
    wowAnimation();
    // NOTE: ensureHeaderInBody removed — we rely on CSS position fixed and inline fallback instead.
  });

  /*=============================================
	=          One page Menu               =
=============================================*/
  // Target links in the main navigation or any element that may have the `section-link` class
  var scrollLink = $('.section-link, .navigation a[href*="#"]');
  // Active link switching
  $(window).scroll(function () {
    var scrollbarLocation = $(this).scrollTop();

    scrollLink.each(function () {
      var sectionOffset = $(this.hash).offset().top - 90;

      if (sectionOffset <= scrollbarLocation) {
        $(this).parent().addClass("active");
        $(this).parent().siblings().removeClass("active");
        // Update URL without page reload using History API
        var sectionId = $(this).attr("href").replace("#", "");
        if (sectionId && sectionId !== "home") {
          history.replaceState(null, null, "/" + sectionId);
        } else {
          history.replaceState(null, null, "/");
        }
      }
    });
  });
  //jQuery for page scrolling feature - requires jQuery Easing plugin
  $(function () {
    // Support both `section-link` anchors and navigation anchors that include a hash.
    $('.navigation a[href*="#"], a.section-link[href*="#"]:not([href="#"])').on(
      "click",
      function (e) {
        var target = $(this.hash);
        // If the target section exists on the current page, animate; otherwise let the link behave normally.
        if (target.length) {
          target = target.length
            ? target
            : $("[name=" + this.hash.slice(1) + "]");
          if (target.length) {
            $("html, body").animate(
              {
                scrollTop: target.offset().top - 80,
              },
              1200,
              "easeInOutExpo"
            );

            // Update URL using History API for clean URLs
            var sectionId = this.hash.replace("#", "");
            if (sectionId && sectionId !== "home") {
              history.pushState(null, null, "/" + sectionId);
            } else {
              history.pushState(null, null, "/");
            }
            e.preventDefault();
            return false;
          }
        }
      }
    );

    // Handle direct URL access (e.g., vrindagreencity.com/pricing)
    $(window).on("load", function () {
      var path = window.location.pathname.replace(/^\//, "").replace(/\/$/, "");
      if (path && path !== "index.html") {
        var target = $("#" + path);
        if (target.length) {
          setTimeout(function () {
            $("html, body").animate(
              {
                scrollTop: target.offset().top - 80,
              },
              1200,
              "easeInOutExpo"
            );
          }, 100);
        }
      }
    });
  });

  /*=============================================
	=    		Mobile Menu			      =
=============================================*/
  //SubMenu Dropdown Toggle
  if ($(".menu-area li.menu-item-has-children ul").length) {
    $(".menu-area .navigation li.menu-item-has-children").append(
      '<div class="dropdown-btn"><span class="fas fa-angle-down"></span></div>'
    );
  }

  //Mobile Nav Hide Show
  if ($(".mobile-menu").length) {
    var mobileMenuContent = $(".menu-area .main-menu").html();
    $(".mobile-menu .menu-box .menu-outer").append(mobileMenuContent);

    // If for any reason mobile menu content failed to populate (e.g., DOM load order), try alternate selectors
    if ($(".mobile-menu .menu-box .menu-outer").children().length === 0) {
      var fallbackMenuContent = $(".navbar-wrap .main-menu").html();
      if (fallbackMenuContent) {
        $(".mobile-menu .menu-box .menu-outer").append(fallbackMenuContent);
      }
    }

    //Dropdown Button
    $(".mobile-menu li.menu-item-has-children .dropdown-btn").on(
      "click",
      function () {
        $(this).toggleClass("open");
        $(this).prev("ul").slideToggle(500);
      }
    );
    //Menu Toggle Btn
    $(".mobile-nav-toggler").on("click", function () {
      $("body").addClass("mobile-menu-visible");
    });

    //Menu Toggle Btn
    $(
      ".menu-backdrop, .mobile-menu .close-btn, .mobile-menu .navigation li a"
    ).on("click", function () {
      $("body").removeClass("mobile-menu-visible");
    });
  }

  /*=============================================
	=          Data Background               =
=============================================*/
  $(".header-btn a").on("click", function () {
    $("html, body").animate(
      {
        scrollTop: $("#shop").offset().top,
      },
      1200,
      "easeInOutExpo"
    );
  });

  /*=============================================
	=          Data Background               =
=============================================*/
  $("[data-background]").each(function () {
    $(this).css(
      "background-image",
      "url(" + $(this).attr("data-background") + ")"
    );
  });

  /*=============================================
	=           Data Color             =
=============================================*/
  $("[data-bg-color]").each(function () {
    $(this).css("background-color", $(this).attr("data-bg-color"));
  });

  /*=============================================
	=            Header Search            =
=============================================*/
  $(".header-search > a").on("click", function () {
    $(".search-popup-wrap").slideToggle();
    $("body").addClass("search-visible");
    return false;
  });

  $(".search-backdrop").on("click", function () {
    $(".search-popup-wrap").slideUp(500);
    $("body").removeClass("search-visible");
  });

  /*=============================================
	=     Menu sticky & Scroll to top      =
=============================================*/
  // Note: Main sticky-menu logic is handled in the outer scroll handler at the top of this file
  // This handler just manages the scroll-to-top button visibility
  $(window).on("scroll", function () {
    var scroll = $(window).scrollTop();
    if (scroll >= 10) {
      $(".scroll-to-target").addClass("open");
      $("#header-fixed-height").addClass("active-height");
    } else {
      $(".scroll-to-target").removeClass("open");
      $("#header-fixed-height").removeClass("active-height");
    }
  });

  /*=============================================
	=    		 Scroll Up  	         =
=============================================*/
  if ($(".scroll-to-target").length) {
    $(".scroll-to-target").on("click", function () {
      var target = $(this).attr("data-target");
      // animate
      $("html, body").animate(
        {
          scrollTop: $(target).offset().top,
        },
        1000
      );
    });
  }

  /*=============================================
	=          OffCanvas Active            =
=============================================*/
  $(".navSidebar-button").on("click", function () {
    $("body").addClass("offcanvas-menu-visible");
    return false;
  });

  $(".offCanvas-overlay, .offCanvas-toggle").on("click", function () {
    $("body").removeClass("offcanvas-menu-visible");
  });

  /*=============================================
	=    		 Main Slider		      =
=============================================*/
  function mainSlider() {
    var BasicSlider = $(".slider-active");
    BasicSlider.on("init", function (e, slick) {
      var $firstAnimatingElements = $(".single-slider:first-child").find(
        "[data-animation]"
      );
      doAnimations($firstAnimatingElements);
    });
    BasicSlider.on(
      "beforeChange",
      function (e, slick, currentSlide, nextSlide) {
        var $animatingElements = $(
          '.single-slider[data-slick-index="' + nextSlide + '"]'
        ).find("[data-animation]");
        doAnimations($animatingElements);
      }
    );
    BasicSlider.slick({
      autoplay: false,
      autoplaySpeed: 10000,
      dots: false,
      fade: true,
      arrows: false,
      responsive: [
        { breakpoint: 767, settings: { dots: false, arrows: false } },
      ],
    });

    function doAnimations(elements) {
      var animationEndEvents =
        "webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend";
      elements.each(function () {
        var $this = $(this);
        var $animationDelay = $this.data("delay");
        var $animationType = "animated " + $this.data("animation");
        $this.css({
          "animation-delay": $animationDelay,
          "-webkit-animation-delay": $animationDelay,
        });
        $this.addClass($animationType).one(animationEndEvents, function () {
          $this.removeClass($animationType);
        });
      });
    }
  }

  /*=============================================
	=    		Accordion Active		      =
=============================================*/
  $(function () {
    $(".accordion-collapse").on("show.bs.collapse", function () {
      $(this).parent().addClass("active-item");
      $(this).parent().prev().addClass("prev-item");
    });

    $(".accordion-collapse").on("hide.bs.collapse", function () {
      $(this).parent().removeClass("active-item");
      $(this).parent().prev().removeClass("prev-item");
    });
  });

  /*=============================================
	=    		Shop Active		      =
=============================================*/
  $(".home-shop-active").slick({
    dots: true,
    infinite: true,
    speed: 1000,
    autoplay: true,
    arrows: true,
    slidesToShow: 4,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="flaticon-left-arrow"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="flaticon-right-arrow"></i></button>',
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1500,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
        },
      },
      {
        breakpoint: 575,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
    ],
  });

  /*=============================================
	=       Related Product Active      =
=============================================*/
  $(".related-product-active").slick({
    dots: true,
    infinite: true,
    speed: 1000,
    autoplay: true,
    arrows: true,
    slidesToShow: 4,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="flaticon-left-arrow"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="flaticon-right-arrow"></i></button>',
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1500,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
        },
      },
      {
        breakpoint: 575,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
    ],
  });

  /*=============================================
	=         Testimonial Active          =
=============================================*/
  $(".testimonial-active").slick({
    dots: true,
    infinite: true,
    speed: 1000,
    autoplay: true,
    arrows: true,
    slidesToShow: 1,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="flaticon-left-arrow"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="flaticon-right-arrow"></i></button>',
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
      {
        breakpoint: 575,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
    ],
  });

  /*=============================================
	=         Instagram Active          =
=============================================*/
  $(".instagram-active").slick({
    dots: false,
    infinite: true,
    speed: 1000,
    autoplay: true,
    arrows: false,
    swipe: false,
    slidesToShow: 5,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          arrows: false,
        },
      },
      {
        breakpoint: 575,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          arrows: false,
        },
      },
    ],
  });

  /*=============================================
	=            Blog Active               =
=============================================*/
  $(".blog-thumb-active").slick({
    dots: false,
    infinite: true,
    arrows: true,
    speed: 1500,
    slidesToShow: 1,
    slidesToScroll: 1,
    fade: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fas fa-arrow-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fas fa-arrow-right"></i></button>',
  });

  /*============================================
	=          Jarallax Active          =
=============================================*/
  // Ensure mobile menu/backdrop are direct children of <body> to avoid iOS stacking issues
  (function () {
    var moveMenuNodesToBody = function () {
      try {
        var mobileMenu = document.querySelector('.mobile-menu');
        var menuBackdrop = document.querySelector('.menu-backdrop');
        if (mobileMenu && mobileMenu.parentNode !== document.body) {
          document.body.appendChild(mobileMenu);
        }
        if (menuBackdrop && menuBackdrop.parentNode !== document.body) {
          document.body.appendChild(menuBackdrop);
        }
      } catch (e) {
        // Fail silently; this shouldn't stop the rest of the script
        console.warn('moveMenuNodesToBody failed', e);
      }
    };
    // Run early and also on DOM changes because some pages register the menu later
    document.addEventListener('DOMContentLoaded', moveMenuNodesToBody);
    // run immediately in case this script executes after DOM loaded
    moveMenuNodesToBody();
  })();
  $(".jarallax").jarallax({
    speed: 0.2,
  });

  /*=============================================
	=    	   Paroller Active  	         =
=============================================*/
  if ($("#paroller").length) {
    $(".paroller").paroller();
  }

  /*=============================================
	=    		Magnific Popup		      =
=============================================*/
  $(".popup-image").magnificPopup({
    type: "image",
    gallery: {
      enabled: true,
    },
  });

  /* magnificPopup video view */
  $(".popup-video").magnificPopup({
    type: "iframe",
  });

  /*=============================================
	=    	 Slider Range Active  	         =
=============================================*/
  $("#slider-range").slider({
    range: true,
    min: 20,
    max: 400,
    values: [120, 280],
    slide: function (event, ui) {
      $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
    },
  });
  $("#amount").val(
    "$" +
      $("#slider-range").slider("values", 0) +
      " - $" +
      $("#slider-range").slider("values", 1)
  );

  /*=============================================
	=          easyPieChart Active          =
=============================================*/
  function easyPieChart() {
    $(".fact-item").on("inview", function (event, isInView) {
      if (isInView) {
        $(".chart").easyPieChart({
          scaleLength: 0,
          lineWidth: 6,
          trackWidth: 6,
          size: 70,
          lineCap: "round",
          rotate: 360,
          trackColor: "#F4F4F4",
          barColor: "#FAA432",
        });
      }
    });
  }
  easyPieChart();

  /*=============================================
	=         Cart Active           =
=============================================*/
  $(".quickview-cart-plus-minus").append(
    '<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>'
  );
  $(".qtybutton").on("click", function () {
    var $button = $(this);
    var oldValue = $button.parent().find("input").val();
    if ($button.text() == "+") {
      var newVal = parseFloat(oldValue) + 1;
    } else {
      // Don't allow decrementing below zero
      if (oldValue > 0) {
        var newVal = parseFloat(oldValue) - 1;
      } else {
        newVal = 0;
      }
    }
    $button.parent().find("input").val(newVal);
  });

  /*=============================================
	=    		Isotope	Active  	      =
=============================================*/
  $(".grid").imagesLoaded(function () {
    // init Isotope
    var $grid = $(".grid").isotope({
      itemSelector: ".grid-item",
      percentPosition: true,
      masonry: {
        columnWidth: ".grid-item",
      },
    });
    // filter items on button click
    $(".portfolio-menu").on("click", "button", function () {
      var filterValue = $(this).attr("data-filter");
      $grid.isotope({ filter: filterValue });
    });
  });
  //for menu active class
  $(".product-license li").on("click", function (event) {
    $(this).siblings(".active").removeClass("active");
    $(this).addClass("active");
    event.preventDefault();
  });

  /*=============================================
	=    		 Wow Active  	         =
=============================================*/
  function wowAnimation() {
    var wow = new WOW({
      boxClass: "wow",
      animateClass: "animated",
      offset: 0,
      mobile: false,
      live: true,
    });
    wow.init();
  }
})(jQuery);
