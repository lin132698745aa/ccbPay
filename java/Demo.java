

import java.util.*;

public class Demo {

    /**
     * 该示例以互联网银企直连被扫的PAY100接口为例
     * @throws Exception
     */
    public static void testCCBPayUtil() throws Exception{
        //银行接口url
        String host = "https://ibsbjstar.ccb.com.cn/CCBIS/B2CMainPlat_00_BEPAY?";
        //商户信息
        String merInfo = "MERCHANTID=105910100190000&POSID=000000000&BRANCHID=610000000";
        //获取柜台完整公钥
        String pubKey = "30819d300d06092a864886f70d010101050003818b0030818702818100a32fb2d51dda418f65ca456431bd2f4173e41a82bb75c2338a6f649f8e9216204838d42e2a028c79cee19144a72b5b46fe6a498367bf41" +
                "43f959e4f73c9c4f499f68831f8663d6b946ae9fa31c74c9332bebf3cba1a98481533a37ffad944823bd46c305ec560648f1b6bcc64d54d32e213926b26cd10d342f2c61ff5ac2d78b020111";
        //加密原串【PAY100接口定义的请求参数】
        String param = merInfo + "&MERFLAG=1&TERMNO1=&TERMNO2=&ORDERID=937857156" +
                "&QRCODE=134737690209713400&AMOUNT=0.01&TXCODE=PAY100&PROINFO=&REMARK1=&REMARK2=&SMERID=&SMERNAME=&SMERTYPEID=" +
                "&SMERTYPE=&TRADECODE=&TRADENAME=&SMEPROTYPE=&PRONAME=";
        //执行加密操作
        CCBPayUtil ccbPayUtil = new CCBPayUtil();
        String url = ccbPayUtil.makeCCBParam(param, pubKey);
        //拼接请求串
        url = host + merInfo + "&ccbParam=" + url;
        System.out.println(url);

        //请求的URL如下所示：
		/*
		https://ibsbjstar.ccb.com.cn/CCBIS/B2CMainPlat_00_BEPAY?MERCHANTID=105910100190000&POSID=000000000&BRANCHID=610000000
		&ccbParam=加密结果...
		 */

        //向建行网关发送请求交易...
        //HttpUtils.doSend(url);
    }


    /**
     * 商户通知验签DEMO，该示例以互联网银企直连被扫的PAY100接口为例
     */
    public static void testCCBNotifyCheck() {
        //商户通知参数
        String notifyURLParam = "RESULT=Y&ORDERID=151677281312212&AMOUNT=0.01&WAITTIME=null&TRACEID=1010115031516772964428432&SIGN=80c3298a47b26cb9d8d708e1465c6b521edcce32b0deecab91257a3f41fc6" +
                "cf39fa43afa54dc8489a04615eee9dcca1f4b52ce677f70109f29745ff34033018353b78e982cc860623b6c3df0d9c1a62ca010a019fff8544d4d8e154a010d7fc16cb590ccd87f34d8bea6added68cf1f9943fdb1d836" +
                "16507a4588b68774b9fe1";
        int i = notifyURLParam.indexOf("&SIGN=");
        //获取签名内容原串
        String strSrc = notifyURLParam.substring(0, i);
        //获取数字签名域
        String sign = notifyURLParam.substring(i+6, notifyURLParam.length());
        //商户柜台完整公钥
        String pubKey = "30819d300d06092a864886f70d010101050003818b0030818702818100a32fb2d51dda418f65ca456431bd2f4173e41a82bb75c2338a6f649f8e9216204838d42e2a028c79cee19144a72b5b46fe6a498367bf41" +
                "43f959e4f73c9c4f499f68831f8663d6b946ae9fa31c74c9332bebf3cba1a98481533a37ffad944823bd46c305ec560648f1b6bcc64d54d32e213926b26cd10d342f2c61ff5ac2d78b020111";
        //验证签名数据
        CCBPayUtil ccbPayUtil = new CCBPayUtil();
        boolean b = ccbPayUtil.verifyNotifySign(strSrc,sign,pubKey);
        if(b){
            System.out.println("签名验证成功！");
        } else {
            System.out.println("签名验证失败！");
        }
    }

    public static void main(String[] args) throws Exception {
        //商户接入加密DEMO
        testCCBPayUtil();
        //商户通知验证签名DEMO
        testCCBNotifyCheck();
    }
}
