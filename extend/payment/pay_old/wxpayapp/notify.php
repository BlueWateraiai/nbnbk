<?php
namespace payment\wxpay_app;
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "WxPayApi.php";
require_once 'WxPayNotify.php';
require_once 'log.php';



class PayNotifyCallBack extends WxPayNotify
{



	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		$map['order_sn'] = $data['out_trade_no'];//商户订单号，也就是优加系统中的充值流水号
		
		if ($data['result_code'] == 'SUCCESS') {
			//做充值成功处理
		}else if ($data['result_code'] == 'FAIL') 
		{
			//做充值失败处理
			
		}
		
		
                
                }
}


