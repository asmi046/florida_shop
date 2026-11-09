
document.addEventListener("DOMContentLoaded", () => {
    let filter_mobile_panel = document.querySelector(".filter_mobile_panel ")

    if (filter_mobile_panel)
        filter_mobile_panel.addEventListener("click", function (e) {
            this.classList.toggle("open")
            let tf = document.querySelector(".tovar_filter")
            if (tf) tf.classList.toggle("open")
        })

    let acc_blk = document.querySelectorAll(".acc_head ")
    for (let elem of acc_blk ) {
        elem.addEventListener("click", function (e) {
            elem.classList.toggle("open")
            elem.nextSibling.classList.toggle("open")
        })
    }

    // Автоматическое применение фильтра без перезагрузки страницы
    let filter_form = document.querySelector(".tovar_filter")
    if (!filter_form) return

    // Чекбоксы и селекты — применяем сразу при изменении
    filter_form.addEventListener("change", function (e) {
        if (e.target.matches('input[type="checkbox"], select')) {
            applyFilter(filter_form)
        }
    })

    // Ценовые поля — применяем по Enter или при потере фокуса (с задержкой)
    let price_inputs = filter_form.querySelectorAll('input[name="minprice"], input[name="maxprice"]')
    for (let input of price_inputs) {
        let debounce_timer = null
        input.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault()
                applyFilter(filter_form)
            }
        })
        input.addEventListener("input", function () {
            clearTimeout(debounce_timer)
            debounce_timer = setTimeout(() => applyFilter(filter_form), 800)
        })
    }

    // Перехват кликов по пагинации
    document.addEventListener("click", function (e) {
        let pag_link = e.target.closest("#product-pagination a")
        if (pag_link) {
            e.preventDefault()
            applyFilter(filter_form, pag_link.href)
        }
    })

    // Кнопка «Сбросить фильтр» — снимаем галочки, очищаем цену и отправляем
    let reset_btn = filter_form.querySelector('a.button[href]')
    if (reset_btn) {
        reset_btn.addEventListener("click", function (e) {
            e.preventDefault()
            for (let cb of filter_form.querySelectorAll('input[type="checkbox"]')) cb.checked = false
            for (let input of filter_form.querySelectorAll('input[name="minprice"], input[name="maxprice"]')) input.value = ""
            applyFilter(filter_form, filter_form.action)
        })
    }

    // Кнопки «назад/вперёд» браузера
    window.addEventListener("popstate", function () {
        location.reload()
    })
})

async function applyFilter(form, url = null) {
    let list = document.getElementById("product-list")
    if (!list) {
        form.submit()
        return
    }

    if (!url) {
        let params = new URLSearchParams(new FormData(form))
        params.delete("page")
        let qs = params.toString()
        url = form.action + (qs ? "?" + qs : "")
    }

    list.style.opacity = "0.5"

    try {
        let res = await fetch(url, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })

        // Сервер вернул 404 (например, уход за пределы страниц) — пробуем без page
        if (!res.ok && url.includes("page=")) {
            let clean_url = url.replace(/([?&])page=\d+&?/, "$1").replace(/[?&]$/, "")
            res = await fetch(clean_url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
            url = clean_url
        }

        if (!res.ok) {
            location.href = url
            return
        }

        let data = await res.json()
        let tmp = document.createElement("div")
        tmp.innerHTML = data.html

        let new_list = tmp.querySelector("#product-list")
        let new_pagination = tmp.querySelector("#product-pagination")

        if (new_list) list.replaceWith(new_list)
        if (new_pagination) {
            let old_pagination = document.getElementById("product-pagination")
            if (old_pagination) old_pagination.replaceWith(new_pagination)
        }

        history.pushState(null, "", url)
        new_list.scrollIntoView({ behavior: "smooth", block: "start" })
    } catch (err) {
        location.href = url
    }
}
