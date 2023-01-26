export default class Delivery {

    map = null
    map_dom_element = null
    map_dom_element_show = null
    zones = null;

    constructor(element, show) {
        this.map_dom_element = element
        this.map_dom_element_show = show

        // Полигоны
        this.poligons = []

        this.map_dom_element.style.display = (show)?"block":"none"

        this.create()
    }

    getZoneCoord() {
        return this.zones;
    }

    getDeliveryPrice(adress = '') {
        if (adress == '') return 0;
        const myGeocoder = ymaps.geocode(adress, {
            results: 1,
        });
        myGeocoder.then((response) => {
            console.log(response.geoObjects.get(0).geometry._coordinates)
        })
        return adress
    }

    renderMap() {
        this.map_dom_element.querySelector('.zones__map-label').hidden = true;
        this.map = new ymaps.Map(this.map_dom_element, {
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

        this.map.geoObjects.each((geoObject) => {
            if (geoObject.geometry.getType() == "Polygon") {
                this.poligons.push(geoObject)
            }
        });

        this.map.geoObjects.events.add('click', (e) => {
            const object = e.get('coords');

            const myGeocoder = ymaps.geocode(object, {
                results: 1,
            });

            myGeocoder.then((response) => {
                console.log(response)
                const coords = response.metaData.geocoder.request.split(',')

                this.poligons.forEach(poligon => {
                    if (poligon.geometry.contains(coords)) {

                        console.log(poligon.properties._data.description)



                        if (this.map_dom_element.querySelector('.zones__map-label')) {

                            console.log(this.map_dom_element.querySelector('.zones__map-label'))
                            this.map_dom_element.querySelector('.zones__map-label').hidden = false

                            this.map_dom_element.querySelector('.zones__map-label').hidden = false
                            this.map_dom_element.querySelector('.zones__map-label').innerHTML = poligon.properties._data.description
                        }


                        let placemark = new ymaps.Placemark(coords, {}, {
                            preset: 'islands#icon',
                            iconColor: '#7F0FD6'
                        });


                        this.map.geoObjects.remove(this.current_placemark);

                        this.current_placemark = placemark
                        this.map.geoObjects.add(this.current_placemark);
                    }
                })
            });
        });

        this.map.events.add('click', (e) => {
            const object = e.get('coords');

            const placemark = new ymaps.Placemark(object, {
                iconContent: 'Данный адрес не входит в зону доставки',
            }, {
                iconContentLayout: this.icon_layout,
            });


            this.map.geoObjects.remove(this.current_placemark);
            this.map.geoObjects.add(placemark);

            this.current_placemark = placemark;

            this.street.value = ''
            this.house.value = ''
        });

    }

    create() {
        ymaps.ready(() => {
            // Получение зон
            this.getZones().then(async response => {
                this.zones = response

                // Рендер карты
                await this.renderMap()

                // if (this.needInputHandler) {
                //     if (this.addresses_id && this.addresses_id.length) {
                //         this.addressIdChange()
                //     } else {
                //         this.inputHandler()
                //     }
                // }
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

