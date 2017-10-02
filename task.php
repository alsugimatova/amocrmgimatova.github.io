<?php

$element_id=array('id'=>$lead['id']);

foreach ($element_id as $key => $value) {
	if($lead['closest_task']=true) {
		unset($element_id);
	}
}

$tasks['request']['tasks']['add']=array(
  #Привязываем к сделке
  array(
    'element_id'=>$element_id, 
    'element_type'=>2, #Показываем, что это - сделка, а не контакт
    'task_type'=>1, #Звонок
    'text'=>'Сделка без задачи',
    'responsible_user_id'=>109999,
    'complete_till'=>1375285346
  ),
  #Привязываем к сделке
  array(
    'element_id'=>3698752,
    'element_type'=>2,
    'task_type'=>3, #Письмо
    'text'=>'Сделка без задачи-2',
    'responsible_user_id'=>109999,
    'complete_till'=>1375198975
  )
); 

$subdomain='test'; #Наш аккаунт - поддомен
#Формируем ссылку для запроса
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/tasks/set';
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($tasks));
curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
 
$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
$code=(int)$code;
$errors=array(
  301=>'Moved permanently',
  400=>'Bad request',
  401=>'Unauthorized',
  403=>'Forbidden',
  404=>'Not found',
  500=>'Internal server error',
  502=>'Bad gateway',
  503=>'Service unavailable'
);
try
{
  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
  if($code!=200 && $code!=204)
    throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
}
catch(Exception $E)
{
  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
}
 
/**
 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 * нам придётся перевести ответ в формат, понятный PHP
 */
$Response=json_decode($out,true);
$Response=$Response['response']['tasks']['add'];
 
$output='ID добавленных задач:'.PHP_EOL;
foreach($Response as $v)
  if(is_array($v))
    $output.=$v['id'].PHP_EOL;
return $output;
?>