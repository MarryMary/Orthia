<?php

namespace Orthia;

class OrthiaBlockFunction
{
    public $terms = "";
    public $parsemode = "phper";
    public $if_pathed = True;
    public $elif_pathed = True;
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

    public function endforeach(String $dumper)
    {
        $result = "";
        $param = $this->params;
        foreach($this->params as $PARAMS_VARIABLE => $VARIABLE_VALUE){
            $$PARAMS_VARIABLE = $VARIABLE_VALUE;
        }
        if(strtolower($this->parsemode) == "pythonista"){
            //TODO endfor関数へ処理を委譲、そこからさらにReSTYLEエンジンへ処理を委譲
        }else{
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
    }

    public function endfor(String $dumper)
    {
        $param = $this->params;
        $result = "";
        foreach($this->params as $PARAMS_VARIABLE => $VARIABLE_VALUE){
            $$PARAMS_VARIABLE = $VARIABLE_VALUE;
        }
        if(strtolower($this->parsemode) == "pythonista"){
            //TODO ReSTYLEへ処理を委譲
        }else{
            $terms = $this->terms;
            $exploded_terms = explode(";", $terms);
            if(count($exploded_terms) == 3){
                $initializer = explode("=", $exploded_terms[0]);
                $variable_term = $exploded_terms[1];
                $doing = $exploded_terms[2];
                if(count($initializer) == 2){
                    $initializer_variable = ltrim(trim($initializer[0]), "$");
                    $initializer_value = trim($initializer[1]);
                    if(is_numeric($initializer_value)){
                        $$initializer_variable = (int)$initializer_value;
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
    }

    public function endif(String $template)
    {
        return $template;
    }

    public function endcomment(String $dumped)
    {
        return "";
    }
}