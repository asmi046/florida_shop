import axios from 'axios'

// params: {
//     _token:document.querySelector('meta[name="_token"]').content,
// }

let count_in_bascet = 0

function update_counter(new_count) {

    let bascet_counter = document.querySelectorAll(".bascet_counter")
    for (let elem of bascet_counter) {
        count_in_bascet = new_count
        elem.innerHTML = new_count
    }

}

function bascet_to_page() {
            axios.get('/bascet/get', {

            })
            .then((response) => {
                if (response.data.length == 0) return;

                    update_counter(response.data.count)

                    for (let element of response.data.position) {
                        let card = document.querySelector('.main-prod-card[data-prodid="'+element.product_sku+'"]')
                        if (card != undefined)
                        {
                            card.classList.add("in-bascet")
                            card.querySelector(".bascet_count span").innerHTML = element.quentity
                            card.querySelector(".card_to_bascet_btn").style.display = "none"
                            card.querySelector(".card_bascet_btn").style.display = "inline-block"
                        }
                    }

            })
            .catch(error => console.log(error));
}

document.addEventListener("DOMContentLoaded", () => {

    bascet_to_page();

    let add_buttons = document.querySelectorAll(".to_bascet")

    for (let elem of add_buttons)
        elem.addEventListener("click", function (e) {
            e.preventDefault()
            let product_id = elem.dataset.prodid;
            let tiken = document.querySelector('meta[name="_token"]').content;

            let data = new FormData()
            data.append("product_id", product_id)
            data.append("_token", tiken)


            let main_card = document.querySelector(".main-prod-card[data-prodid='"+product_id+"']");

            let thisBtn = this;
            if (main_card == null)
             {
                thisBtn.querySelector(".card_to_bascet_btn .nadp").style.display = "none"
                thisBtn.querySelector(".card_to_bascet_btn .btnLoader").style.display = "block"
             } else {
                main_card.querySelector(".card_to_bascet_btn .nadp").style.display = "none"
                main_card.querySelector(".card_to_bascet_btn .btnLoader").style.display = "block"
             }



            var xhr = new XMLHttpRequest()

                xhr.open("post", "/bascet/add", true)
                xhr.responseType = 'json'
                xhr.setRequestHeader('Accept', 'application/json')

                xhr.onload = function () {
                    if (xhr.status == 422) {

                        for (let error of Object.entries(xhr.response.errors) )
                        {
                            console.log(error[0])
                            console.log(error[1][0])
                        }
                    } else

                    if (xhr.status == 200) {


                        update_counter(parseInt(count_in_bascet)+1)

                        if (main_card == null) {
                            thisBtn.querySelector(".card_to_bascet_btn .nadp").style.display = "block"
                            thisBtn.querySelector(".card_to_bascet_btn .btnLoader").style.display = "none"
                        } else {
                            main_card.classList.add("in-bascet")
                            main_card.querySelector(".card_to_bascet_btn").style.display = "none"
                            main_card.querySelector(".card_bascet_btn").style.display = "inline-block"
                        }
                    }

                };

                xhr.onerror = function () {
                    alert(xhr.responseText)
                    console.log(xhr.status)
                };

                xhr.send(data);
        });

})
