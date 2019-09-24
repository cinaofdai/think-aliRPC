<?php
/**
 * Created by dh2y.
 * Blog: http://blog.csdn.net/sinat_22878395
 * Date: 2018/4/26 0026 16:26
 * for: 阿里巴巴实人认证
 */

namespace dh2y\aliRPC;


use think\facade\Config;



class AliRPC
{
    /**
     * 请求url
     * @var string
     * @access protected
     */
    protected $baseUrl = 'https://cloudauth.aliyuncs.com/?';

    protected $config = [];


    protected $message;


    private static $instance=null;  //创建静态单列对象变量

    /**
     * AliRPC constructor.
     * @param array $config
     * @throws \Exception
     */
    private function __construct($config = []){
        if(empty( $config )&& $C = Config::get('rpc.')){
            $config = $C;
        }
        /* 获取配置 */
        $this->config   =   array_merge($this->config, $config);
    }

    /**
     * 单列模式
     * @param array $config
     * @return AliRPC|null
     */
    public static function getInstance($config = array()){
        if(empty(self::$instance)) {
            self::$instance=new AliRPC($config);
        }
        return self::$instance;
    }

    /**
     * 克隆函数私有化，防止外部克隆对象
     * @throws \Exception
     */
    private function __clone(){
        throw new \Exception('禁止克隆');
    }

    /**
     * 返还错误信息
     * @return string
     */
    public function getError(){
        return $this->handler->getError();
    }

    /**
     * 设置错误信息
     * @param $error
     */
    public function setError($error){
        $this->handler->setError($error);
    }


    /**
     * 发起认证请求获取token
     * @param bool $RequestId 是否返回 RequestId
     * @return mixed|string
     */
    public function getToken($RequestId = false){
        $params['Action'] = 'DescribeVerifyToken';
        $params['RegionId'] = 'cn-hangzhou';
        $params['BizType'] = $this->config['BizType'];
        $params['BizId'] = md5(time());
        $params = array_merge($params,$this->baseParams());
        $params['Signature'] = $this->Signature($params,$this->config['AccessKeySecret']);

        $url = $this->baseUrl.http_build_query($params);
        $result = file_get_contents($url);
        $result = json_decode($result,true);

        if (!$RequestId){
            unset($result['RequestId']);
        }

        return $result;
    }

    /**
     * 获取公共参数
     * @return mixed
     */
    private function baseParams(){
        date_default_timezone_set("GMT");
        $params['Format'] = 'JSON';
        $params['Version'] = '2019-03-07';
        $params['AccessKeyId'] = $this->config['AccessKeyID'];
        $params['SignatureMethod'] = 'HMAC-SHA1';
        $params['Timestamp'] = date('Y-m-d\TH:i:s\Z');
        $params['SignatureVersion'] = '1.0';
        $params['SignatureNonce'] = uniqid();
        return $params;
    }


    // 获取签名
    private function Signature($params,$accessKeySecret){
        // 将参数Key按字典顺序排序
        ksort($params);
        // 生成规范化请求字符串
        $canonicalizedQueryString = '';
        foreach($params as $key => $value)
        {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key)
                . '=' . $this->percentEncode($value);
        }
        // 生成用于计算签名的字符串 stringToSign
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        // 计算签名，注意accessKeySecret后面要加上字符'&'
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
        return $signature;
    }


    // 使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范
    private function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

}