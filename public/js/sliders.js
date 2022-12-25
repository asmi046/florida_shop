
var brandSlider = new Swiper(".hit_slider", {

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
