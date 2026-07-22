## Комманда для получения данных из сервиса Мой склад

Сделай комманду артисан которая будет запрашивать данные из сервиса. Для понимания структуры вот curl запрос для получения данных:

```curl
curl --compressed \
  -X GET "https://api.moysklad.ru/api/remap/1.2/entity/assortment?limit=10000" \
  -u "<login>:<password>" \
  -H "Accept: application/json;charset=utf-8" \
  -H "Accept-Encoding: gzip"
```

Логин и пароль возьми из конфига котороый создал ранее.

На первом этапе сохрани полученные данные в json файл в папку public

Далее будем развивать этот скрипт.

### Запрос остатков

В том же скрипте сделай запрос на получение остатков. Вот curl запрос на получение данных

```curl
curl --request GET \
  --url "https://api.moysklad.ru/api/remap/1.2/report/stock/all/current?stockType=freeStock&include=zeroLines" \
  --user "LOGIN:PASSWORD" \
  --header "Accept: application/json;charset=utf-8"
```

Результат так же помести в файл

## Перернос данных в базу

Переделываем скрипт на работу с базой данных.

### Миграции и модели

Для реализации задачи создай 2 модели к которым сделай миграции

- MySkladAssortiment
- MySkladStock

В каждую из моделей данные будут записываться в соответствии с выполняемыми запросами.

**Поля для модели MySkladAssortiment**

- sklad_id - uuid пример 05cbd7e2-56c3-11f1-0a80-0c95001dc55d (берем из поля "id" массива rows)
- type - строковое берем из meta.type
- name - строковое длинна 500
- code - строковое
- externalCode - строковое
- pathName - строковое
- components_href - строковый длинна 700 nullable (Берем из components.meta.href)
- components_size - целочисленное nullable (Берем из components.meta.size)

В результатах запроса есть поле с массивом "rows" в котором есть структура которую и нужно обрабатывать. Пример:

