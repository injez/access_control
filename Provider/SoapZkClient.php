<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SoapClientNG
 *
 * @author ahabchi
 */
class SoapZkClient extends \SoapClient {

    private $filter_enabled = false;

    public function getFilter_enabled() {
        return $this->filter_enable;
    }

    public function setFilter_enabled($filter_enabled) {
        $this->filter_enabled = $filter_enabled;
    }
    
    private $allowChar = array("\r", "\n", " ");

    public function correctXml($data) {
        for ($i = 0; $i < strlen($data); $i++) {
            if ($data[$i] == ">") {
                for ($j = $i + 1; $j < strlen($data); $j++) {
                    if (($data[$j] == "<" && $data[$j + 1] == "/") || ($data[$j] == "<" && ($data[$j - 1] == ">" || $data[$j - 1] == "\n"))) {
                        break;
                    } else if (!ctype_alnum($data[$j]) && !in_array($data[$j], $this->allowChar)) {
                        $data[$j] = "*";
                    }
                }
            }
        }
        return $data;
    }

    /*private function correctXmlWithPreg($data) {
        $name_pattern = "@<Name>(.*)</Name>@";
        preg_match_all($name_pattern, $data, $out);
        for ($i = 0; $i < count($out[1]); $i++) {
            $data = str_replace("<Name>" . $out[1][$i] . "</Name>", "<Name>asd</Name>", $data);
        }
        return $data;
    }*/

    public function __doRequest($req, $location, $action, $version = SOAP_1_1) {

        $original_response = (parent::__doRequest($req, $location, $action, $version));
        if ($this->filter_enabled) {
            return str_replace("*", "", $this->correctXml($original_response));
        }
        return $original_response;
    }

}
