document.addEventListener("DOMContentLoaded", () => {

    let more_btn = document.querySelector("#head_category ul a.more")

    if (more_btn)
        more_btn.addEventListener("click", function (e) {
            e.preventDefault()
            more_btn.classList.toggle('active')
            let oc_elem = document.querySelector(".ower_cat")
            if (oc_elem)
                oc_elem.classList.toggle('showed')

        })
})
