# 建行互联网银企被扫支付(聚合)
### 说明
- 只包含支付和查询，不含退款
- 建行官方提供的只有Java的demo，其他语言的是dll文件，Linux用不了
- 本项目的PHP版是根据Java版翻译过来的
- 本项目只适用于二维码支付（聚合）的被扫支付，即B扫C

### 签名计算流程
1. 将所有的请求参数去掉空值，并按key升序排序
2. 将第一步得到的数据，按key=value的形式进行拼接，用&隔开
3. 将拼接后的字符串再拼接上"20120315201809041004"
4. 将最后得到的字符串进行MD5加密，就是SIGN的值

### 加密串计算流程
1. 把上面签名后的结果以键值对的形式放入请求参数中(所有的请求参数，含空值)，键名是SIGN
2. 将第一步得到的请求参数，按key=value的形式进行拼接，用&隔开，得到待加密的字符串
3. 截取公钥的后30位，再截取这30位的前8位，得到一个8位的字符串，这个是参与加密串计算的公钥
4. 先将第二步得到的待加密的字符串从"utf-8"编码转为"utf-16"，并与第三步得到的8位的公钥用"DES-ECB"进行加密
5. 把第四步得到的加密结果中的"+"替换为","
6. 再对第五步的结果进行UrlEncode编码，得到的结果就是ccbParam

### 验签流程
1. 建行接口所有返回的参数，只取接口文档中的"签名源文格式"中相关的数据，作为验签源数据
2. 将返回的签名字段SIGN(十六进制)，转为十进制
3. 建行的公钥是DER格式的，且是十六进制，需要转为PEM格式。将完整的公钥转为十进制，同时进行base64编码，拼接上"-----BEGIN PUBLIC KEY-----"和"-----END PUBLIC KEY-----"做成pem
4. 提取第三步得到的PEM证书的公钥
5. 将第一步得到的验签源数据，按key=value的形式进行拼接，用&隔开，作为新的源数据
6. 使用MD5withRSA方法，将十进制的SIGN、源数据以及提取的公钥进行验证

PS：
- 目前建行退款只有两种方式，一种是商户服务平台退款(手动)，另外一个是外联平台退款(接口)，且外联平台跟支付的处理方式不同，具体详情可以参考“建行龙支付20200602.zip”
- 建行文档虽然说了请求支付和查询接口用POST，但是经过测试后发现POST不行，后续反馈给建行技术人员，他们说用GET
- 接口传的金额默认单位是：元，没有特殊说明的情况下都是元
- 请求参数严格区分大小写！
- 计算签名SIGN要去掉空值，计算加密串ccbParam要加上空值
- 建行的公钥和返回的签名是十六进制的，这个要注意


网速慢的朋友可以在这里自行下载
链接：https://pan.baidu.com/s/1SqOSrVIfds_XGDEV0r2AnA 
提取码：4a7u 
