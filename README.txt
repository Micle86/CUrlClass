����� �� ��������� ������ �� URL
�������������:

$data = CUrlClass::getData( ['url'=>'ya.ru'] );
//���� ���� �������� ���������
$data = CUrlClass::getData( ['url' => 'ya.ru', 'headers' => ['Connection' => 'keep-alive'] ] );
//������� ������ ������� POST � �������� ������( � ���� ������ 'para1=val1&para2=val2&..' ��� �������)
$data = CUrlClass::getData( ['url' => 'ya.ru', 'method'=> 'post', 'data'=> 'para1=val1&para2=val2' ] );