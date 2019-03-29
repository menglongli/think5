<?php
/**
 * exception :异常
 */
namespace app\demo\controller;
use think\Exception;
class Exceptions
{
   public function checknum()
    {
        try{
            $number = 3;
            if($number>1) {
                throw new Exception("number 大于1");
            }
            if($number<4) {
                throw new Exception("number 小于4");
            }
        }
        catch(Exception $e){
            echo $e->getMessage();
        }

    }
}