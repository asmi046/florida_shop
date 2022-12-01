class Delivery {

    constructor() {
        this.create()
    }


    create() {
        ymaps.ready(() => {
            // Получение зон
            this.getZones().then(async response => {
                this.zones = response

                // Рендер карты
                await this.renderMap()

                if (this.needInputHandler) {
                    if (this.addresses_id && this.addresses_id.length) {
                        this.addressIdChange()
                    } else {
                        this.inputHandler()
                    }
                }
            })
        });
    }

    getZones() {
        return new Promise(async resolve => {
            let response = await fetch('zones.json')

            response = await response.json()

            resolve(response)
        })
    }
}

let delivery = new Delivery();

export default delivery;
