import { register } from 'swiper/element/bundle';

register()

document.addEventListener("DOMContentLoaded", (event) => {
    const main_cat_slider = document.getElementById('main_cat_slider')

    const main_cat_slider_param = {
        slidesPerView: 1.3,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2.5,
            },

            1024: {
                slidesPerView: 3.5,
            },

            1920: {
                slidesPerView: 4,
            },
        }
    }

    const main_cat_slider_btn_prev = document.getElementById('main_cat_slider_btn_prev')
    main_cat_slider_btn_prev.onclick = () => {
        main_cat_slider.swiper.slidePrev()
    }

    const main_cat_slider_btn_next = document.getElementById('main_cat_slider_btn_next')
    main_cat_slider_btn_next.onclick = () => {
        main_cat_slider.swiper.slideNext()
    }


    Object.assign(main_cat_slider, main_cat_slider_param)
    main_cat_slider.initialize()
})
