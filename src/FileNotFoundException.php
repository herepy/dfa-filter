<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2019/7/15
 * Time: 17:14
 */

namespace Pengyu\DfaFilter;

class FileNotFoundException extends \Exception
{

    public function __construct($filename = "", $code = 0, Throwable $previous = null)
    {
        $message="Can not found the file:".$filename;
        parent::__construct($message, $code, $previous);
    }

}