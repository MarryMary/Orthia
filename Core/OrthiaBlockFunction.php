<?php

namespace Orthia;

use Orthia\ClearSkyOrthiaException;


class OrthiaBlockFunction
{
    public $terms = "";
    public $parsemode = "phper";
    public $if_pathed = True;
    public $elif_pathed = True;
    public $copy_parts = array();
    public $block_name = "";
    public $frame = "";
    public $params;

    public function __construct($params, $parsemode)
    {
        $this->params = $params;
        $this->parsemode = $parsemode;
    }

    public function if($terms)
    {
        foreach($this->params as $ORTHIAkey => $ORTHIAval){
            $$ORTHIAkey = $ORTHIAval;
        }

        if(eval("return ".$terms.";")){
            return True;
        }else{
            $this->if_pathed = True;
            return False;
        }
    }

    public function elif($terms)
    {
        foreach($this->params as $ORTHIAkey => $ORTHIAval){
            $$ORTHIAkey = $ORTHIAval;
        }

        if(eval("return ".$terms.";") && $this->if_pathed){
            return True;
        }else{
            if($this->if_pathed){
                return False;
            }else {
                $this->elif_pathed = True;
                return False;
            }
        }
    }

    public function elseif($terms)
    {
        return $this->elif($terms);
    }

    public function else()
    {
        foreach($this->params as $ORTHIAkey => $ORTHIAval){
            $$ORTHIAkey = $ORTHIAval;
        }

        if($this->if_pathed && $this->elif_pathed){
            return True;
        }else{
            return False;
        }
    }

    public function for(String $terms)
    {
        $this->terms = $terms;
        return True;
    }

    public function comment()
    {
        return True;
    }

    public function foreach(String $terms)
    {
        foreach($this->params as $key => $val){
            $$key = $val;
        }
        $this->terms = $terms;
        return True;
    }

    public function parts_block(String $block_name)
    {
        $this->block_name = $block_name;
        return True;
    }


    public function endforeach(String $dumper)
    {
        $result = "";
        $param = $this->params;
        foreach($this->params as $PARAMS_VARIABLE => $VARIABLE_VALUE){
            $$PARAMS_VARIABLE = $VARIABLE_VALUE;
        }
        $terms = $this->terms;
        $exploded_terms = explode("as", $terms);
        if(count($exploded_terms) == 2){
            $exploded_variable = explode("=>", $exploded_terms[1]);
            if(count($exploded_variable) == 2){
                $as_before = ltrim(trim($exploded_terms[0]), "$");
                $key = ltrim(trim($exploded_variable[0]), "$");
                $value = ltrim(trim($exploded_variable[1]), "$");
                foreach($$as_before as $k => $v){
                    $add_array = [
                        $key => $k,
                        $value => $v
                    ];
                    $params = array_merge($param, $add_array);
                    $AnalyzerInstance = new Analyzer();
                    $returned =  $AnalyzerInstance->Main($dumper, $params, False, $this->parsemode);
                    if(strpos($returned,'ORTHIASIGNAL@') !== false){
                        $rslt = explode("##", $returned);
                        if(count($rslt) >= 2) {
                            $result .= $rslt[0];
                            $order = explode("@", $rslt[1]);
                            if(count($order) >= 2) {
                                if ($order[1] == "STOP") {
                                    break;
                                } else if ($order[1] == "CONTINUE") {
                                    continue;
                                }else{
                                    break;
                                }
                            }else{
                                $result .= $returned;
                            }
                        }else{
                            $result .= $returned;
                        }
                    }else{
                        $result .= $returned;
                    }
                }
            }else{
                foreach($$exploded_terms[0] as $$exploded_terms[1]){
                    $AnalyzerInstance = new Analyzer();
                    $result .= $AnalyzerInstance->Main($dumper, $this->params, False, $this->parsemode);
                }
            }
            return $result;
        }else{
            throw new ClearSkyOrthiaException("foreach構文の条件指定が誤っています。");
        }
    }

