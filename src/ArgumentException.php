<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/7/15
 * Time: 17:14
 */

namespace Pengyu\DfaFilter;

class ArgumentException extends \Exception
{

    public function __construct($arg = "", $code = 0, Throwable $previous = null)
    {
        $message="The argument is invalid:".$arg;
        parent::__construct($message, $code, $previous);
    }

}