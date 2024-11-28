import { result } from "lodash";

export class DataLayer {

    static getCategories(tovar_categories) {
        return (tovar_categories.length > 0)?tovar_categories[0].title:"";
    }

    static getCartList(list) {
        let result = [];

        for (let elem in list) {
            let addet = {}
            addet['id'] = list[elem].tovar_data.sku
            addet['name'] = list[elem].tovar_data.title
            addet['price'] = list[elem].tovar_data.price
            addet['brand'] = "Florida46"
            addet['quantity'] = list[elem].quentity

            result.push(addet)
        }

        return result;
    }

    static sendCart(tovar_list, order_id) {

        window.dataLayerEc.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "purchase": {
                    "actionField": {
                        "id" : order_id
                    },
                    "products": DataLayer.getCartList(tovar_list)
                }
            }
        });
    }

    static sendOneClick(tovar, order_id) {
        console.log(tovar)

        window.dataLayerEc.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "purchase": {
                    "actionField": {
                        "id" : order_id
                    },
                    "products": [
                        {
                            "id": tovar[0].product_sku,
                            "name": tovar[0].tovar_data.title,
                            "price": tovar[0].price,
                            "brand": "Florida46",
                            "quantity": 1,
                        },
                    ]
                }
            }
        });
    }

    static addToCatr(data, quantity) {
        window.dataLayerEc.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "add": {
                    "products": [
                        {
                            "id": data.sku,
                            "name" : data.title,
                            "price": data.price,
                            "brand": "Florida46",
                            "category": DataLayer.getCategories(data.tovar_categories),
                            "quantity": quantity
                        }
                    ]
                }
            }
        })
    }

    static sendDetail(data) {
        window.dataLayerEc.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "detail": {
                    "products": [
                        {
                            "id": data.sku,
                            "name" : data.title,
                            "price": data.price,
                            "brand": "Florida46",
                            "category": DataLayer.getCategories(data.tovar_categories),
                        }
                    ]
                }
            }
        });
    }
}
