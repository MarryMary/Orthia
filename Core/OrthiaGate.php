<?php
namespace Orthia;

class OrthiaGate
{
    public function CallAnalyzer(String $template, Array $params, String $mode)
    {
        if($mode == "pythonista"){
            $mode = "pythonista";
        }else if ($mode == "phper"){
            $mode = "phper";
        }else{
            $mode = "phper";
        }
        $analyzer_instance = new Analyzer();
        return $analyzer_instance->Main($template, $params, False, $mode);
    }

    public function OnlyFunctionEval(String $template, Array $params = array())
    {
        $analyzer_instance = new Analyzer();
        return $analyzer_instance->Main($template);
    }

    public function OnlyParamsEval(String $template, Array $params)
    {
        $analyzer_instance = new Analyzer();
        return $analyzer_instance->Main($template, $params, True);
    }
}