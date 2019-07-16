
##是什么
一个基于DFA算法的敏感词过滤插件

##有哪些功能？
* 例用`单利模式`，保持整个项目实例一致
* 支持数组和`文件导入`两种方式添加敏感词，自定义文件导入时的敏感词分割符
* 支持添加`干扰因子`来增强敏感词的识别能力
* `检测词语`是否是敏感词
* `检测文本`是否包含敏感词
* `过滤文本`中的敏感词

##使用条件
PHP >= 7.0.0

##使用方式
克隆git地址:
```git
    git clone https://github.com/herepy/dfa-filter.git
```
composer安装所需文件:
```composer
    composer install
```
引入到项目:
```php
    require_once "vendor/autoload.php";
```

示例:
```php
//初始化
$filter=\PyDfa\DfaFilter::build();
//添加敏感词
$filter->addSensitives(["测试","良好","通过"]);
//添加干扰因子
$filter->addDisturbance("@");
$filter->addDisturbance(["?","%"]);
//检查是否是独立的敏感词
$filter->isKey("测试呀");
$filter->isKey("测试");
$filter->isKey("@测?试");
//检查是否包含敏感词
$filter->check("测试呀");
$filter->check("测试通?过了啊，感觉良?好%");
$filter->check("这次通不过了呀");
//敏感词替换
$filter->filter("简单的内容测*试，没有敏感词");
$filter->filter("简单的内容测?试，有敏感词");
$filter->filter("有很?多测@@试?的词，能?不能良%好?通??%过呢");
```