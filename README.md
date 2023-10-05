Shoparize Partner Tracking Plugin for WooCommerce
================================================

Setting page:
![](./imgs/img1.png)

After installing the plugin, new shoparize partner tab will be available at admin product page:
![](./imgs/img2.png)

### Product Api

Example of request:
```text
GET http://localhost:8080/?rest_route=/shoparize-partner/products&page=1&limit=2&updated_after=2023-09-19T16%3A05%3A53%2B03%3A00
Shoparize-Partner-Key: 999999
```

Example of response:
```json
{
  "items": [
    {
      "id": 14,
      "title": "V-Neck T-Shirt",
      "description": "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
      "link": "http:\/\/localhost:8080\/?product=v-neck-t-shirt",
      "images": [
        "http:\/\/localhost:8080\/wp-content\/uploads\/2023\/08\/vneck-tee-2.jpg",
        "http:\/\/localhost:8080\/wp-content\/uploads\/2023\/08\/vnech-tee-green-1.jpg",
        "http:\/\/localhost:8080\/wp-content\/uploads\/2023\/08\/vnech-tee-blue-1.jpg"
      ],
      "mobile_link": "http:\/\/localhost:8080\/?product=v-neck-t-shirt",
      "availability": "in_stock",
      "price": "0.00",
      "brand": "",
      "gtin": "",
      "condition": "",
      "currency_code": "USD",
      "shipping_length": "",
      "shipping_width": "",
      "shipping_height": "",
      "shipping_weight": "",
      "size_unit": "cm",
      "sale_price": "15.00",
      "colors": [
        "Blue",
        "Green",
        "Red"
      ],
      "sizes": [
        "Large",
        "Medium",
        "Small"
      ],
      "shipping": {
        "country": "PL",
        "service": "Free shipping",
        "price": "0.00"
      },
      "weight_unit": "kg"
    },
    {
      "id": 16,
      "title": "Hoodie with Logo",
      "description": "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
      "link": "http:\/\/localhost:8080\/?product=hoodie-with-logo",
      "images": [
        "http:\/\/localhost:8080\/wp-content\/uploads\/2023\/08\/hoodie-with-logo-2.jpg"
      ],
      "mobile_link": "http:\/\/localhost:8080\/?product=hoodie-with-logo",
      "availability": "in_stock",
      "price": "45.00",
      "brand": "",
      "gtin": "",
      "condition": "",
      "currency_code": "USD",
      "shipping_length": "",
      "shipping_width": "",
      "shipping_height": "",
      "shipping_weight": "",
      "size_unit": "cm",
      "sale_price": "45.00",
      "colors": [
        "Blue",
        "Gray",
        "Green",
        "Red",
        "Yellow"
      ],
      "sizes": [
        "Large",
        "Medium",
        "Small"
      ],
      "shipping": {
        "country": "PL",
        "service": "Free shipping",
        "price": "0.00"
      },
      "weight_unit": "kg"
    }
  ]
}
```