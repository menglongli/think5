<?php
namespace app\index\controller;
class Multi
{
    public function test()
    {
        $post_data = array();
        $post_data["customer"] = 'A12A216F12C5448E2041ED092DE9BDF6';
        $key= 'YVArkMtU2130';
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
    /**
     * 用户授权
     */
    public function authorize()
    {
        $client_secret = "62e85e15e6c845b59ff7c6eca48d2846";
        $state1 = "kuaidi";
        $response_type = "code";
        $timestamp = $this->msectime(); //13位
        $redirect_uri = "http://tplus.yqnmy.com/express/callback";
//        $redirect_uri = "http://middle.yqnmy.com/express/callback";
        $appuid = "13271582882";
        $client_id= 'UnCsY9l6Zl4D';
        $signstr="$client_secret.'appuid'.$appuid.'client_id'.$client_id.'redirect_uri'.$redirect_uri.'response_type'.$response_type.'state'.$state1.'timestamp'.$timestamp.$client_secret";
        $sign=strtoupper(MD5($signstr));//API 输入参数签名结果,md5后再转大写
        $redirect_uri=urlencode($redirect_uri);//url编码

        $url2 = 'https://b.kuaidi100.com/open/oauth/authorize?response_type='.$response_type.'&client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&timestamp='.$timestamp.'&sign='.$sign;
        echo $url2;

//        header('Location:'.$url2);//这里跳转到授权登录页面，登录成功后将跳转到回调地址
    }

    function msectime() {
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


}