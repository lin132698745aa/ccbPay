<?php
class ccbUtils {
    // 加密MD5 key
    const MD5KEY = '20120315201809041004';

    // 验证签名用到的类型，1-支付接口，2-查询接口
    const SIGN_CCB_PAY = 1;
    const SIGN_CCB_QUERY = 2;

    /**
     * 按key升序排序，同时去掉空值
     * @param $params array
     * @return mixed
     */
    public function sortParams($params) {
        ksort($params);
        foreach ($params as $key => $value) {
            if (empty($value) && $value == '') {
                unset($params[$key]);
            }
        }

        return $params;
    }

    /**
     * 计算签名
     * @param $params array 不含空值
     * @return string
     */
    public function calSign($params) {
        return md5(http_build_query($params) . self::MD5KEY);
    }

    /**
     * 计算ccbparam
     * @param $params string
     * @param $key string
     * @return string
     */
    public function calCcbParam($params, $key) {
        $res = openssl_encrypt (iconv("utf-8", "utf-16", $params), 'DES-ECB', $key);
        $res = str_replace('+', ',', $res);
        $res = urlencode($res);

        return $res;
    }

    /**
     * 真正请求建行接口要传的参数
     * @param $data array
     * @return array
     */
    public function getRequestData($data) {
        return [
            'MERCHANTID' => $data['MERCHANTID'],
            'POSID'      => $data['POSID'],
            'BRANCHID'   => $data['BRANCHID'],
            'ccbParam'   => $data['ccbParam'],
        ];
    }

    /**
     * 获取要验证签名的参数
     * @param $data array
     * @param $type int
     * @return array
     */
    public function getCalSignData($data, $type) {
        switch ($type) {
            case self::SIGN_CCB_PAY:
                $res = [
                    'RESULT' => $data['RESULT'],
                    'ORDERID' => $data['ORDERID'],
                    'AMOUNT' => $data['AMOUNT'],
                    'WAITTIME' => $data['WAITTIME'],
                    'TRACEID' => $data['TRACEID'],
                    'SIGN' => $data['SIGN']
                ];
                break;
            case self::SIGN_CCB_QUERY:
                $res = [
                    'RESULT' => $data['RESULT'],
                    'ORDERID' => $data['ORDERID'],
                    'AMOUNT' => $data['AMOUNT'],
                    'WAITTIME' => $data['WAITTIME'],
                    'SIGN' => $data['SIGN']
                ];
                break;
            default:
                $res = [];
                break;
        }

        return $res;
    }

    /**
     * 验证签名
     * @param $data array
     * @param $key string
     * @return bool
     */
    public function checkSign($data, $key) {
        if (empty($data)) {
            return false;
        }
        $sign = $data['SIGN'];
        unset($data['SIGN']);
        $data = http_build_query($data);

        $pubkey = "-----BEGIN PUBLIC KEY-----\n"
            . wordwrap(base64_encode(self::Hex2String($key)), 64, "\n", true)
            . "\n-----END PUBLIC KEY-----";
        $pkeyId = openssl_pkey_get_public($pubkey);
        $verify = openssl_verify($data, self::Hex2String($sign), $pkeyId, OPENSSL_ALGO_MD5);
        openssl_free_key($pkeyId);

        return (bool) $verify;
    }

    /**
     * 十六进制转字符串
     * @param $hex string
     * @return string
     */
    private function Hex2String($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    /**
     * 字符串转十六进制
     * @param $str string
     * @return string
     */
    private function String2Hex($str){
        $hex='';
        for ($i=0; $i < strlen($str); $i++){
            $hex .= dechex(ord($str[$i]));
        }
        return $hex;
    }

}