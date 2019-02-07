<?php

  class sms {
  
    private static $API_SESTOKINFO = 'http://192.168.8.1/api/webserver/SesTokInfo';
    private static $API_SMSLIST = 'http://192.168.8.1/api/sms/sms-list';
    private static $API_SENDSMS = 'http://192.168.8.1/api/sms/send-sms';
    
    private static function getsession() {
      return simplexml_load_string(file_get_contents(sms::$API_SESTOKINFO));
    }
    
    public static function send($number, $message) {
      $data ='<?xml version="1.0" encoding="UTF-8"?><request><Index>-1</Index><Phones><Phone>'.$number.'</Phone></Phones><Sca></Sca><Content>'.$message.'</Content><Length>'.strlen($message).'</Length><Reserved>1</Reserved><Date>'.date("Y-m-d H:i:s").'</Date></request>' ;
      return sms::query(sms::$API_SENDSMS, $data);
    }
    
    public static function receive($amount = 1) {
      $data = '<request><PageIndex>1</PageIndex><ReadCount>'.$amount.'</ReadCount><BoxType>1</BoxType><SortType>0</SortType><Ascending>0</Ascending><UnreadPreferred>1</UnreadPreferred></request>';
      return sms::query(sms::$API_SMSLIST, $data);
    }
    
    private static function query($url, $data) {
      $session = sms::getsession();
      
      $curl = curl_init($url);
      $headers = array(
        'Cookie:'.$session->SesInfo,
        '__RequestVerificationToken:'.$session->TokInfo,
        'Content-Type: text/xml; charset=UTF-8',
        'X-Requested-With: XMLHttpRequest'
      );

      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
      $content = curl_exec($curl);
      curl_close($curl);
      
      return json_decode(json_encode(simplexml_load_string($content)),TRUE);
    }
  }

?>
