<h1>Письмо с сайта</h1>
<p><strong>Имя:</strong> {{$formData['fio']}}</p>
<p><strong>Телефон:</strong> {{$formData['phone']}}</p>
<p><strong>E-mail:</strong> {{$formData['email']}}</p>
<p><strong>Адрес:</strong> {{$formData['adress']}}</p>
<p><strong>Комментарий:</strong> {{$formData['comment']}}</p>
<table style="width:100%; border-top:1px solid black; border-left:1px solid black; border-spacing: 0;">
    <thead style="text-align:left;">
        <tr>
            <th style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">Картинка</th>
            <th style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">Наименование</th>
            <th style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">Цена</th>
            <th style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">Колличество</th>
            <th style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">Сумма</th>
        </tr>
    </thead>
    <tbody>
           @foreach ($formData['tovars'] as $item)


                <tr>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;"><img width="70" height="70" src="{{asset('/storage'.$item["tovar_data"]["img"])}}" alt=""></td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{$item["tovar_data"]["title"]}}<br/><span style="font-size:12px">Артикул: {{$item["product_sku"]}}</span></td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{$item["tovar_data"]["price"]}}</td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{$item["quentity"]}}</td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{(float)$item["quentity"] * (float)$item["tovar_data"]["price"]}}</td>
                </tr>
            @endforeach
    </tbody>
</table>
<hr/>
