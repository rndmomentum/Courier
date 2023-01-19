<?php 
namespace Momentumplanet\Courier;

use Exception;
use Momentumplanet\Courier\Contracts\Courier;

class JNTExpress implements Courier
{
    public const PAYMENT_TYPE_PP_PM = 'PP_PM';
    
    public const EXPRESS_TYPE_STANDARD_DELIVERY = 'EZ';
    public const EXPRESS_TYPE_NEXT_DAY_DELIVERY = 'EX';
    public const EXPRESS_TYPE_CASHLESS_ON_DELIVERY = 'COD';
    public const EXPRESS_TYPE_INTERNATIONAL_SHIPPING = 'International';
    public const EXPRESS_TYPE_EXPRESS_DOC = 'Express Doc';
    public const EXPRESS_TYPE_JNTSUPER = 'J&T Super';
    public const EXPRESS_TYPE_MULTIPIECES_SHIPMENT = 'MPS';
    public const EXPRESS_TYPE_JNTCARGO_RAPID_GO = 'Rapid Go';
    public const EXPRESS_TYPE_JNTCARGO_STANDARD_GO = 'Standard Go';
    public const EXPRESS_TYPE_JNTINTERNATIONAL_LOGISTIC_INTERNATIONAL_PRODUCT = 'International Product';
    public const EXPRESS_TYPE_JNTINTERNATIONAL_LOGISTIC_WAREHOUSING_PRODUCT = 'Warehousing Product';

    public const GOOD_TYPE_PARCEL = 'PARCEL';
    public const GOOD_TYPE_DOCUMENT = 'DOCUMENT';
    public const SERVICE_TYPE_PICKUP = 1;
    public const SERVICE_TYPE_DROPOFF = 6;

    public const CHINESE = "1";
    public const ENGLISH = "2";

    public const QUERY_TYPE_WAYBILL = 1;
    public const QUERY_TYPE_CUSTOMER_ORDER_NUMBER = 2;

    public const CONSIGNMENT_NOTE_SIZE_A4 = 'A4';
    public const CONSIGNMENT_NOTE_SIZE_THERMAL_PRINTER = 'THERMAL PRINTER';

    private $sender_name;
    private $sender_address;
    private $sender_phone;
    private $sender_zip;

    private $receiver_name;
    private $receiver_address;
    private $receiver_phone;
    private $receiver_zip;

    private $parcel_description;
    private $parcel_quantity;
    private $parcel_weight;
    private $parcel_height;
    private $parcel_length;
    private $parcel_width;
    private $parcel_remarks;
    private $parcel_value; 

    private $pay_type;
    private $express_type;
    private $goods_type;
    private $service_type;
    private $send_start_time;
    private $send_end_time;

    private $account_username;
    private $account_api_key;
    private $account_cuscode;
    private $account_password;
    private $order_id;

    private $insurance;
    private $cod;

    private $query_type;
    private $language;
    private $billcode;
    private $consignment_note_size;


    public function order_id($order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }

    public function payment_type($pay_type)
    {
        $this->pay_type = $pay_type;
        return $this;
    }

    public function express_type($express_type)
    {
        $this->express_type = $express_type;
        return $this;
    }

    public function goods_type($goods_type)
    {
        $this->goods_type = $goods_type;
        return $this;
    }

    public function service_type($service_type)
    {
        $this->service_type = $service_type;
        return $this;
    }

    public function insurance($insurance)
    {
        $this->insurance = $insurance;
        return $this;    
    }

    public function cod($cod)
    {
        $this->cod = $cod;
        return $this;
    }

    public function query_type($query_type)
    {
        $this->query_type = $query_type;
        return $this;
    }

    public function language($language)
    {
        $this->language = $language;
        return $this;
    }

    public function billcode($billcode)
    {
        $this->billcode = $billcode;
        return $this;
    }

    public function consignment_note_size($consignment_note_size)
    {
        $this->consignment_note_size = $consignment_note_size;
        return $this;
    }

    public function sender($sender_nameasdasd,$sender_address,$sender_phone,$sender_zip)
    {
        $this->sender_name = $sender_nameasdasd;
        $this->sender_address = $sender_address;
        $this->sender_phone = $sender_phone;
        $this->sender_zip = $sender_zip;
        return $this;
    }

    public function receiver($receiver_name,$receiver_address,$receiver_phone,$receiver_zip)
    {
        $this->receiver_name = $receiver_name;
        $this->receiver_address = $receiver_address;
        $this->receiver_phone = $receiver_phone;
        $this->receiver_zip = $receiver_zip;
        return $this;
    }

    public function parcel($parcel_description,$parcel_quantity,$parcel_weight,$parcel_height,$parcel_length,$parcel_width,$parcel_remarks,$parcel_value)
    {
        $this->parcel_description = $parcel_description;
        $this->parcel_quantity = $parcel_quantity;
        $this->parcel_weight = $parcel_weight;
        $this->parcel_height = $parcel_height;
        $this->parcel_length = $parcel_length;
        $this->parcel_width = $parcel_width;
        $this->parcel_remarks = $parcel_remarks;
        $this->parcel_value = $parcel_value;
        return $this;
    }

