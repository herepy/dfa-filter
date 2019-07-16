<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/7/15
 * Time: 17:36
 */

require_once "vendor/autoload.php";

$filter=\PyDfa\DfaFilter::build();

$filter->addSensitives(["测试","良好","通过"]);

$filter->addDisturbance("@");
$filter->addDisturbance(["?","%"]);

$filter->isKey("测试呀");
$filter->isKey("测试");
$filter->isKey("@测?试");

$filter->check("测试呀");
$filter->check("测试通?过了啊，感觉良?好%");
$filter->check("这次通不过了呀");

$filter->filter("简单的内容测*试，没有敏感词");
$filter->filter("简单的内容测?试，有敏感词");
$filter->filter("有很?多测@@试?的词，能?不能良%好?通??%过呢");