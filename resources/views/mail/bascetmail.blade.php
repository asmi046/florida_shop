<h1>Письмо с сайта</h1>
<p><strong>Имя:</strong> {{$formData['fio']}}</p>
<p><strong>Телефон:</strong> {{$formData['phone']}}</p>
<p><strong>E-mail:</strong> {{$formData['email']}}</p>

<h2>Получатель</h2>
<p><strong>Имя получателя:</strong> {{$formData['polname']}}</p>
<p><strong>Телефон получателя:</strong> {{$formData['polphone']}}</p>

<h2>Адрес доставки</h2>
<p><strong>Адрес:</strong> {{$formData['adress_fk']}}</p>
<p><strong>Подъезд:</strong> {{$formData['podezd']}}</p>
<p><strong>Этаж:</strong> {{$formData['etazg']}}</p>
<p><strong>Квартира:</strong> {{$formData['kvartira']}}</p>

<h2>Время доставки</h2>
<p><strong>Дата:</strong> {{$formData['data']}}</p>
<p><strong>Время:</strong> {{$formData['time']}}</p>

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
           @foreach ($formData['items'] as $item)


                <tr>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;"><img width="70" height="70" src="{{asset($item["img"])}}" alt=""></td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{$item["title"]}}<br/><span style="font-size:12px">Артикул: {{$item["sku"]}}</span></td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{$item["price"]}}</td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{$item["quantity"]}}</td>
                    <td style="padding: 5px; border-bottom:1px solid black; border-right:1px solid black;">{{(float)$item["total"]}}</td>
                </tr>
            @endforeach
    </tbody>
</table>

<h2>Итого {{$formData['count']}} товар(ов) на сумму {{$formData['amount']}} ₽</h2>
<hr/>
