Класс по получению данных по URL
Использование:

$data = CUrlClass::getData( ['url'=>'ya.ru'] );
//если надо передать заголовки
$data = CUrlClass::getData( ['url' => 'ya.ru', 'headers' => ['Connection' => 'keep-alive'] ] );
//указать запрос методом POST и передать данные( в виде строки 'para1=val1&para2=val2&..' или массива)
$data = CUrlClass::getData( ['url' => 'ya.ru', 'method'=> 'post', 'data'=> 'para1=val1&para2=val2' ] );