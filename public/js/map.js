ymaps.ready(init);

function init () {
    let centerMap = [36.17530106745902, 51.777271793676064]
    var myMap = new ymaps.Map("render_map", {
        // Координаты центра карты
        center: centerMap,
        // Масштаб карты
        zoom: 18,
        // Выключаем все управление картой
        controls: []
    });

    var myGeoObjects = [];

    // Указываем координаты метки
    myGeoObjects = new ymaps.Placemark(centerMap,{
                                    hintContent: '<div class="map-hint">Магазин цветов Florida</div>',
                                    balloonContent: '<div class="map-hint"><b>Магазин цветов Florida</b> <br/> г. Курск, пр. Победы, 14</div>',
                                    }
                                    ,{
                                        iconLayout: 'default#image',
                    // Путь до нашей картинки
                    iconImageHref: '/img/icons/map-pin-fill.svg',
                    // Размеры иконки
                    iconImageSize: [62, 60],
                    // Смещение верхнего угла относительно основания иконки
                    iconImageOffset: [-31, -45]
                });

    var clusterer = new ymaps.Clusterer({
        clusterDisableClickZoom: false,
        clusterOpenBalloonOnClick: false,
    });

    clusterer.add(myGeoObjects);
    myMap.geoObjects.add(clusterer);
    // Отключим zoom
    myMap.behaviors.disable('scrollZoom');

}
