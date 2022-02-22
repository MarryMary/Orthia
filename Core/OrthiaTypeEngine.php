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
        $dumper = "";
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
                    if(strpos($method_name,'end') !== false && substr($method_name, 0, 3)){
                        $dumping = False;
                        $dropping = False;
                        $is_code = True;
                        $this->template .= $BuiltInBlockFunction->$method_name($dumper);
                        $dumper = "";
                    }else {
                        $is_code = True;
                        if (!$this->JudgeBlockOrLine($method_name)) {
                            $pattern = "{\((.*)\)}";
                            preg_match($pattern, $val, $match);
                            if(isset($match[1])) {
                                $result = $BuiltInBlockFunction->$method_name($match[1]);
                            }else{
                                $result = $BuiltInBlockFunction->$method_name($match);
                            }
                            if (is_string($result)) {
                                if(strpos($result,'ORTHIASIGNAL@') !== false){
                                    return $this->template."##".$result;
                                }
                                $this->template .= $result;
                                unset($template[$key]);
                            } else if (is_bool($result) && $result) {
                                $dumping = True;
                                $dropping = False;
                            } else if (is_bool($result) && !$result) {
                                $dumping = False;
                                $dropping = True;
                            }
                        } else {
                            $BuiltInFunction = new OrthiaBuildInFunctions($this->params, $this->parsemode);
                            $pattern = "{\((.*)\)}";
                            preg_match($pattern, $val, $match);
                            if(isset($match[1])) {
                                $result = $BuiltInFunction->$method_name($match[1]);
                            }else{
                                $result = $BuiltInFunction->$method_name($match);
                            }
                            if (is_string($result)) {
                                $this->template .= $result;
                            }
                        }
                    }
                } else if ($this->UserCreateFunctionJudgement($val)) {
                    $is_code = True;
                    //TODO
                } else {
                    $is_code = True;
                    $result = eval($result);
                    $this->template .= $result;
                }
            }
            if(!$is_code){
                if($dumping){
                    $dumper .= $line;
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
        }else if($this->UserCreateFunctionJudgement($FunctionName)){
            //TODO
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