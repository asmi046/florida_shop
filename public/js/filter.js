
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
});
