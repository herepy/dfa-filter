<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/7/15
 * Time: 10:02
 */

namespace PyDfa;

class DfaFilter
{
    /**
     * @var array 敏感词树
     */
    protected $tree;

    /**
     * @var array 干扰因子
     */
    protected $disturbance;

    /**
     * @var object 单利模式的实例
     */
    protected static $instance;

    /**
     * DfaFilter constructor 单利模式，禁止手动new
     */
    protected function __construct()
    {

    }

    /**
     * 创建过滤实例
     *
     * @param array $words 敏感词
     * @param array $disturbance 干扰因子
     * @return object|DfaFilter 过滤实例
     */
    public static function build(array $words=[],$disturbance=[])
    {
        if (!self::$instance) {
            self::$instance=new self();
        }

        self::$instance->addSensitives($words);
        self::$instance->disturbance=$disturbance;

        return self::$instance;
    }

    /**
     * 添加干扰因子
     *
     * @param mixed $disturbance 干扰因子
     */
    public function addDisturbance($disturbance)
    {
        if (!is_array($disturbance)) {
            $disturbance=[$disturbance];
        }

        $this->disturbance=array_merge($this->disturbance,$disturbance);
    }

    /**
     * 导入敏感词文件
     *
     * @param string $filename 文件名
     * @param string $delimiter 内容分割符
     * @throws FileNotFoundException
     */
    public function importSensitiveFile(string $filename,$delimiter=",")
    {
        if (!$filename || !file_exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        $words=explode($delimiter,file_get_contents($filename));

        $this->addSensitives($words);
    }

    /**
     * 批量添加敏感词数组
     *
     * @param array $words 敏感词数组
     */
    public function addSensitives(array $words)
    {
        if (count($words) == 0) {
            return;
        }

        foreach ($words as $word) {
            $this->add($word);
        }
    }

    /**
     * 添加单个敏感词
     *
     * @param string $word 敏感词
     */
    protected function add(string $word)
    {
        $len=mb_strlen($word,'utf-8');
        if ($len == 0) {
            return;
        }

        $tree=&$this->tree;
        for ($i=0;$i<$len;$i++) {
            $char=mb_substr($word,$i,1,'utf-8');

            if (!isset($tree["map"][$char])) {
                $tree["map"][$char]=array("isEnd"=>false,"map"=>array());
            }

            $tree=&$tree["map"][$char];
        }

        //最后一个字符节点标记状态为结束
        $tree["isEnd"]=true;
    }

    /**
     * 是否是完整敏感词
     *
     * @param string $word 词
     * @return bool
     */
    public function isKey(string $word)
    {
        $len=mb_strlen($word,'utf-8');
        if ($len == 0) {
            return false;
        }

        $tree=$this->tree;
        for ($i=0;$i<$len;$i++) {
            $char=mb_substr($word,$i,1,'utf-8');

            //跳过干扰因子
            if (in_array($char,$this->disturbance)) {
                continue;
            }

            if (!isset($tree["map"][$char])) {
                return false;
            }
            $tree=$tree["map"][$char];
        }

        //如果遍历匹配完后，最后一个字符是最后节点，则匹配成功
        if ($tree["isEnd"] === true) {
            return true;
        }
        return false;
    }

    /**
     * 是否包含敏感词
     *
     * @param string $content 文本内容
     * @return bool 包含返回true
     */
    public function check(string $content)
    {
        $len=mb_strlen($content,'utf-8');
        if ($len == 0) {
            return false;
        }

        for ($i=0;$i<$len;$i++) {
            $tree=$this->tree;

            for($j=$i;$j<$len;$j++) {
                $char=mb_substr($content,$j,1,'utf-8');

                //跳过干扰因子
                if (in_array($char,$this->disturbance)) {
                    continue;
                }

                if (!isset($tree["map"][$char])) {
                    break;
                }

                $tree=$tree["map"][$char];

                //是否匹配某个词完成
                if ($tree["isEnd"] === true) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 过滤敏感词
     *
     * @param string $content 文本内容
     * @param bool $minMatch 是否是最小匹配原则
     * @param string $replace 替换文本
     * @return string 过滤后的文本内容
     */
    public function filter(string $content,$minMatch=true,$replace="*")
    {
        $len=mb_strlen($content,'utf-8');
        if ($len == 0) {
            return "";
        }

        $result=$content;
        for ($i=0;$i<$len;$i++) {

            $tree=$this->tree;
            $matchLen=0;
            $isMatch=false;
            $endIndex=$i;

            for($j=$i;$j<$len;$j++) {
                $endIndex=$j;
                $char=mb_substr($content,$j,1,'utf-8');

                //跳过干扰因子
                if (in_array($char,$this->disturbance)) {
                    //如果已经进入某个词的匹配验证，干扰因子占位长度也算进去
                    if ($matchLen > 0) {
                        $matchLen++;
                    }
                    continue;
                }

                if (!isset($tree["map"][$char])) {
                    break;
                }

                $tree=$tree["map"][$char];
                $matchLen++;

                //是否匹配某个词完成
                if ($tree["isEnd"] === true) {
                    $isMatch=true;

                    //是否是最小匹配
                    if ($minMatch == true) {
                        break;
                    }
                }
            }

            if ($isMatch && $matchLen > 0) {
                $result=mb_substr($result,0,$endIndex+1-$matchLen,'utf-8').
                        str_pad("",$matchLen,$replace).
                        mb_substr($result,$endIndex+1,null,'utf-8');
            }
        }
        return $result;
    }

}

