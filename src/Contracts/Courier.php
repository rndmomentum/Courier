<?php 
namespace Momentumplanet\Courier\Contracts;

use Momentumplanet\Courier\JNTExpress;
interface Courier 
{

    public function sender($sender_name,$sender_address,$sender_phone,$sender_zip);
    public function receiver($receiver_name,$receiver_address,$receiver_phone,$receiver_zip);
    public function parcel($parcel_description,$parcel_quantity,$parcel_weight,$parcel_height,$parcel_length,$parcel_width,$parcel_remarks,$parcel_value);
    public function tracking(Array $waybills);
    public function shipping_price();
    public function order();
    public function consignment_note();
}