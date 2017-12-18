<?php
	namespace GoodWheels;
	
	interface ICUrlClass{
		public static function getData();
	}
	
	class CUrlException extends \Exception{}
	class CUrlClassException extends \Exception{}
	
	/*
	*класс по получению данных из стороннего источника по url ссылке - в случае неудачи соединяется до 10 раз
	*
	*@property cURL|FALSE $curl
	*@property integer $counter
	*/
	class CUrlClass //implements ICUrlClass
	{
		protected static $curl=false;
		protected static $counter=0;
		/*
		*@param array $arr [ 'url'=>'url', 'headers'=>['Connection' => 'keep-alive', 'привет' => 'я умный парсер!'], 'method'=> 'post' | 'get' , 'data'=> NULL | string ]
		*@return string
		*/
		public static function getData(array $arr){
			try{
				if(!isset($arr['url'])) throw new CUrlException('Не передан параметр url');
				if (!filter_var($arr['url'], FILTER_VALIDATE_URL)) throw new CUrlException('Не корректный url: '.$arr['url']);
				++self::$counter;
				try{
					if(!self::$curl) self::$curl=curl_init( $arr['url'] );
				}
				catch(\Exception $e){
					throw new CUrlException('На сервере не установлена библиотека CUrl, либо внутренняя ошибка: '.$e->getMessage());
				}
				if(!self::$curl) throw new CUrlException('Не удалось сформировать дескриптор CUrl');
				//----------формируем загловки----------------------
				if(isset( $arr['headers'] ) ) curl_setopt( self::$curl, CURLOPT_HTTPHEADER,$arr['headers'] ); 
				if(isset( $arr['method'] ) ){
					if( $arr['method'] == 'post' ) curl_setopt( self::$curl, CURLOPT_CUSTOMREQUEST, "POST");
				}
				if(isset( $arr['data'] ) ) curl_setopt( self::$curl, CURLOPT_POSTFIELDS, $arr['data'] );
				//--------------------------------------------------
				//вместо вывода в браузер - отдаем строковое значение
				curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
				//задаем тайминг ожидания ответа от сервера
				curl_setopt(self::$curl, CURLOPT_CONNECTTIMEOUT, 10);
				//обходим редирект
				curl_setopt(self::$curl,CURLOPT_FOLLOWLOCATION, true ); 
				//устанавливаем конечное число обходов
				curl_setopt(self::$curl,CURLOPT_MAXREDIRS, 50 ); 
				
				//Инициируем запрос и сохраняем ответ в переменную
				$out=curl_exec(self::$curl); 
				//Получим HTTP-код ответа сервера
				$code=curl_getinfo(self::$curl,CURLINFO_HTTP_CODE); 
				$code=(int)$code;
				//если удаленный сервер не отвечает делаем 9 попыток
				if($code!=200 && $code!=204 && self::$counter<10 ){
					return CUrlClass::getData($arr);
					//return $code;
				}
				elseif($code!=200 && $code!=204 && self::$counter==10 ){
					throw new CUrlException('К серверу было сделано 9 запросов: ни на один из них он не ответил. Код ответа сервера: '.$code);
				}
				else{
					if(self::$curl!==false) curl_close(self::$curl);
					self::$curl=false;
					self::$counter=0;
					return $out;
				}
			}
			catch(CUrlException $e){
				//return $e->getMessage();
				throw new CUrlClassException( $e->getMessage() );
			}
			finally {
				if(self::$curl!==false) curl_close(self::$curl);
				self::$curl=false;
				self::$counter=0;
			}
		}
	}