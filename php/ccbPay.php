<?php
require_once './ccbUtils.php';

/**
 * 被扫支付：建行互联网银企被扫支付(聚合)
 * Class ccbPay
 */
class ccbPay {

    // 商户号
    const MERCHANTID = '105910100190000';
    // 柜台号
    const POSID = '000000000';
    // 分行号
    const BRANCHID = '610000000';
    // 建行支付公钥
    const PUBKEY = '30819d300d06092a864886f70d010101050003818b0030818702818100a32fb2d51dda418f65ca456431bd2f4173e41a82bb75c2338a6f649f8e9216204838d42e2a028c79cee19144a72b5b46fe6a498367bf4143f959e4f73c9c4f499f68831f8663d6b946ae9fa31c74c9332bebf3cba1a98481533a37ffad944823bd46c305ec560648f1b6bcc64d54d32e213926b26cd10d342f2c61ff5ac2d78b020111';
    // 请求接口域名
    const HOST = 'https://ibsbjstar.ccb.com.cn/CCBIS/B2CMainPlat_00_BEPAY';

    /**
     * 建行支付，被扫
     */
    public function pay() {
        $data = [
            'MERCHANTID'   => self::MERCHANTID, // 商户号
            'POSID'        => self::POSID, // 柜台号
            'BRANCHID'     => self::BRANCHID, // 分行号
            'GROUPMCH'     => '', // 集团商户信息
            'TXCODE'       => 'PAY100', // 交易码
            'MERFLAG'      => '', // 商户类型
            'TERMNO1'      => '', // 终端编号 1
            'TERMNO2'      => '', // 终端编号 2
            'ORDERID'      => '', // 订单号
            'QRCODE'       => '', // 码信息（一维码、二维码）
            'AMOUNT'       => '0.01', // 订单金额，单位：元
            'PROINFO'      => '', // 商品名称
            'REMARK1'      => '', // 备注 1
            'REMARK2'      => '', // 备注 2
            'FZINFO1'      => '', // 分账信息一
            'FZINFO2'      => '', // 分账信息二
            'SUB_APPID'    => '', // 子商户公众账号 ID
            'RETURN_FIELD' => '', // 返回信息位图
            'USERPARAM'    => '', // 实名支付
            'detail'       => '', // 商品详情
            'goods_tag'    => '', // 订单优惠标记
        ];

        $ccbUtils = new ccbUtils();
        // 计算签名
        $sign = $ccbUtils->calSign($ccbUtils->sortParams($data));
        $data['SIGN'] = $sign;

        // 计算加密串
        $params = http_build_query($data);
        $pubKey = substr(self::PUBKEY, -30);
        $pubKey = substr($pubKey, 0, 8);
        $data['ccbParam'] = $ccbUtils->calCcbParam($params, $pubKey);

        // 获取要请求的参数
        $requestData = $ccbUtils->getRequestData($data);

        $url = self::HOST . '?' . http_build_query($requestData);
        var_dump($url);

    }

    /**
     * 支付查询
     */
    public function query() {
        $data = [
            'MERCHANTID'   => self::MERCHANTID, // 商户号
            'POSID'        => self::POSID, // 柜台号
            'BRANCHID'     => self::BRANCHID, // 分行号
            'GROUPMCH'     => '', // 集团商户信息
            'TXCODE'       => 'PAY101', // 交易码
            'MERFLAG'      => '', // 商户类型
            'TERMNO1'      => '', // 终端编号 1
            'TERMNO2'      => '', // 终端编号 2
            'ORDERID'      => '', // 订单号
            'QRYTIME'      => '', // 查询次数 从1开始
            'QRCODE'       => '', // 码信息（一维码、二维码）
            'QRCODETYPE'   => '', // 二维码类型 如未上送 QRCODE 则此参数为必输
            'REMARK1'      => '', // 备注 1
            'REMARK2'      => '', // 备注 2
            'SUB_APPID'    => '', // 子商户公众账号 ID
            'RETURN_FIELD' => '', // 返回信息位图
        ];
        // 与支付的区别TXCODE不一样，需要传QRYTIME，QRCODE和QRCODETYPE两个需传一个
        // 后续计算签名和加密串跟支付类似
    }

    public function refund() {
        // 退款只能走外联平台
    }

    /**
     * 建行返回参数sign验签
     */
    public function checkCcbSign() {
        // 建行返回的数据
        $returnData = [
            'RESULT' => 'Y',
            'ORDERID' => '151677281312212',
            'AMOUNT' => '0.01',
            'WAITTIME' => 'null',
            'TRACEID' => '1010115031516772964428432',
            'SIGN' => '80c3298a47b26cb9d8d708e1465c6b521edcce32b0deecab91257a3f41fc6cf39fa43afa54dc8489a04615eee9dcca1f4b52ce677f70109f29745ff34033018353b78e982cc860623b6c3df0d9c1a62ca010a019fff8544d4d8e154a010d7fc16cb590ccd87f34d8bea6added68cf1f9943fdb1d83616507a4588b68774b9fe1'
        ];
        $ccbUtils = new ccbUtils();
        $result = $ccbUtils->checkSign($ccbUtils->getCalSignData($returnData, ccbUtils::SIGN_CCB_PAY), self::PUBKEY);

        var_dump($result);

    }

}