<?php
namespace app\index\controller;
class Multi
{
    static private $client_id = "Yo66StWQEVGA";
    static private $redirect_uri = 'http://kd.myyqn.com/multis';
    static private $client_secret = "442a5205430541d789210a57c4827c83";
    static private $response_type = "code";
    static private $orderData = array(
        "recMobile"=>"13888685555",
        "recTel"=>"",
        "recName"=>"刘生",
        "recAddr"=>"安徽亳州涡阳县牌坊镇 陈兰大药房",
        "reccountry"=>"中国",
        "sendMobile"=>"18675586237",
        "sendTel"=>"",
        "sendName"=>"刘三石",
        "sendAddr"=>"广东深圳南山区科技南十二路金蝶软件园",
        "orderNum"=>"123456",
        "cargo"=>"",
        "kuaidiCom"=>"",
        "weight"=>"1",
        "valins"=>"",
        "collection"=>"",
        "payment"=>"",
        "comment"=>"",
        "recCompany"=>"",
        "sendCompany"=>"",
        "items"=>array(
            "itemName"=>"小米 MIX3",
            "itemSpec"=>"",
            "itemCount"=>"2",
            "itemUnit"=>"件",
            "itemOuterId"=>"",
        ),
    );
    public function callback(){
        trace($_REQUEST);
        $code = $_GET["code"];
       $accessToken =  self::accessToken($code);
        $openid = $accessToken['openid'];
        $expires_in = $accessToken['expires_in'];
        $refresh_token = $accessToken['refresh_token'];
        $access_token = $accessToken['access_token'];
        //$refresh_row = self::refreshToken($refresh_token);
        $order = self::send($access_token,self::$orderData);//导入订单
        //  dump($refresh_row);
        dump($order);
        //array(3) {
        //  ["status"] => int(200)
        //  ["message"] => string(7) "success"
        //  ["data"] => string(6) "123456"
        //}
    }
    #导入订单
    static function send($accessToken, $orderData) {
        if (is_array($orderData)) {
            $orderData = json_encode($orderData);
        }
        $timestamp = self::msectime();
        $data = array(
            "appid"=>self::$client_id,
            "access_token"=>$accessToken,
            "data"=>$orderData,
            "timestamp"=>$timestamp
        );
        $sign = self::generateSign($data);
        $data['sign'] = $sign;
        $res = self::https_request("https://b.kuaidi100.com/v6/open/api/send", $data);
        $res_arr = json_decode($res,true);
        return $res_arr;
    }
    #刷新accessToken
    static function refreshToken($refreshToken) {
        $timestamp = self::msectime();
        $data = array(
            "client_id"=>self::$client_id,
            "client_secret"=>self::$client_secret,
            "refresh_token"=>$refreshToken,
            "grant_type"=>"refresh_token",
            "timestamp"=>$timestamp
        );
        $sign = self::generateSign($data);
        $data['sign'] = $sign;
        $res = self::https_request("https://b.kuaidi100.com/open/oauth/refreshToken", $data);
        $res_arr = json_decode($res,true);
        return $res_arr;
    }
    //用授权得到的code换取accessToken
    static function accessToken($code) {
        $timestamp = self::msectime();
        $data = array(
            "client_id"=>self::$client_id,
            "client_secret"=>self::$client_secret,
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>self::$redirect_uri,
            "timestamp"=>$timestamp
        );
        $sign = self::generateSign($data);
        $data['sign'] = $sign;
        $res = self::https_request("https://b.kuaidi100.com/open/oauth/accessToken", $data);
        $res_arr = json_decode($res,true);
        return $res_arr;
    }
    /**
     * 用户授权
     */
    public function authorize()
    {
        $timestamp = self::msectime();
        $state = "test123";
        $arr=[
            'timestamp'=>$timestamp,
            'response_type'=>self::$response_type,
            'redirect_uri'=>self::$redirect_uri,
            'state'=>$state,
            'client_id'=>self::$client_id
        ];
        ksort($arr);
        //        $str = http_build_query($arr,'','');
        //$str = implode('',$arr);
        //$strsign = sprintf("%s%s%s",self::$client_secret,$str,self::$client_secret);
        $strsign = '';
        foreach($arr as $kk=>$vv){
            $strsign .=$kk . $vv;
        }
        $appSecret = self::$client_secret;
        $sign = $appSecret . $strsign . $appSecret;
        $sign = strtoupper(md5($sign));     //API 输入参数签名结果,md5后再转大写
        $url ='https://b.kuaidi100.com/open/oauth/authorize?response_type=code&client_id='.self::$client_id.'&redirect_uri='.urlencode(self::$redirect_uri).'&state'.$state.'&timestamp='.$timestamp.'&sign='.$sign;
        header('Location:'.$url);            //这里跳转到授权登录页面，登录成功后将跳转到回调地址
    }
    #生成授权地址
    static function authorize2($state = 'test123') {
        $timestamp = self::msectime();
        $data = array(
            "client_id"=>self::$client_id,
            "response_type"=>"code",
            "redirect_uri"=>self::$redirect_uri,
            "state"=>$state,
            "timestamp"=>$timestamp
        );
        $sign = self::generateSign($data);
        $url =  "https://b.kuaidi100.com/open/oauth/authorize?response_type=code&client_id=".self::$client_id."&redirect_uri=".urlencode(self::$redirect_uri)."&state=".$state."&timestamp=".$timestamp."&sign=".$sign;
        header('Location:'.$url);
    }
    #生成调用接口的sign
    static function generateSign($data) {
        $appSecret = self::$client_secret;
        ksort($data);
        $str = '';
        foreach($data AS $key=>$val) {
            $str.= $key.$val;
        }
        $str = $appSecret.$str.$appSecret;
        $sign = strtoupper(md5($str));
        return $sign;
    }
    #发送http请求
    static function https_request($url, $data = null) {
        $headers = array("Content-type: application/x-www-form-urlencoded", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
        $fields = (is_array($data)) ? http_build_query($data) : $data;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * @return 13位数的时间戳
     * #获取当前系统时间，毫秒
     */
    static function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    public function  curl_post($url, $postdate=null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdate);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        return $result;
    }

    /**
     * 根据（快递公司编码） 和（快递单号） 查询快递信息；
     */
    public function test()
    {
        $post_data = array();
        $post_data["customer"] = 'A12A216F12C5448E2041ED092DE9BDF6'; // customer ID
        $key= 'YVArkMtU2130';          // 实时接口管理 授权key
        $data['com']="huitongkuaidi";  //查询的快递公司的编码， 一律用小写字母
        $data['num']="51369920506643";  //查询的快递单号， 单号的最大长度是32个字符 358263398950
        $post_data["param"] = json_encode($data);  //num运单号
        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        $data = str_replace("\"",'"',$result );
        $data = json_decode($data,true);
        dump($data);
    }

}

//echo Multi::authorize2();
//echo Multi::send('',$orderData);