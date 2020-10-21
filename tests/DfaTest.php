<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/7/15
 * Time: 17:53
 */

namespace Pengyu\DfaFilter\Tests;

use Pengyu\DfaFilter\ArgumentException;
use Pengyu\DfaFilter\FileNotFoundException;
use PHPUnit\Framework\TestCase;
use Pengyu\DfaFilter\Filter;

class DfaTest extends TestCase
{

    /**
     * @var Filter
     */
    private $filter=null;

    public function setUp()
    {
        if ($this->filter === null) {
            $this->filter=Filter::build();

            $this->filter->addSensitives(["测试","通过","敏感","敏感词"]);
            $this->filter->addSensitives('');
            $this->filter->importSensitiveFile('./src/test.txt','|');
            $this->filter->importSensitiveFile('./src/testa.txt');
            try {
                $this->filter->importSensitiveFile('./src/testNotFound.txt');
            } catch (\Exception $exception) {
                $this->assertInstanceOf(FileNotFoundException::class,$exception);
            }

            $this->filter->addDisturbance(["@","&"]);
            $this->filter->addDisturbance('%');
        }
    }

    public function keyProvider()
    {
        return [
            ['通过',true],
            ['通%过',true],
            ['不通过',false],
            ['测%试',true],
            ['测试了',false],
            ['%@&',false],
            ['',false]
        ];
    }

    public function checkProvider()
    {
        return [
            ['我@通%%过了测试',true],
            ['我没有过',false],
            ['怎么会通%不过的测&试',true],
            ['试一试不通 过',false],
            ['',false]
        ];
    }

    public function filterProvider()
    {
        return [
            ['最小匹配敏感词啊','最小匹配**词啊','*',Filter::DFA_MIN_MATCH],
            ['最大匹配敏感词啊','最大匹配+++啊','+',Filter::DFA_MAX_MATCH],
            ['这个测试我不通过','这个??我不??','?',Filter::DFA_MIN_MATCH],
            ['这个测@试我不通%%过','这个***我不****','*',Filter::DFA_MIN_MATCH],
            ['&测试通过了,好开心','&****了,好开心','*',Filter::DFA_MIN_MATCH],
            ['测了个试，但是没通 过，又测%试@ 了一边','测了个试，但是没通 过，又***@ 了一边','*',Filter::DFA_MIN_MATCH],
            ['','','*',Filter::DFA_MIN_MATCH]
        ];
    }

    public function markProvider()
    {
        return [
            ['帮我找到敏感词啊','帮我找到<b>敏感</b>词啊',["<b>","</b>"],Filter::DFA_MIN_MATCH],
            ['帮我找到敏感词啊','帮我找到<b>敏感词</b>啊',["<b>","</b>"],Filter::DFA_MAX_MATCH]
        ];
    }

    /**
     * @param $key
     * @param $result
     * @dataProvider keyProvider
     */
    public function testIsKey($key,$result)
    {
        $this->assertEquals($this->filter->isKey($key),$result);
    }

    /**
     * @param $content
     * @param $result
     * @dataProvider checkProvider
     */
    public function testCheck($content,$result)
    {
        $this->assertEquals($this->filter->check($content),$result);
    }

    /**
     * @param $content
     * @param $result
     * @param $replace
     * @param $matchMode
     * @dataProvider filterProvider
     */
    public function testFilter($content,$result,$replace,$matchMode)
    {
        $this->assertEquals($result,$this->filter->filter($content,$replace,$matchMode));
    }

    /**
     * @param $content
     * @param $result
     * @param $marker
     * @param $matchMode
     * @dataProvider markProvider
     */
    public function testMark($content,$result,$marker,$matchMode)
    {
        $this->assertEquals($result,$this->filter->mark($content,$marker,$matchMode));
    }

    public function testCheckMark()
    {
        $this->assertEquals('帮我找到<b>敏感</b>词啊',$this->filter->mark('帮我找到敏感词啊',["<b>","</b>"]));
        $this->assertEquals('帮我找到+敏感+词啊',$this->filter->mark('帮我找到敏感词啊','+'));
        $this->assertEquals('帮我找到|敏感|词啊',$this->filter->mark('帮我找到敏感词啊',['|']));

        try {
            $this->assertEquals('帮我找到<b>敏感</b>词啊',$this->filter->mark('帮我找到敏感词啊',''));
        } catch (\Exception $exception) {
            $this->assertInstanceOf(ArgumentException::class,$exception);
        }

    }

    public function testFlushSensitives()
    {
        $this->filter->flushSensitives();
        $this->assertEquals([],$this->filter->getSensitivesTree());
    }

    public function testFlushDisturbance()
    {
        $this->filter->flushDisturbance();
        $this->assertEquals([],$this->filter->getDisturbance());
    }

}