<?php
/**
 * 
 */
require_once('constants.php');
class Rest 
{
	protected $request;
	protected $serviceName;
	protected $param;

	public function __construct()
	{
		if ($_SERVER['REQUEST_METHOD']!=='POST') {
			$this->throwError(REQUEST_METHOD_NOT_VALID,'Request Method Is Not Vailid ');
		}
		$handler=fopen('php://input', 'r');
		$this->request=stream_get_contents($handler);
		$this->validateRequest($this->request);

	}
	public function validateRequest($request)
	{
		if ($_SERVER['CONTENT_TYPE']!=='application/json') {
			$this->throwError(REQUEST_CONTENTTYPE_NOT_VALID,"REQUEST CONTENTTYPE NOT VALID");
		}
		$data=json_decode($this->request,true);
		if(!isset($data['name'])|| $data['name']=="") {
			$this->throwError(API_NAME_REQUIRED,"Api Name Is Requide");
		}
		$this->serviceName=$data['name'];
		if(!isset($data['param'])|| $data['param']=="") {
			$this->throwError(API_PARAM_REQUIRED,"Api Parameter Is Requide");
		}
		$this->param=$data['param'];
	}
	public function processApi()
	{
		$api=new API;
		$rMethod=new reflectionMethod('API',$this->serviceName);
		if (!method_exists($api,$this->serviceName)) {
			$this->throwError(API_DOST_NOT_EXIST,'Api Does Not Exist');
		}
		$rMethod->invoke($api);
	}
	public function validateParameter($fieldName,$value,$dataType,$required=true)
	{
		if($required==true && empty($value)==true){
			$this->throwError(VALIDATE_PARAMETER_REQUIRED,$fieldName.' Parameter Is Required');
		}
		switch ($dataType) {
			case BOOLEAN:
				if(!is_bool($value)){
					$this->throwError(VALIDATE_PARAMETER_DATATYPE,"Data Type Is Not Vailid For".$fieldName);
				}
				break;
			case INTEGER:
				if(!is_int($value)){
					$this->throwError(VALIDATE_PARAMETER_DATATYPE,"Data Type Is Not Vailid For".$fieldName);
				}
				break;
			case STRING:
				if(!is_string($value)){
					$this->throwError(VALIDATE_PARAMETER_DATATYPE,"Data Type Is Not Vailid For".$fieldName);
				}
				break;
			default:
				# code...
				break;
		}
		return $value;
	}
	public function throwError($code,$msg)
	{
		header('content-type:application/json');
		$erreor=json_encode(["error"=>['status'=>$code,'message'=>$msg]]);
		echo $erreor;exit();
		
	}
	public function returnResponse($code,$data)
	{
		header("content-type:aplication/json");
		$resopnce=json_encode(['responce'=>['Data'=>$data,"status"=>$code]]);
		echo $resopnce;exit;
	}
}
?>