```json
      {
            "meta": {
                "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/service\/b42bea77-b65b-11f0-0a80-0bac001083a4",
                "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/product\/metadata",
                "type": "service",
                "mediaType": "application\/json",
                "uuidHref": "https:\/\/online.moysklad.ru\/app\/#good\/edit?id=b42be608-b65b-11f0-0a80-0bac001083a2"
            },
            "id": "b42bea77-b65b-11f0-0a80-0bac001083a4",
            "accountId": "bcc4635f-b129-11ef-0a80-198400001b30",
            "owner": {
                "meta": {
                    "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/employee\/bd1593a8-b129-11ef-0a80-1a7d0002b68a",
                    "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/employee\/metadata",
                    "type": "employee",
                    "mediaType": "application\/json",
                    "uuidHref": "https:\/\/online.moysklad.ru\/app\/#employee\/edit?id=bd1593a8-b129-11ef-0a80-1a7d0002b68a"
                }
            },
            "shared": true,
            "group": {
                "meta": {
                    "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/group\/bcc5a653-b129-11ef-0a80-198400001b31",
                    "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/group\/metadata",
                    "type": "group",
                    "mediaType": "application\/json"
                }
            },
            "updated": "2026-06-05 13:44:48.508",
            "name": "Доставка Центральный 2",
            "description": "Клыкова, пр-кт Дружбы, 50 лет октября...",
            "code": "00570379",
            "externalCode": "hicSaEbhiq5eXmcxFkFe61",
            "archived": false,
            "pathName": "Доставка",
            "productFolder": {
                "meta": {
                    "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/productfolder\/e58b2adb-8e32-11f0-0a80-0ee500205566",
                    "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/productfolder\/metadata",
                    "type": "productfolder",
                    "mediaType": "application\/json",
                    "uuidHref": "https:\/\/online.moysklad.ru\/app\/#good\/edit?id=e58b2adb-8e32-11f0-0a80-0ee500205566"
                }
            },
            "effectiveVat": 5,
            "effectiveVatEnabled": true,
            "vat": 5,
            "vatEnabled": true,
            "useParentVat": false,
            "uom": {
                "meta": {
                    "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/uom\/19f1edc0-fc42-4001-94cb-c9ec9c62ec10",
                    "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/uom\/metadata",
                    "type": "uom",
                    "mediaType": "application\/json"
                }
            },
            "minPrice": {
                "value": 0,
                "currency": {
                    "meta": {
                        "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/bd2b22f9-b129-11ef-0a80-1a7d0002b6db",
                        "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/metadata",
                        "type": "currency",
                        "mediaType": "application\/json",
                        "uuidHref": "https:\/\/online.moysklad.ru\/app\/#currency\/edit?id=bd2b22f9-b129-11ef-0a80-1a7d0002b6db"
                    }
                }
            },
            "salePrices": [
                {
                    "value": 15000,
                    "currency": {
                        "meta": {
                            "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/bd2b22f9-b129-11ef-0a80-1a7d0002b6db",
                            "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/metadata",
                            "type": "currency",
                            "mediaType": "application\/json",
                            "uuidHref": "https:\/\/online.moysklad.ru\/app\/#currency\/edit?id=bd2b22f9-b129-11ef-0a80-1a7d0002b6db"
                        }
                    },
                    "priceType": {
                        "meta": {
                            "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/context\/companysettings\/pricetype\/bd2b508a-b129-11ef-0a80-1a7d0002b6dc",
                            "type": "pricetype",
                            "mediaType": "application\/json"
                        },
                        "id": "bd2b508a-b129-11ef-0a80-1a7d0002b6dc",
                        "name": "Цена по умолчанию",
                        "externalCode": "cbcf493b-55bc-11d9-848a-00112f43529a"
                    }
                },
                {
                    "value": 15000,
                    "currency": {
                        "meta": {
                            "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/bd2b22f9-b129-11ef-0a80-1a7d0002b6db",
                            "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/metadata",
                            "type": "currency",
                            "mediaType": "application\/json",
                            "uuidHref": "https:\/\/online.moysklad.ru\/app\/#currency\/edit?id=bd2b22f9-b129-11ef-0a80-1a7d0002b6db"
                        }
                    },
                    "priceType": {
                        "meta": {
                            "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/context\/companysettings\/pricetype\/9857b7d7-8fe5-11f0-0a80-10e70012685d",
                            "type": "pricetype",
                            "mediaType": "application\/json"
                        },
                        "id": "9857b7d7-8fe5-11f0-0a80-10e70012685d",
                        "name": "Цена доплаты",
                        "externalCode": "6ab11d0f-c0a3-41ad-92c0-9685cff5f8c2"
                    }
                }
            ],
            "buyPrice": {
                "value": 0,
                "currency": {
                    "meta": {
                        "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/bd2b22f9-b129-11ef-0a80-1a7d0002b6db",
                        "metadataHref": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/currency\/metadata",
                        "type": "currency",
                        "mediaType": "application\/json",
                        "uuidHref": "https:\/\/online.moysklad.ru\/app\/#currency\/edit?id=bd2b22f9-b129-11ef-0a80-1a7d0002b6db"
                    }
                }
            },
            "barcodes": [
                {
                    "ean13": "2000000011776"
                }
            ],
            "paymentItemType": "SERVICE",
            "discountProhibited": false,
            "files": {
                "meta": {
                    "href": "https:\/\/api.moysklad.ru\/api\/remap\/1.2\/entity\/service\/b42bea77-b65b-11f0-0a80-0bac001083a4\/files",
                    "type": "files",
                    "mediaType": "application\/json",
                    "size": 0,
                    "limit": 1000,
                    "offset": 0
                }
            }
        },
```

**Поля для модели MySkladStock**

- assortmentId - пример 91433f82-d92c-11f0-0a80-096300253127 сделай ключем
- freeStock - целочисленное пример: 85

В результатах запроса сразу есть массив в которм есть структуры с одноименными полями

```json
[
    {
        "assortmentId": "0067e00d-7012-11f1-0a80-14bd000160ad",
        "freeStock": -2
    },
    {
        "assortmentId": "0b260bd0-5a92-11f1-0a80-09d8000efc61",
        "freeStock": 50
    },
```

В рамках выполнения задачи зарегистрируй отдельный канал логирования назови его my_sklad. Записывай в него следующие данные:

- Тип сессии константа - get_data
- Длата и время начала получения данных
- Количество обработанных строк ассортимента (MySkladAssortiment)
- Колличество обработанных строк наличия (MySkladStock)
- Длата и время завершения получения данных

## Создание скрипта для обновления данных товаров

Цель: создать артисан комманду которая обновит данные в элементах модели Product по описанному алгоритму.

### Дополнение модели Product и ее миграции

Сосдай миграцию которая дополнит таблицу `products` полями:

- skladСount - целочисленное по умолчанию 0
- code - строковое nullable
- externalCode - строковое nullable

Добавь эти поля в модель

### Алгоритм работы коммпнды

