<!DOCTYPE html>
<html>

<head>
    <title>Nota Fiscal</title>
</head>
<style type="text/css">
    body {
        font-family: 'Roboto Condensed', sans-serif;
        margin-top: 0;
        padding-top: 0;
    }

    div {
        margin-top: 0;
        padding-top: 0;
    }

    p {
        margin-top: 0;
        padding-top: 0;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .pt-5 {
        padding-top: 5px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
    }

    .w-50 {
        width: 50%;
    }

    .w-85 {
        width: 85%;
    }

    .w-15 {
        width: 15%;
    }

    .logo img {
        width: 200px;
        height: 60px;
    }

    .gray-color {
        color: #5D5D5D;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    table tr,
    th,
    td {
        border: 1px solid #000000;
        border-collapse: collapse;
        padding: 5px 3px;
    }

    tr {
        border: 1px solid #000000;
        border-collapse: collapse;
        padding: 5px 3px;
    }

    table tr th {
        background: #F4F4F4;
        font-size: 15px;
    }

    table tr td {
        font-size: 13px;
        white-space: nowrap;
    }

    table {
        border: 1px solid #000000;
        border-collapse: collapse;
    }

    .box-text p {
        line-height: 10px;
    }

    .float-left {
        float: left;
    }

    .total-part {
        font-size: 16px;
        line-height: 12px;
    }

    .total-right p {
        padding-right: 0px;
    }
</style>

<body>
    <div class="">
        <p class="">:: NFS-e - Nota Fiscal de Serviços eletrônica ::</p>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Id do pedido - <span class="gray-color"> {{ $pdfData['order_id'] }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Data de emissão: - <span class="gray-color"> {{ $pdfData['order_date'] }}</span></p>
        </div>
        <div class="w-50 float-left logo mt-10">
            <img src="" alt="Logo">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">


            <tr>
                <th>Destinatário/Remetente</th>
                <th>Tomador do(s) Serviço(s)</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p>Nome/Razão Social: {{ $pdfData['user_name'] }}</p>
                        <p>Email: {{ $pdfData['user_mail'] }}</p>
                        <p>Estado: {{ $pdfData['user_state'] }}</p>
                        <p>Município: {{ $pdfData['user_city'] }}</p>
                        <p>Bairro: {{ $pdfData['user_neighborhood'] }}</p>
                        <p>CEP: {{ $pdfData['user_cep'] }}</p>
                        <p>Rua: {{ $pdfData['user_street'] }}</p>
                        <p>Número: {{ $pdfData['user_street_number'] }}</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p>Mountain View,</p>
                        <p>California,</p>
                        <p>United States</p>
                        <p>Contact: (650) 253-0000</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Método de Pagamento</th>
                <th class="w-50">Método de transporte</th>
            </tr>
            <tr>
                <td>{{$pdfData['payment_type']}}</td>
                <td>{{$pdfData['transport_type']}}</td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <thead>
                <tr>
                    <th class="w-50">Description</th>
                    <th class="w-50">Price</th>
                    <th class="w-50">Qty</th>
                    <th class="w-50">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pdfData['products'] as $product)
                <tr align="center">
                    <td>{{ $product['description'] }}</td>
                    <td>{{ $product['price'] }}</td>
                    <td>{{ $product['qty'] }}</td>
                    <td>{{ $product['grand_total'] }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="7">
                        <div class="total-part">
                            <div class="total-left w-85 float-left" align="right">
                                <p>Sub Total:</p>
                                <p>Frete:</p>
                                <p>Valor Total:</p>
                            </div>
                            <div class="total-right w-15 float-left text-bold" align="right">
                                <p>{{ $pdfData['product_sum_value'] }}</p>
                                <p>{{ $pdfData['logistic_value'] }}</p>
                                <p>{{ $pdfData['total_price'] }}</p>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</html>