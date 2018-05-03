<?php

require 'SoapZkClient.php';
require 'UdpZkClient.php';


class ZkLib {

    private $soapClient;
    private $udpClient;

   /* static private $soap_commands_available = [
      'get_date' => '<GetDate><ArgComKey>%com_key%</ArgComKey></GetDate>',
      'get_att_log' => '<GetAttLog><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></GetAttLog>',
      'get_user_info' => '<GetUserInfo><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></GetUserInfo>',
      'get_all_user_info' => '<GetAllUserInfo><ArgComKey>%com_key%</ArgComKey></GetAllUserInfo>',
      'get_user_template' => '<GetUserTemplate><ArgComKey>0</ArgComKey><Arg><PIN>%pin%</PIN><FingerID>%finger_id%</FingerID></Arg></GetUserTemplate>',
      'get_combination' => '<GetCombination><ArgComKey>%com_key%</ArgComKey></GetCombination>',
      'get_option' => '<GetOption><ArgComKey>%com_key%</ArgComKey><Arg><Name>%option_name%</Name></Arg></GetOption>',
      'set_user_info' => [ '<DeleteUser><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></DeleteUser>', '<SetUserInfo><ArgComKey>%com_key%</ArgComKey><Arg><Name>%name%</Name><Password>%password%</Password><Group>%group%</Group><Privilege>%privilege%</Privilege><Card>%card%</Card><PIN2>%pin%</PIN2><TZ1>%tz1%</TZ1><TZ2>%tz2%</TZ2><TZ3>%tz3%</TZ3></Arg></SetUserInfo>'],
      'set_user_template' => '<SetUserTemplate><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN><FingerID>%finger_id%</FingerID><Size>%size%</Size><Valid>%valid%</Valid><Template>%template%</Template></Arg></SetUserTemplate>',
      'delete_user' => '<DeleteUser><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></DeleteUser>',
      'delete_template' => '<DeleteTemplate><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></DeleteTemplate>',
      'delete_user_password' => '<ClearUserPassword><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></ClearUserPassword>',
      'delete_data' => '<ClearData><ArgComKey>%com_key%</ArgComKey><Arg><Value>%value%</Value></Arg></ClearData>',
      'refresh_db' => '<RefreshDB><ArgComKey>%com_key%</ArgComKey></RefreshDB>',
      ]; 
*/
    public function __construct($host) {
        $soap_options = array(
            'location' => "http://" . $host . "/iWsService",
            'uri' => 'http://www.zksoftware/Service/message/',
            'encoding' => 'UTF-8',
            'exceptions' => false,
            'trace' => true
        );

        $udp_option = array(
            'connection_timeout' => 5,
            'port' => 4370,
            'host' => $host,
            'encoding' => 'utf-8',
        );
        $this->soapClient = new SoapZkClient(null, $soap_options);
        $this->udpClient = new UdpZkClient($udp_option);
    }
    
    public function clearData($value){
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $param->Arg = new \stdClass();
        $param->Arg->Value = $value;
        $objectresult = $this->soapClient->ClearData($param);
        return($objectresult);
    }
    
    
    public function getDate() {
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $objectresult = $this->soapClient->GetDate($param);
        return($objectresult);
    }

    public function getUser($pin = null) {
        $this->soapClient->setFilter_enabled(true);
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $param->Arg = new \stdClass();
        $param->Arg->PIN = $pin;
        $objectresult = $this->soapClient->GetUserInfo($param);
        $this->soapClient->setFilter_enabled(false);
        return($objectresult);
    }

    public function getAttLog($pin = null) {
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $param->Arg = new \stdClass();
        $param->Arg->PIN = $pin;
        $objectresult = $this->soapClient->GetAttLog($param);
        return($objectresult);
    }

    //<SetUserInfo><ArgComKey>%com_key%</ArgComKey><Arg><Name>%name%</Name><Password>%password%</Password><Group>%group%</Group><Privilege>%privilege%</Privilege><Card>%card%</Card><PIN2>%pin%</PIN2><TZ1>%tz1%</TZ1><TZ2>%tz2%</TZ2><TZ3>%tz3%</TZ3></Arg></SetUserInfo>
    public function setUser($user) {
        $this->deleteUser($user->PIN2);
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $param->Arg = new \stdClass();
        $param->Arg->PIN2 = $user->PIN2;
        $param->Arg->Name = $user->Name;
        $param->Arg->Password = $user->Password;
        $param->Arg->Privilege = $user->Privilege;
        $param->Arg->Group = $user->Group;
        $param->Arg->Card = $user->Card;
        $param->Arg->TZ1 = $user->TZ1;
        $param->Arg->TZ2 = $user->TZ2;
        $param->Arg->TZ3 = $user->TZ3;
        $objectresult = $this->soapClient->SetUserInfo($param);
        $this->refreshDB();
        return($objectresult);
    }

    //<DeleteUser><ArgComKey>%com_key%</ArgComKey><Arg><PIN>%pin%</PIN></Arg></DeleteUser>
    public function deleteUser($pin) {
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $param->Arg = new \stdClass();
        $param->Arg->PIN = $pin;
        $objectresult = $this->soapClient->DeleteUser($param);
        $this->refreshDB();
        return($objectresult);
    }

    public function refreshDB() {
        $param = new \stdClass();
        $param->ArgComKey = 0;
        $this->soapClient->RefreshDB($param);
    }

    //'<GetOption><ArgComKey>%com_key%</ArgComKey><Arg><Name>%option_name%</Name></Arg></GetOption>'
    public function getOption() {
        $param = new \stdClass();
        $param->ArgComKey = 0;
        // $param->Arg = new \stdClass();
        // $param->Arg->Name = "name";
        $objectresult = $this->soapClient->GetOption($param);
        print_r($objectresult);
    }

    public function getDeviceName() {
        return($this->udpClient->get_device_name());
    }
    
    
     public function disable() {
        return($this->udpClient->disable());
    }
    
    public function restart() {
        return($this->udpClient->restart());
    }
    
    public function unlock() {
        return($this->udpClient->unlock());
    }
    
    public function enable() {
        return($this->udpClient->enable());
    }



}
