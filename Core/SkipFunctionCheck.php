<?php

namespace Orthia;

class SkipFunctionCheck
{
    public function checker($function)
    {
        $flag = False;
        $registered = $this->registered_function();
        foreach($registered as $k => $v){
            if($k == $function){
                $flag = True;
                break;
            }else{
                $flag = False;
                continue;
            }
        }
        return $flag;
    }

    public function registered_function()
    {
        $result = file_get_contents(dirname(__FILE__)."/can_skip_using_function.json");
        return json_decode($result);
    }
}