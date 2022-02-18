<?php

namespace Orthia;

class OrthiaBlockFunction
{
    public $terms = "";
    public $if_pathed = True;
    public $elif_pathed = True;
    public $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function if($terms)
    {
        foreach($this->params as $key => $val){
            $$key = $val;
        }

        if(eval($terms)){
            return True;
        }else{
            $this->if_pathed = True;
            return False;
        }
    }

    public function elif($terms)
    {
        foreach($this->params as $key => $val){
            $$key = $val;
        }

        if(eval($terms) && $this->if_pathed){
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

    public function else()
    {
        foreach($this->params as $key => $val){
            $$key = $val;
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

    public function foreach(String $terms)
    {
        foreach($this->params as $key => $val){
            $$key = $val;
        }
        $this->terms = $terms;
        return True;
    }

    public function endforeach()
    {
        //TODO 渡されたブロックを基に解析
    }

    public function endfor()
    {
        //TODO 渡されたブロックを基に解析
    }

    public function endif(String $template)
    {
        return $template;
    }
}