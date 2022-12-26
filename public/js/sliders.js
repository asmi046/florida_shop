
var hitSlider = new Swiper(".hit_slider", {

    slidesPerView: 1,
    spaceBetween: 30,

    loop: true,
    loopFillGroupWithBlank: true,

    navigation: {
      nextEl: "#hit_btn_right",
      prevEl: "#hit_btn_left",

    },

    breakpoints: {
        1920:{
            slidesPerView: 4,
        },

        1440:{
            slidesPerView: 4,
        },

        768: {
          slidesPerView: 2,
        }
    }

  });

var salsesSlider = new Swiper(".sales_slider", {

    slidesPerView: 1,
    spaceBetween: 30,

    loop: true,
    loopFillGroupWithBlank: true,

    navigation: {
      nextEl: "#sale_btn_right",
      prevEl: "#sale_btn_left",

    },

    breakpoints: {
        1920:{
            slidesPerView: 6,
        },

        1440:{
            slidesPerView: 6,
        },

        768: {
          slidesPerView: 4,
        }
    }

  });

  var brandSlider = new Swiper(".rew_slider", {
    slidesPerView: 3,
    spaceBetween: 30,
    pagination: {
      el: ".banner_controll",
      clickable: true,
    },
  });
