# think-aliRPC
The ThinkPHP5 alibaba RPC
laibaba ERC 实人认证 SDK

官方文档地址： https://help.aliyun.com/document_detail/127471.html?spm=a2c4g.11186623.6.569.17ef684dmHNfpc
## 安装

### 一、执行命令安装
```
composer require dh2y/think-aliRPC
```

或者

### 二、require安装

  适配5.0
```
"require": {
        "dh2y/think-aliRPC":"1.*"
},
```

  适配5.1
```
"require": {
        "dh2y/think-aliRPC":"2.*"
},
```

或者
###  三、autoload psr-4标准安装
```
   a) 进入vendor/dh2y目录 (没有dh2y目录 mkdir dh2y)
   b) git clone 
   c) 修改 git clone下来的项目名称为think-aliRPC
   d) 添加下面配置
   "autoload": {
        "psr-4": {
            "dh2y\\aliRPC\\": "vendor/dh2y/think-aliRPC/src"
        }
    },
    e) php composer.phar update
```


## 使用
#### 添加配置文件
```
return [
    'AccessKeyID' => 'LTAI4FdYqG5uHNwmrN38nQuS',
    'AccessKeySecret' => 'yGwGOLTCzyrQranc5Fx4mqaK15S5n9',
    'BizType' => 'mimiliaoRPBase'   //业务场景标识
];
```

#### 使用方法
```
 $token = AliRPC::getInstance()->getToken();

```


