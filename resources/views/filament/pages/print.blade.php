
<?php
\Cknow\Money\Money::setLocale('ar');
app()->setLocale('ar');
$settings = new \Tasawk\Settings\GeneralSettings();
$totals = $order->as_cart->formattedTotals();
$native_totals = $order->as_cart->totals();
$generatedString = \Salla\ZATCA\GenerateQrCode::fromArray([
    new \Salla\ZATCA\Tags\Seller('naeem'),
    new \Salla\ZATCA\Tags\TaxNumber('10000000000'),
    new \Salla\ZATCA\Tags\InvoiceDate($order->created_at?->toIso8601String()),
    new \Salla\ZATCA\Tags\InvoiceTotalAmount($totals['total']),
    new \Salla\ZATCA\Tags\InvoiceTaxAmount($totals['taxes']) // invoice tax amount
])->render();
?>
    <!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naeem Alshemal Invoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
          rel="stylesheet">
    <style>
        body {
            font-family: "Tajawal", sans-serif;
            background-color: #fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        h1,
        h2,
        h4,
        h5,
        h3,
        h6,
        p {
            margin: 0;
            margin-bottom: 5px;
        }

        td {
            vertical-align: top;
        }

        tr td {
            padding-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #EEEEEE;
        }

        .bg-grey {
            background-color: #EEEEEE;
        }

        .img-wrapper {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #EEEEEE;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .details {
            border-bottom: 1px solid #EEEEEE;
            padding: 15px 0;
        }

        .product {
            width: fit-content;
        }

        .product-desc {
            float: left;
            margin-right: 20px;
            width: 250px;
        }

        .product-details {
            display: flex;
            width: fit-content;
        }

        tr,
        th,
        td {
            text-align: start;
            padding-inline: 15px;
            height: 30px;
        }

        @page {
            size: A4;
        }

        @media print {
            body {
                width: 210mm;
                height: 297mm;
                padding-top: 12mm;
                margin: 0mm 10mm;
            }
        }
    </style>
</head>

<body>

<main class="main" style="width: 85%;">
    <div class="img-wrapper">
        <img src="{{asset("storage/$settings->app_logo")}}" style="width: 35px; text-align: center;margin-right: auto;"
             alt="">
        <img src="{{$generatedString}}" style="width: 60px; margin-right: auto;" alt="">
    </div>
    <div class="details">
        <div class="details-item" style="display: flex; align-items: center; justify-content: space-between;">
            <h3>رقم الفاتورة
                <span class="number">
                        #10{{$order->order_number}}
                    </span>
            </h3>
            <p class="date-time">
                {{--                Thursday 22 August 2024 | 06:50 PM--}}

                {{now()->translatedFormat('l d M Y | H:i a')}}
            </p>
        </div>
        <h5 class="details-item">تفاصيل الطلب</h5>
        <h5 class="details-item">رقم الطلب : {{$order->order_number}} </h5>
        <h5 class="details-item">الرقم الضريبى : 312114058100003</h5>
    </div>
    <div class="details" style="display: flex;">
        <div style="width: 65%;">
            <h3 style="text-decoration: underline;">مصدرة من : </h3>
            <h5 class="details-item">
                المتجر الالكترونى : مؤسسة نعيمى الشمال
            </h5>
            <h5 class="details-item">السعودية</h5>
            <h5 class="details-item">حفر الباطن </h5>
            <h5 class="details-item">عمر بن عيد العزيز 12525,52854, حى الخالدية,855858,CNFM857</h5>
            <h5 class="details-item">عمر بن عيد العزيز 12525,52854, حى الخالدية</h5>
            <h5 class="details-item">Saudi Arabia , Batin 52828 , حفر الباطن , السعودية</h5>
            <h5 class="details-item">naeemalshemal@gmail.com</h5>
            <h5 class="details-item" style="direction: ltr; text-align: end;">+966530634456 / +966530634456</h5>

        </div>
        <div style="width: 35%;">
            <h3 style="text-decoration: underline;">مصدرة الى : </h3>
            <h5 class="details-item">{{$order->customer->name}}</h5>
            <h5 class="details-item" style="
    direction: ltr;
    text-align: right;
">+966{{$order->customer->phone}}</h5>
        </div>
    </div>
    <div class="details">
        <h3 class="details-item" style="text-decoration: underline;">تفاصيل الدفع : </h3>
        <h5 class="details-item">
            <span style="display: inline-block; width: 120px">المبلغ :</span>

            <span>{{$order->total->format()}}</span>
        </h5>
        <h5 class="details-item">
            <span style="display: inline-block; width: 120px">طريقة الدفع :</span>
            <span>{{\Tasawk\Enum\PaymentMethods::tryFrom($order->payment_data['method'])?->getLabel() ?? $order->payment_data['method'] ?? ''}}</span>
        </h5>
    </div>
    <table>
        <tbody>
        <tr class="bg-grey">
            <th colspan="2" style="text-align: right;">المنتج</th>
            <th colspan="1">الكمية</th>
            <th colspan="2">السعر</th>
            <th colspan="1" style="text-align: end;">المجموع</th>
        </tr>
        @foreach($order->as_cart->getContent() as $item )
            <tr>

                <td colspan="2" style="width: 60%;">
                    <div class="product">
                        <img style="width: 100px;" src="product.jpg" alt="">
                        <div class="product-desc">
                            <h5>
                                {{$item->name}}
                            </h5>
                            @if(count($item->attributes['options']??[]))
                                <h5>التحديد على جميع الخيارت ليظهر لك السعر الاجمالى </h5>
                            @endif
                        </div>

                    </div>
                    @if(count($item->attributes['options']??[]))
                        <h3 style="text-decoration: underline;">خيارات المنتج</h3>
                        @foreach($item->attributes['options']??[] as $option)
                                <?php
                                $option_name = \Tasawk\Lib\Utils::getTranslatedField($option['name']);
                                $value = \Tasawk\Lib\Utils::getTranslatedField($option['value']);

                                ?>
                            <div class="product-details">
                                <h5 style="width: 250px;">{{$option_name}} : </h5>
                                <p>{{$value}}</p>
                            </div>
                        @endforeach
                    @endif
                </td>
                <td colspan="1">{{$item->quantity}}</td>
                <td colspan="2">{{\Cknow\Money\Money::parse($item->getPriceWithConditions())->format()}}</td>
                <td colspan="1"
                    style="text-align: end;"> {{\Cknow\Money\Money::parse($item->getPriceSumWithConditions())->format()}}</td>


            </tr>
        @endforeach

        <tr style="border-top: 4px solid #EEEEEE;">
            <td colspan="3" style="vertical-align: middle;">
                <h5>مجموع السلة</h5>
            </td>
            <td colspan="3" style="text-align: end; vertical-align: middle;">
                <h5>{{$totals['items_total_with_options']}}</h5>
            </td>
        </tr>
        @if($native_totals['discount'])

            <tr style="border-top: 4px solid #EEEEEE;">
                <td colspan="3" style="vertical-align: middle;">
                    <h5>خصم</h5>
                </td>

                <td colspan="3" style="text-align: end; vertical-align: middle;">
                    <h5>  {{$totals['discount']}}</h5>
                </td>
            </tr>
        @endif
        <tr class="">
            <td style="vertical-align: middle;" colspan="3">
                <h5>ضريبة القيمه المضافة</h5>

            </td>
            <td colspan="3" style="text-align: end;vertical-align: middle;">
                <h5>{{$totals['taxes']}}</h5>

            </td>
        </tr>
        @if($native_totals['delivery'])
            <tr class="" s>
                <td style="vertical-align: middle;" colspan="3">
                    <h5>رسوم التوصيل</h5>

                </td>
                <td colspan="3" style="text-align: end;vertical-align: middle;">
                    <h5>{{$totals['delivery']}}</h5>

                </td>
            </tr>
        @endif
        @if($native_totals['cash_on_delivery_fees'])
            <tr class="" s>
                <td style="vertical-align: middle;" colspan="3">
                    <h5>رسوم الدفع عند الاستلام</h5>

                </td>
                <td colspan="3" style="text-align: end;vertical-align: middle;">
                    <h5>{{$totals['cash_on_delivery_fees']}}</h5>

                </td>
            </tr>
        @endif
        <tr class="bg-grey" style="background-color: #EEEEEE;">
            <td style="vertical-align: middle;" colspan="3">
                <h5>اجمالى الطلب</h5>

            </td>
            <td colspan="3" style="text-align: end;vertical-align: middle;">
                <h5>{{$totals['total']}}</h5>

            </td>
        </tr>
        @if(!in_array($order->payment_data['gateway'],['myfatoorah','tamara','tabby','bank_transfer']))
            <tr class="bg-grey" style="background-color: #EEEEEE;">
                <td style="vertical-align: middle;" colspan="3">
                    <h5>المتبقي</h5>

                </td>
                <td colspan="3" style="text-align: end;vertical-align: middle;">
                    <h5>{{in_array($order->payment_data['gateway'],['myfatoorah','tamara','tabby','bank_transfer'])?0:$totals['total']}}</h5>

                </td>
            </tr>
        @endif
        </tbody>
    </table>

</main>

<script>
    window.print();
</script>
</body>

</html>
