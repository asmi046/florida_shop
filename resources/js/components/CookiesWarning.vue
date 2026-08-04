<template>
    <Transition name="fade">
        <div v-if="showBanner" class="cookie-banner">
            <div class="cookie-content">
                <h3>На сайте используются файлы cookie</h3>
                <p>
                    Оставаясь на сайте, вы выражаете свое
                    <a :href="privacyPolicyAcceptLink" target="_blank"
                        >согласие</a
                    >
                    на обработку персональных данных в соответствии c
                    <a :href="privacyPolicyLink" target="_blank"
                        >плитика конфиденциальности</a
                    >
                </p>
                <p>
                    Подробнее о файлах
                    <a :href="cookiesInfoLink" target="_blank">cookies</a>
                </p>
                <button
                    @click="acceptCookies"
                    class="button button_green accept-button"
                >
                    Принять
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    privacyPolicyLink: {
        type: String,
        default: "/privacy-policy",
    },
    privacyPolicyAcceptLink: {
        type: String,
        default: "/privacy-accept",
    },
    cookiesInfoLink: {
        type: String,
        default: "/cookies-info",
    },
});

const showBanner = ref(false);

const checkCookieConsent = () => {
    return document.cookie
        .split(";")
        .some((item) => item.trim().startsWith("cookieConsent="));
};

const acceptCookies = () => {
    const longTermExpiry = new Date();
    longTermExpiry.setFullYear(longTermExpiry.getFullYear() + 10);
    document.cookie = `cookieConsent=true; expires=${longTermExpiry.toUTCString()}; path=/`;
    showBanner.value = false;
};

onMounted(() => {
    if (!checkCookieConsent()) {
        setTimeout(() => {
            showBanner.value = true;
        }, 2000);
    }
});
</script>