    public function tracking(Array $waybills)
    {
        $url = $_ENV['JNT_EXPRESS_TRACKING_API_ENDPOINT']; //new test 
        $key = $_ENV['JNT_EXPRESS_TRACKING_API_PRIVATE_KEY']; //JTS PARTNER ID

        if(empty($this->query_type))
        {
            throw new Exception("Query type is not specified.", 1);
            
        }

        if(empty($this->language))
        {
            throw new Exception("Language is not specified.", 1);
            
        }

        $logistic_interface = array 
        (
            'queryType'         => $this->query_type,
            'language'          => $this->language,
            'queryCodes'        => $waybills
        );

        $json_data = json_encode((object)$logistic_interface);
        $a = base64_encode(md5($json_data.$key));

        $post = array
        ( 
            'logistics_interface'   => $json_data, 
            'data_digest'           => $a, 
            'msg_type'              => 'TRACK', 
            'eccompanyid'           => $_ENV['JNT_EXPRESS_ACCOUNT_USERNAME'] 
        );

        $s = curl_init(); 
        curl_setopt($s, CURLOPT_URL, $url); 
        curl_setopt($s, CURLOPT_POST, 1); 
        curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($s, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form- urlencoded')); 
        curl_setopt($s,CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($s, CURLOPT_HEADER, 0); 
        curl_setopt($s, CURLOPT_RETURNTRANSFER, TRUE); 
        $r = curl_exec($s); 
        curl_close($s);

        return $r;
    }

    public function shipping_price()
    {
        return null;
    }

    public function order()
    {
        if(empty($this->sender_name) || empty($this->sender_address) || empty($this->sender_phone) || empty($this->sender_zip))
        {
            throw new Exception("Sender details empty.", 1);
        }

        if(empty($this->receiver_name) || empty($this->receiver_address) || empty($this->receiver_phone) || empty($this->receiver_zip))
        {
            throw new Exception("Receiver details empty.", 1);
        }

        if(empty($this->parcel_quantity) || empty($this->parcel_weight) || empty($this->parcel_description) || empty($this->parcel_remarks) || empty($this->parcel_value))
        {
            throw new Exception("Parcel details empty.", 1);
        }

        if(empty($this->pay_type))
        {
            throw new Exception("Payment type is not specified.", 1);
        }

        if(empty($this->goods_type))
        {
            throw new Exception("Goods Type is not specified.", 1);
        }

        if(empty($this->service_type))
        {
            throw new Exception("Service Type is not specified.", 1);
        }

        $data = array
        (
            'username'          => $_ENV['JNT_EXPRESS_ACCOUNT_USERNAME'],
            'api_key'           => $_ENV['JNT_EXPRESS_ACCOUNT_API_KEY'],
            'cuscode'           => $_ENV['JNT_EXPRESS_ACCOUNT_CUSCODE'],
            'password'          => $_ENV['JNT_EXPRESS_ACCOUNT_PASSWORD'],
            'orderid'           => $this->order_id,
            'shipper_contact'   => $this->sender_name,
            'shipper_name'      => $this->sender_name,
            'shipper_phone'     => $this->sender_phone,
            'shipper_addr'      => $this->sender_address,
            'sender_zip'        => $this->sender_zip,
            'receiver_name'     => $this->receiver_name,
            'receiver_addr'     => $this->receiver_address,
            'receiver_phone'    => $this->receiver_phone,
            'receiver_zip'      => $this->receiver_zip,
            'payType'           => $this->pay_type,
            'goodsType'         => $this->goods_type,
            'serviceType'       => $this->service_type,
        );

        if(!empty($this->express_type))
            $data['expressType'] =  $this->express_type;
        
        if(!empty($this->send_start_time))
            $data['sendstarttime']  = $this->send_start_time;
        
        if(!empty($this->send_end_time))
            $data['sendendtime'] = $this->send_end_time;

        if(!empty($this->parcel_height))
            $data['height'] = $this->parcel_height;

        if(!empty($this->parcel_length))
            $data['length'] = $this->parcel_length;
        
        if(!empty($this->parcel_width))
            $data['width'] = $this->parcel_width;

        if(!empty($this->parcel_value))
            $data['goodsvalue'] = $this->parcel_value;
        
        if(!empty($this->insurance))
            $data['offerFeeFlag'] = $this->insurance;
        
        if(!empty($this->cod))
            $data['COD'] = $this->cod;

        $url = $_ENV['JNT_EXPRESS_ORDER_API_ENDPOINT'];
        $private_key = $_ENV['JNT_EXPRESS_ORDER_API_PRIVATE_KEY'];

        $data_json = json_encode(array('detail'=>array($data)));
        $data_request = array(
            'data_param' =>  $data_json,
            'data_sign'  =>  base64_encode(md5($data_json.$private_key))
        );

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_POST, 1);
        curl_setopt($s,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query($data_request));
        curl_setopt($s,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($s, CURLOPT_HEADER, 0);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, TRUE);

        $r = curl_exec($s);
        echo($r);
        curl_close($s);

        return $r;
    }

    public function consignment_note()
    {

        if($this->consignment_note_size == $this::CONSIGNMENT_NOTE_SIZE_A4)
        {
            $url = $_ENV['JNT_EXPRESS_CONSIGNMENT_NOTE_API_A4_ENDPOINT'];
        }else{
            $url = $_ENV['JNT_EXPRESS_CONSIGNMENT_NOTE_API_THERMAL_ENDPOINT'];
        }
        
        $data = array
        (
            'billcode'      => $this->billcode,
            'account'       => $_ENV['JNT_EXPRESS_ACCOUNT_USERNAME'],
            'password'      => $_ENV['JNT_EXPRESS_ACCOUNT_PASSWORD'],
            'customercode'  => $_ENV['JNT_EXPRESS_ACCOUNT_CUSCODE'],
        );

        $t = array
        (
            'logistics_interface' => json_encode($data), 
            'msg_type' => '1', 
            'data_digest' => md5($this->billcode)
        );

        $s = curl_init(); 
        curl_setopt($s,CURLOPT_URL,$url); 
        curl_setopt($s,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 
        curl_setopt($s,CURLOPT_POSTFIELDS,http_build_query($t)); 
        header('Content-type: application/pdf');
        $r = curl_exec($s); 
        curl_close($s);

        return $r;
    }
}