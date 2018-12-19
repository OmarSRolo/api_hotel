<?php

class Countries extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json_result(true, '', $this->country_model->order_by('name_'.$this->config->item('lang'))->get_all());
    }

    public function cities($country)
    {
        $this->json_result(true, '', $this->city_model->order_by('name')->get_many_by('country_code', $country));
    }

    public function timezones()
    {
       $this->load->helper('date');
       $this->json_result(true,'',array(
         array("zone"=>"UM12","location"=>'(UTC - 12:00) Baker/Howland Island'),
         array("zone"=>"UM11","location"=>"(UTC - 11:00) Samoa Time Zone, Niue"),
         array("zone"=>"UM10","location"=>"(UTC - 10:00) Hawaii-Aleutian Standard Time, Cook Islands"),
         array("zone"=>"UM95","location"=>"(UTC - 09:30) Marquesas Islands"),
         array("zone"=>"UM9","location"=>"(UTC - 09:00) Alaska Standard Time, Gambier Islands"),
         array("zone"=>"UM8","location"=>"(UTC - 08:00) Pacific Standard Time, Clipperton Island"),
         array("zone"=>"UM7","location"=>"(UTC - 07:00) Mountain Standard Time"),
         array("zone"=>"UM6","location"=>"(UTC - 06:00) Central Standard Time"),
         array("zone"=>"UM5","location"=>"(UTC - 05:00) Eastern Standard Time, Western Caribbean"),
         array("zone"=>"UM45","location"=>"(UTC - 04:30) Venezuelan Standard Time"),
         array("zone"=>"UM4","location"=>"(UTC - 04:00) Atlantic Standard Time, Eastern Caribbean"),
         array("zone"=>"UM35","location"=>"(UTC - 03:30) Newfoundland Standard Time"),
         array("zone"=>"UM3","location"=>"(UTC - 03:00) Argentina, Brazil, French Guiana, Uruguay"),
         array("zone"=>"UM2","location"=>"(UTC - 02:00) South Georgia/South Sandwich Islands"),
         array("zone"=>"UM1","location"=>"(UTC -1:00) Azores, Cape Verde Islands"),
         array("zone"=>"UTC","location"=>"(UTC) Greenwich Mean Time, Western European Time"),
         array("zone"=>"UP1","location"=>"(UTC +1:00) Central European Time, West Africa Time"),
         array("zone"=>"UP2","location"=>"(UTC +2:00) Central Africa Time, Eastern European Time"),
         array("zone"=>"UP3","location"=>"(UTC +3:00) Moscow Time, East Africa Time"),
         array("zone"=>"UP35","location"=>"(UTC +3:30) Iran Standard Time"),
         array("zone"=>"UP4","location"=>"(UTC +4:00) Azerbaijan Standard Time, Samara Time"),
         array("zone"=>"UP45","location"=>"(UTC +4:30) Afghanistan"),
         array("zone"=>"UP5","location"=>"(UTC +5:00) Pakistan Standard Time, Yekaterinburg Time"),
         array("zone"=>"UP55","location"=>"(UTC +5:30) Indian Standard Time, Sri Lanka Time"),

         array("zone"=>"UP575","location"=>"(UTC +5:45) Nepal Time"),
         array("zone"=>"UP6","location"=>"(UTC +6:00) Bangladesh Standard Time, Bhutan Time, Omsk Time"),
         array("zone"=>"UP65","location"=>"(UTC +6:30) Cocos Islands, Myanmar"),
         array("zone"=>"UP7","location"=>"(UTC +7:00) Krasnoyarsk Time, Cambodia, Laos, Thailand, Vietnam"),
         array("zone"=>"UP8","location"=>"(UTC +8:00) Australian Western Standard Time, Beijing Time"),
         array("zone"=>"UP875","location"=>"(UTC +8:45) Australian Central Western Standard Time"),
         array("zone"=>"UP9","location"=>"(UTC +9:00) Japan Standard Time, Korea Standard Time, Yakutsk"),
         array("zone"=>"UP95","location"=>"(UTC +9:30) Australian Central Standard Time"),
         array("zone"=>"UP10","location"=>"(UTC +10:00) Australian Eastern Standard Time, Vladivostok Time"),
         array("zone"=>"UP105","location"=>"(UTC +10:30) Lord Howe Island"),
         array("zone"=>"UP11","location"=>"(UTC +11:00) Srednekolymsk Time, Solomon Islands, Vanuatu"),
         array("zone"=>"UP115","location"=>"(UTC +11:30) Norfolk Island"),
         array("zone"=>"UP12","location"=>"(UTC +12:00) Fiji, Gilbert Islands, Kamchatka, New Zealand"),
         array("zone"=>"UP1275","location"=>"(UTC +12:45) Chatham Islands Standard Time"),
         array("zone"=>"UP13","location"=>"(UTC +13:00) Phoenix Islands Time, Tonga"),
         array("zone"=>"UP14","location"=>"(UTC +14:00) Line Islands")

       ));
    }
}
