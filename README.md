## 简介

一个基于DFA算法的敏感词过滤插件

## 功能特性

* 例用`单利模式`，保持整个项目实例一致
* 导入敏感词文本采用yield生成器`降低内存`
* 支持数组和`文件导入`两种方式添加敏感词，自定义文件导入时的敏感词分割符
* 支持添加`干扰因子`来增强敏感词的识别能力
* 自定义符号`标记`敏感词
* `匹配模式`模式分为最大最小两种，默认最小匹配模式
* `检测词语`是否是敏感词
* `检测文本`是否包含敏感词
* `过滤文本`中的敏感词
* `单元测试`覆盖率高

## 使用条件

PHP >= 5.5.0

## 安装说明

##### git方式安装:
```git
    git clone https://github.com/herepy/dfa-filter.git
    cd dfa-filter && composer install
```
##### composer方式安装
```comopser
    composer require pengyu/dfa-filter
```

##### 引入项目
```php
    use Pengyu\DfaFilter\Filter;
    require_once "vendor/autoload.php";
    $filer=Filter::build();
```

## 如何使用:
##### 初始化实例并设置敏感词库
```php
$filter=Filter::build();
$filter->addSensitives(["测试","良好","通过"]);
$filter->importSensitiveFile("words.txt");
```
##### 添加干扰因子
```php
$filter->addDisturbance("@");
$filter->addDisturbance(["?","%"]);
```
##### 检查是否是独立的敏感词
```php
$filter->isKey("测试呀");
$filter->isKey("测试");
$filter->isKey("@测?试");
```
##### 检查是否包含敏感词
```php
$filter->check("测试呀");
$filter->check("测试通?过了啊，感觉良?好%");
$filter->check("这次通不过了呀");
```
##### 敏感词替换
```php
$filter->filter("测试了一下，看看能不能@@通%%过了","^",Filter::DFA_MAX_MATCH);
$filter->filter("简单的内容测?试，有敏感词");
$filter->filter("有很?多测@@试?的词，能?不能良%好?通??%过呢");
```
##### 标记敏感词
```php
$filter->mark("这里有一个敏感词通过,看看测@试%会不会过");
$filter->mark("这里有一个敏感词通过,看看测@试%会不会过",["<span>","</span>"],Filter::DFA_MAX_MATCH);
```
##### 重置敏感词树和干扰因子
```php
$filter->flushSensitives();
$filter->flushDisturbance();
```
##### 查看当前敏感词树和干扰因子
$filter->getSensitivesTree();
$filter->getDisturbance();