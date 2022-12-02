class Delivery {

    map = null

    constructor() {
        this.create()
    }

    renderMap(mapContainer) {
        this.map = new ymaps.Map(mapContainer, {
            center: [36.189709, 51.742988],
            zoom: 8,
            controls: [],
        });

        let delivery_zones = ymaps.geoQuery(this.zones).addToMap(this.map);
        // Задаём цвет и контент балунов полигонов.
        delivery_zones.each(function (obj) {
            const fillColor = obj.properties.get('fill');
            const fillOpacity = obj.properties.get('fill-opacity');
            const strokeColor = obj.properties.get('stroke');
            const strokeOpacity = obj.properties.get('stroke-opacity');
            const strokeWidth = obj.properties.get('stroke-width');

            obj.options.set({fillColor: fillColor, fillOpacity: fillOpacity, strokeColor: strokeColor, strokeOpacity: strokeOpacity, strokeWidth: strokeWidth});
        });
    }

    create() {
        ymaps.ready(() => {
            // Получение зон
            this.getZones().then(async response => {
                this.zones = response

                // Рендер карты
                // await this.renderMap()

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
            let response = await fetch('/files/zones.json')

            response = await response.json()

            resolve(response)
        })
    }
}

let delivery = new Delivery();

export default delivery;