    public function endfor(String $dumper)
    {
        $param = $this->params;
        $result = "";
        foreach($this->params as $PARAMS_VARIABLE => $VARIABLE_VALUE){
            $$PARAMS_VARIABLE = $VARIABLE_VALUE;
        }
        $terms = $this->terms;
        $exploded_terms = explode(";", $terms);
        if(count($exploded_terms) == 3){
            $initializer = explode("=", $exploded_terms[0]);
            $variable_term = $exploded_terms[1];
            $doing = $exploded_terms[2];
            if(count($initializer) == 2){
                $initializer_variable = ltrim(trim($initializer[0]), "$");
                $initializer_value = trim($initializer[1]);
                //TODO 代入されたのが変数または関数である場合
                if(is_numeric($initializer_value) || isset($$initializer_value) && is_int($$initializer_value) || is_int(eval("return ".$$initializer_value.";"))){
                    if(is_numeric($initializer_value)) {
                        $$initializer_variable = (int)$initializer_value;
                    }else if(isset($$initializer_value) && is_int($$initializer_value)){
                        $$initializer_variable = $$initializer_value;
                    }else if(is_int(eval("return ".$$initializer_value.";"))){
                        $$initializer_variable = eval("reuturn ".$$initializer_variable.";");
                    }else{
                        $$intiializer_variable = 0;
                    }
                    while(True){
                        if(eval("return ".$variable_term.";")){
                            break;
                        }else{
                            $add_params = compact($initializer_variable);
                            $params = array_merge($param, $add_params);
                            $AnalyzerInstance = new Analyzer();
                            $returned = $AnalyzerInstance->Main($dumper, $params, False, $this->parsemode);
                            if(strpos($returned,'ORTHIASIGNAL@') !== false){
                                $rslt = explode("##", $returned);
                                if(count($rslt) >= 2) {
                                    $result .= $rslt[0];
                                    $order = explode("@", $rslt[1]);
                                    if(count($order) >= 2) {
                                        if ($order[1] == "STOP") {
                                            break;
                                        } else if ($order[1] == "CONTINUE") {
                                            continue;
                                        }else{
                                            break;
                                        }
                                    }else{
                                        $result .= $returned;
                                    }
                                }else{
                                    $result .= $returned;
                                }
                            }else{
                                $result .= $returned;
                            }
                            eval($doing.";");
                        }
                    }
                    return $result;
                }else{
                    throw new ClearSkyOrthiaException("for構文の条件指定が誤っています。");
                }
            }else{
                throw new ClearSkyOrthiaException("for構文の条件指定が誤っています。");
                exit(1);
            }
        }else{
            throw new ClearSkyOrthiaException("for構文の条件指定が誤っています。");
            exit(1);
        }
    }

    public function endif(String $template)
    {
        return $template;
    }

    public function endcomment(String $dumped)
    {
        return "";
    }

    public function endparts_block(String $dumper)
    {
        $params = $this->params;
        $AnalyzerInstance = new Analyzer();
        $returned = $AnalyzerInstance->Main($dumper, $params, False, $this->parsemode);
        $this->copy_parts["[ORTHIABLOCKOBJECT]".trim(trim(trim($this->block_name), "'"), '"')] = $returned;
        return "";
    }

    public function assembleTo(String $path)
    {
        $param = $this->params;
        $params = array_merge($param, $this->copy_parts);
        $path = dirname(__FILE__)."/../Template/".trim(trim(trim(trim(trim($path), "/"), "\\"), "'"), '"');
        if(file_exists($path)){
            $frame_template = file_get_contents($path);
            $AnalyzerInstance = new Analyzer();
            $returned = $AnalyzerInstance->Main($frame_template, $params, False, $this->parsemode);
            return $returned;
        }else{
            return "";
        }
    }
}