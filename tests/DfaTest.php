<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/7/15
 * Time: 17:53
 */

namespace Tests;

use PHPUnit\Framework\TestCase;
use PyDfa\DfaFilter;

class DfaTest extends TestCase
{

    private $filter=null;

    public function setUp()
    {
        $this->testBuild();
    }

    public function testBuild()
    {
        if ($this->filter == null) {
            $this->filter=DfaFilter::build();
            $this->filter->addSensitives(["测试","通过","敏感","敏感词"]);
            $this->filter->addDisturbance(["@","&","%"]);
        }

        $this->assertInstanceOf("PyDfa\DfaFilter",$this->filter);
    }

    public function testIsKey()
    {
        $this->assertTrue($this->filter->isKey("通过"));
        $this->assertTrue($this->filter->isKey("通%过"));
        $this->assertFalse($this->filter->isKey("不通过"));
        $this->assertTrue($this->filter->isKey("测%试"));
        $this->assertFalse($this->filter->isKey("测试了"));
    }

    public function testCheck()
    {
        $this->assertTrue($this->filter->check("我@通%%过了测试"));
        $this->assertFalse($this->filter->check("我没有过"));
        $this->assertTrue($this->filter->check("怎么会通%不过的测&试"));
        $this->assertFalse($this->filter->check("试一试不通 过"));
    }

    public function testFilter()
    {
        $this->assertEquals("最小匹配**词啊",$this->filter->filter("最小匹配敏感词啊"));
        $this->assertEquals("最大匹配***啊",$this->filter->filter("最大匹配敏感词啊","*",DfaFilter::DFA_MAX_MATCH));

        $this->assertEquals("这个**我不**",$this->filter->filter("这个测试我不通过"));
        $this->assertEquals("这个***我不****",$this->filter->filter("这个测@试我不通%%过"));
        $this->assertEquals("&****了,好开心",$this->filter->filter("&测试通过了,好开心"));
        $this->assertEquals("测了个试，但是没通 过，又***@ 了一边",$this->filter->filter("测了个试，但是没通 过，又测%试@ 了一边"));
    }

    public function testMark()
    {
        $this->assertEquals("帮我找到<b>敏感</b>词啊",$this->filter->mark("帮我找到敏感词啊",["<b>","</b>"]));
        $this->assertEquals("帮我找到<b>敏感词</b>啊",$this->filter->mark("帮我找到敏感词啊",["<b>","</b>"],DfaFilter::DFA_MAX_MATCH));
    }

}