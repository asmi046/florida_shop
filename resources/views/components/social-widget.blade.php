<div class="social-widget">
    <div title="Свяжитесь с нами" class="social-widget__main" id="socialWidgetToggle">
        <svg class="sprite_icon">
            <use xlink:href="#ws_icon"></use>
        </svg>
    </div>

    <div class="social-widget__links" id="socialWidgetLinks">
        <a title="Свяжитесь с нами в ВКонтакте" href="#" class="social-widget__link social-widget__link--vk">
            <svg class="sprite_icon vk_icon">
                <use xlink:href="#vk_icon"></use>
            </svg>
        </a>

        <a title="Свяжитесь с нами в Telegram" href="#" class="social-widget__link social-widget__link--telegram">
            <svg class="sprite_icon">
                <use xlink:href="#tg_icon"></use>
            </svg>
        </a>

        <a title="Свяжитесь с нами в WhatsApp" href="#" class="social-widget__link social-widget__link--whatsapp">
            <svg class="sprite_icon">
                <use xlink:href="#ws_icon"></use>
            </svg>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('socialWidgetToggle');
        const links = document.getElementById('socialWidgetLinks');
        const widget = document.querySelector('.social-widget');

        toggle.addEventListener('click', function() {
            widget.classList.toggle('social-widget--active');
        });

        // Закрытие при клике вне виджета
        document.addEventListener('click', function(event) {
            if (!widget.contains(event.target)) {
                widget.classList.remove('social-widget--active');
            }
        });
    });
</script>
