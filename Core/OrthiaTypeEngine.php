<?php

namespace Orthia;

use Orthia\ClearSkyOrthiaException;
use Orthia\OrthiaBlockFunction;
use Orthia\OrthiaBuildInFunctions;
use UserFunction\UserFunction;

class OrthiaTypeEngine
{
    private $template;
    private $params;
    private $parsemode = "phper";
    public function Entrance(String $template, Array $params, String $mode = "phper")
    {
        $this->template = $template;
        $this->params = $params;
        $this->parse_mode = $mode;
        $this->MainAnalyzer();
        return $this->template;
    }

    public function MainAnalyzer()
    {
        $template = explode("\n", $this->template);
        $this->template = "";
        $dumping = False;
        $dropping = False;
        $endcounter = 0;
        $dumper = "";
        $block_start_name = "";
        $BuiltInBlockFunction = new OrthiaBlockFunction($this->params, $this->parsemode);
        foreach($template as $key => $line){
            $is_code = False;
            $pattern = '/\{%.+?%\}/';
            preg_match_all($pattern, $line, $variables);
            foreach($variables[0] as $variable) {
                $val = trim($variable);
                $val = str_replace('{%', '', $val);
                $val = str_replace('%}', '', $val);
                $val = trim($val);
                $method_name = substr($val, 0, strcspn($val,'('));
                if ($this->BuiltInFunctionJudgement($method_name)) {
                    if(strpos($method_name,'end') !== false && substr($method_name, 0, 3) && $method_name == "end".trim($block_start_name)){
                        if($endcounter == 0){
                            $dumping = False;
                            $dropping = False;
                            $is_code = True;
                            $this->template .= $BuiltInBlockFunction->$method_name($dumper);
                            $dumper = "";
                        }else{
                            $endcounter--;
                        }
                    }else if(!$dumping && !$dropping) {
                        $is_code = True;
                        if (!$this->JudgeBlockOrLine($method_name)) {
                            $pattern = "{\((.*)\)}";
                            preg_match($pattern, $val, $match);
                            if(isset($match[1])) {
                                $result = $BuiltInBlockFunction->$method_name($match[1]);
                            }else if(!$dumping && !$dropping){
                                if(array_key_exists(0, $match)){
                                    $result = $BuiltInBlockFunction->$method_name($match[0]);
                                }else{
                                    $result = $BuiltInBlockFunction->$method_name();
                                }
                            }
                            if (is_string($result)) {
                                if(strpos($result,'ORTHIASIGNAL@') !== false){
                                    return $this->template."##".$result;
                                }
                                $this->template .= $result;
                                unset($template[$key]);
                            } else if (is_bool($result) && $result) {
                                $block_start_name = $method_name;
                                $dumping = True;
                                $dropping = False;
                            } else if (is_bool($result) && !$result) {
                                $block_start_name = $method_name;
                                $dumping = False;
                                $dropping = True;
                            }
                        } else if($this->JudgeBlockOrLine($method_name)) {
                            $BuiltInFunction = new OrthiaBuildInFunctions($this->params, $this->parsemode);
                            $pattern = "{\((.*)\)}";
                            preg_match($pattern, $val, $match);
                            if(isset($match[1]) && !$dropping && !$dumping) {
                                $result = $BuiltInFunction->$method_name($match[1]);
                            }else if(!$dropping && !$dumping){
                                $result = $BuiltInFunction->$method_name($match);
                            }
                            if (is_string($result)) {
                                $this->template .= $result."\n";
                            }
                        }
                    }else{
                        if(trim($method_name) == trim($block_start_name)){
                            $endcounter ++;
                        }
                    }
                } else if ($this->UserCreateFunctionJudgement($val) && !$dropping && !$dumping) {
                    $is_code = True;
                    $UserFunction = new UserFunction();
                    $pattern = "{\((.*)\)}";
                    preg_match($pattern, $val, $match);
                    if(isset($match[1]) && !$dropping && !$dumping) {
                        $result = $UserFunction->$method_name($match[1]);
                    }else if(!$dropping && !$dumping){
                        $result = $UserFunction->$method_name($match);
                    }
                    $this->template .= $line;
                } else {
                    if($dumping || $dropping){
                        $is_code = False;
                    }else {
                        $is_code = True;
                    }
                    $result = eval("return ".$val.";");
                    $this->template .= $result."\n";
                }
            }
            if(!$is_code){
                if($dumping){
                    $dumper .= $line."\n";
                }
                if(!$dropping && !$dumping) {
                    $this->template .= $line;
                }
            }
        }
    }

    public function JudgeBlockOrLine(String $FunctionName)
    {
        if($this->BuiltInFunctionJudgement($FunctionName)){
            $BuiltInFunction = new OrthiaBuildInFunctions($this->params, $this->parsemode);
            $BuiltInBlockFunction = new OrthiaBlockFunction($this->params, $this->parsemode);
            if(method_exists($BuiltInFunction, $FunctionName)){
                return True;
            }else if(method_exists($BuiltInBlockFunction, $FunctionName)){
                return False;
            }
        }else{
            return False;
        }
    }

    public function BuiltInFunctionJudgement(String $FunctionName)
    {
        $BuiltInFunction = new OrthiaBuildInFunctions($this->params, $this->parsemode);
        $BuiltInBlockFunction = new OrthiaBlockFunction($this->params, $this->parsemode);
        if(method_exists($BuiltInFunction, $FunctionName) || method_exists($BuiltInBlockFunction, $FunctionName)){
            return True;
        }else{
            return False;
        }
    }

    public function UserCreateFunctionJudgement(String $FunctionName)
    {
        $UserFunction = new UserFunction();
        if(method_exists($UserFunction, $FunctionName)){
            return True;
        }else{
            return False;
        }
    }
}