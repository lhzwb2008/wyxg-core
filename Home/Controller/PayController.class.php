<?php
namespace Home\Controller;

use Think\Controller;

class PayController extends Controller
{

    public function sendOrder()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $con = curl_init($url);
        $data['appid'] = 'wxab1c2b71c7ff40c6';
        $data['attach'] = 'woyaoxiege';
        $data['mch_id'] = '1370334402';
        $data['nonce_str'] = md5(rand());
        $data['body'] = I('get.description');
        $data['out_trade_no'] = md5(uniqid());
        $out_trade_no = $data['out_trade_no'];
        $data['total_fee'] = I('get.price');
        if($data['total_fee']>100){
            $data['total_fee']==100;
        }
        $data['spbill_create_ip'] = get_client_ip();
        $data['notify_url'] = 'https://1.117.109.129/core/home/pay/notify';
        $data['trade_type'] = 'APP';
        ksort($data);
        $stringSignTemp = '';
        foreach ($data as $key => $value) {     
            $stringSignTemp = $stringSignTemp . $key . '=' . $value . '&';
        }
        $stringSignTemp = $stringSignTemp . 'key=c6628908d68feb78caede78d6dadff31';
        $data['sign'] = strtoupper(md5($stringSignTemp));
        $doc = $this->arrayToXml($data);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, $doc);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        $response = $this->xmlToArray(curl_exec($con));
        if($response['return_code']=='SUCCESS'&$response['result_code']=='SUCCESS'){
            $data=array();
            $data['appid'] = 'wxab1c2b71c7ff40c6';
            $data['partnerid'] = '1370334402';
            $data['prepayid'] = $response['prepay_id'];
            $data['package'] = 'Sign=WXPay';
            $data['noncestr'] = md5(rand());
            $data['timestamp'] = date('U');
            ksort($data);
            $stringSignTemp = '';
            foreach ($data as $key => $value) {
                $stringSignTemp = $stringSignTemp . $key . '=' . $value . '&';
            }
            $stringSignTemp = $stringSignTemp . 'key=c6628908d68feb78caede78d6dadff31';
            $data['sign'] = strtoupper(md5($stringSignTemp));
            $data['out_trade_no'] = $out_trade_no;
            $this->ajaxReturn($data, 'JSON');
        }
    }
    
    public function notify()
    {
        $xml = file_get_contents("php://input");
        $xml_array = $this->xmlToArray($xml);
        if($xml_array['return_code']=='SUCCESS'){
            $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
            $con = curl_init($url);
            $query['appid'] = 'wxab1c2b71c7ff40c6';
            $query['mch_id'] = '1370334402';
            $query['out_trade_no'] = $xml_array['out_trade_no'];;
            $query['nonce_str'] = md5(rand());
            ksort($query);
            $stringSignTemp = '';
            foreach ($query as $key => $value) {
                $stringSignTemp = $stringSignTemp . $key . '=' . $value . '&';
            }
            $stringSignTemp = $stringSignTemp . 'key=c6628908d68feb78caede78d6dadff31';
            $query['sign'] = strtoupper(md5($stringSignTemp));
            $doc = $this->arrayToXml($query);
            curl_setopt($con, CURLOPT_HEADER, false);
            curl_setopt($con, CURLOPT_POSTFIELDS, $doc);
            curl_setopt($con, CURLOPT_POST, true);
            curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
            $response = $this->xmlToArray(curl_exec($con));  
            if($response['return_code']=='SUCCESS'&$response['result_code']=='SUCCESS'){
                $Order = M('Order');
                $data['openid'] = $response['openid'];
                $data['bank_type'] = $response['bank_type'];
                $data['total_fee'] = $response['total_fee'];
                $data['transaction_id'] = $response['transaction_id'];
                $data['out_trade_no'] = $response['out_trade_no'];
                $data['time_end'] = $response['time_end'];
                $data['trade_state'] = $response['trade_state'];
                $id = $Order->data($data)->add();
                if($id){
                    $data['return_code'] = 'SUCCESS';
                    $data['return_msg'] = 'ok';
                    $doc = $this->arrayToXml($data);
                    echo $doc;
                }
            }
        }
    }
    
    public function queryInsertOrder()
    {
        $user_id = I('get.user_id');
        $pay_user_id = I('get.pay_user_id',17752);
        $song_id = I('get.song_id');
        $gift_type = I('get.gift_type',1);
        $data['out_trade_no'] = I('out_trade_no');
        $Order = M('Order');
        $order = $Order->where($data)->find();
        if($order===false){//查询出错
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else if(empty($order)){//无结果
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else{//正常返回
            //礼物入库
            $Gift= M('Gift');
            $gift_data['gift_type'] = $gift_type; 
            $gift_data['user_id'] = $pay_user_id;
            $gift_data['song_id'] = $song_id;
            $gift_data['money'] = $order['total_fee'];
            $gift_data['create_time'] = date("Y-m-d H:i:s",time());
            $gift_id = $Gift->data($gift_data)->add();
            
            $Order-> where("id=".$order['id'])->setField('user_id',$user_id);
            $Order-> where("id=".$order['id'])->setField('pay_user_id',$pay_user_id);
            $Order-> where("id=".$order['id'])->setField('song_id',$song_id);
            $Order-> where("id=".$order['id'])->setField('gift_id',$gift_id);
            $Money = M('Money');
            $money = $Money-> where("user_id=".$user_id)->find();
            if(empty($money)){
                $user_data['user_id'] = $user_id;
                $user_data['money'] = $order['total_fee'];
                $user_data['modify_time'] = date("Y-m-d H:i:s",time());
                $Money->data($user_data)->add();
            }else{
                if($order['trade_state']=='SUCCESS'){
                    $Money-> where("id=".$money['id'])->setField('money',$money['money']+$order['total_fee']);
                }
            }
            $order['status'] = 0;
            $this->ajaxReturn($order, 'JSON');
        }
    }
    
    public function getUserMoney(){
        $user_id = I('get.user_id');
        $Money = M('Money');
        $data['user_id'] = $user_id;
        $money = $Money->where($data)->find();
        if($money===false){//查询出错
            $this->ajaxReturn(array('status'=>-1), 'JSON');
        }else if(empty($money)){//无结果
            $user_data['user_id'] = $user_id;
            $user_data['money'] = 0;
            $user_data['modify_time'] = date("Y-m-d H:i:s",time());
            $id = $Money->data($user_data)->add();
            if($id){
                $User=M('User');
                $user = $User->where('id='.$user_id)->find();
                if($user){
                    $user_data['id'] = $id;
                    $user_data['status'] = 0;
                    $this->ajaxReturn($user_data, 'JSON');
                }else{
                    $this->ajaxReturn(array('status'=>-1), 'JSON');
                }
            }
        }else{//正常返回
            $money['status'] = 0;
            $this->ajaxReturn($money, 'JSON');
        }
    }

    function arrayToXml($arr)
    {
        $xml = "<?xml version='1.0' encoding='UTF-8'?><xml>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    
    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
    
    
}

?>