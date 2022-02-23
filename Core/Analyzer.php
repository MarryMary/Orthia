<?php
namespace Orthia;

include dirname(__FILE__)."/../vendor/autoload.php";

use Orthia\ClearSkyOrthiaException;
use Orthia\OrthiaBuildInFunctions;
use UserFunction\UserFunction;

class Analyzer
{
    private $param;
    private $template;
    private $parsemode;
    public function Main(String $template, Array $param = array(), Bool $mode = False, String $writer = "phper")
    {
        if(trim($writer) != ""){
            if(count($param) != 0 && !$mode){
                $this->template = $template;
                $this->param = $param;
                $this->parsemode = $writer;
                $analyzer = new OrthiaTypeEngine();
                $this->template = $analyzer->Entrance($this->template, $this->param, $this->parsemode);
                $this->VariableInserter();
                return $this->template;
            }else if(count($param) == 0){
                $this->template = $template;
                $this->param = array();
                $this->parsemode = $writer;
                $analyzer = new OrthiaTypeEngine();
                $this->template = $analyzer->Entrance($this->template, $this->param, $this->parsemode);
                return $this->template;
            }else{
                $this->template = $template;
                $this->param = $param;
                $this->parsemode = $writer;
                $this->VariableInserter();
                return $this->template;
            }
        }else{
            throw new ClearSkyOrthiaException("writer引数（記述方式スイッチャー）にはphper（php方式値展開）またはpythonista（python方式値展開）のみを入れることができます。");
            exit(1);
        }
    }

    private function VariableInserter()
    {
        foreach($this->param as $CLSKORTHIAKEY => $CLSKORTHIAVAL) {
            $$CLSKORTHIAKEY = $CLSKORTHIAVAL;
        }
        unset($CLSKORTHIAKEY);
        unset($CLSKORTHIAVAL);
        $prepared = "";
        $template = explode("\n", $this->template);
        $this->template = "";
        foreach($template as $line){
            $pattern = '/\{{.+?\}}/';
            preg_match_all($pattern, $line, $variable);
            foreach($variable as $val){
                foreach($val as $v){
                    if($this->parsemode == "phper"){
                        $val_trimed = ltrim(trim(rtrim(ltrim(trim($v), "{{"), "}}")), "$");
                        $val_untrimed = $val_trimed;
                        $val_trimed = explode("->", $val_trimed);
                    }else if($this->parsemode == "pythonista"){
                        $val_trimed = trim(rtrim(ltrim(trim($v), "{{"), "}}"));
                        $val_untrimed = $val_trimed;
                        $val_trimed = explode(".", $val_trimed);
                    }
                    if(isset(${$val_trimed[0]}) && strpos($line, $v) !== false){
                        if(count($val_trimed) != 1){
                            $FunctionInstance = new OrthiaBuildInFunctions($this->param, $this->parsemode);
                            $line = $FunctionInstance->ArrayAnalyzer(${$val_trimed[0]}, $val_untrimed);
                        }else{
                            if(is_array(${$val_trimed[0]})){
                                ob_start();
                                var_dump(${$val_trimed[0]});
                                $line = "<pre><code>".ob_get_contents()."</code></pre>";
                                ob_end_clean();
                            }else{
                                $line = str_replace($v, htmlspecialchars(${$val_trimed[0]}), $line);
                            }
                        }
                    }else{
                        $line = "";
                    }
                }
            }
            $this->template .= $line . "\n";
        }
        return True;
    }
}