Комманда выбирает все элементы Product в которых заполнено поле code и externalCode и запускает по ним цикл.

В цикле ищем по полю externalCode соответствующий элемент в таблице `my_sklad_assortiments` в полученной строке берем поле
components_href если оно не null делаем запрос по ссылке которая находится в этом поле. Результатмо будет вот такая структура:

```json
{
    "context": {
        "employee": {
            "meta": {
                "href": "https://api.moysklad.ru/api/remap/1.2/context/employee",
                "metadataHref": "https://api.moysklad.ru/api/remap/1.2/entity/employee/metadata",
                "type": "employee",
                "mediaType": "application/json"
            }
        }
    },
    "meta": {
        "href": "https://api.moysklad.ru/api/remap/1.2/entity/bundle/05cbd7e2-56c3-11f1-0a80-0c95001dc55d/components",
        "type": "bundlecomponent",
        "mediaType": "application/json",
        "size": 2,
        "limit": 1000,
        "offset": 0
    },
    "rows": [
        {
            "meta": {
                "href": "https://api.moysklad.ru/api/remap/1.2/entity/bundle/05cbd7e2-56c3-11f1-0a80-0c95001dc55d/components/05cbdd34-56c3-11f1-0a80-0c95001dc561",
                "type": "bundlecomponent",
                "mediaType": "application/json"
            },
            "id": "05cbdd34-56c3-11f1-0a80-0c95001dc561",
            "accountId": "bcc4635f-b129-11ef-0a80-198400001b30",
            "assortment": {
                "meta": {
                    "href": "https://api.moysklad.ru/api/remap/1.2/entity/product/755615b4-c0b1-11f0-0a80-174b00002074",
                    "metadataHref": "https://api.moysklad.ru/api/remap/1.2/entity/product/metadata",
                    "type": "product",
                    "mediaType": "application/json",
                    "uuidHref": "https://online.moysklad.ru/app/#good/edit?id=755609fc-c0b1-11f0-0a80-174b00002072"
                }
            },
            "quantity": 6.0
        },
        {
            "meta": {
                "href": "https://api.moysklad.ru/api/remap/1.2/entity/bundle/05cbd7e2-56c3-11f1-0a80-0c95001dc55d/components/05cbdd9a-56c3-11f1-0a80-0c95001dc562",
                "type": "bundlecomponent",
                "mediaType": "application/json"
            },
            "id": "05cbdd9a-56c3-11f1-0a80-0c95001dc562",
            "accountId": "bcc4635f-b129-11ef-0a80-198400001b30",
            "assortment": {
                "meta": {
                    "href": "https://api.moysklad.ru/api/remap/1.2/entity/product/0d721ad6-381a-11f1-0a80-13ba004e7c1e",
                    "metadataHref": "https://api.moysklad.ru/api/remap/1.2/entity/product/metadata",
                    "type": "product",
                    "mediaType": "application/json",
                    "uuidHref": "https://online.moysklad.ru/app/#good/edit?id=0d720fcf-381a-11f1-0a80-13ba004e7c1c"
                }
            },
            "quantity": 5.0
        }
    ]
}
```

Берем массив из поля "rows" перебираем элементы и составляем промежуточный массив в который переносим "id" и "quantity" Так же составляем запрос к таблице `my_sklad_stocks` в который включаем найденные "id". Цель запроса получить из таблицы `my_sklad_stocks` наличие по всем найденным "id" и дополнить формируемую структуру полем stock значение для которого берем из my_sklad_stocks.freeStock. На выходе должен получится массив следующего вида:

```php
[
    [
        "id" => "05cbdd34-56c3-11f1-0a80-0c95001dc561", // id товара в составе продукта
        "quantity" => 6.0, // колличество товара необходимое для формирования продукта
        "stock" => 8 // колличество этого товара на складе
    ],

    [
        "id" => "05cbdd9a-56c3-11f1-0a80-0c95001dc562",
        "quantity" => 5.0,
        "stock" => 18
    ],

    ...
]

```

На основе данных из этого массива нужно определить:

- Можно ли сформировать продукт из остатков на складе
- Колличество продуктов которое можно сформировать исходя из остатков на складе

Если товар можно сформировать то asc_nal устанавливаем в true.
Посчитанное колличество записываем в поле skladСount.

Все действия выпологируй в созданном канале логирования. При этом фиксируй время и дату начала/конца обновления и проставь тип сессии - update_data

Логику калькуляции и обновления вынеси в отдельный ларавель сервис чтобы разгрузить код комманды.
