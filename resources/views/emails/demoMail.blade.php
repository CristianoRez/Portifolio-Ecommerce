<!DOCTYPE html>
<html>
<head>
    <title>ItsolutionStuff.com</title>
</head>
<body>
    <h1>{{ $mailData['title'] }}</h1>
    <p>{{ $mailData['body'] }}</p>
    
    <img src="{{ asset($mailData['img']) }}" />
    <p>Nome do produto:{{ $mailData['productName'] }}</p>
    <p>Quantidade:{{ $mailData['quantity'] }}</p>
    <p>Preço:{{ $mailData['price'] }}</p>
     
    <p>Volte sempre!</p>
</body>
</html>