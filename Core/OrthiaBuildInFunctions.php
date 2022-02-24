<?php
namespace Orthia;

use Orthia\ClearSkyOrthiaException;
use Orthia\UUIDFactory;

class OrthiaBuildInFunctions
{
    public $params;
    public $parsemode;

    public function __construct(Array $params, String $mode = "phper")
    {
        $this->params = $params;
        $this->parsemode = $mode;
    }

    public function csrf_token()
    {
        $token = UUIDFactory::generate();
        if ((function_exists('session_status')
                && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
            session_start();
        }
        $_SESSION["csrf_token"] = $token;
        return "<input type='hidden' name='csrf_token' value='".$token."'>";
    }

    public function break()
    {
        return "ORTHIASIGNAL@STOP";
    }

    public function debug()
    {
        $config_json = file_get_contents(dirname(__FILE__)."/config.json");
        $config_array = mb_convert_encoding($config_json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $config_array = json_decode($config_array,true);
        $version = $config_array['version'];
        $engine_name = $config_array['engine_name'];
        $dtype = $config_array['type'];
        $codename = $config_array['version_name'];
        $mode = $this->parsemode;
        $params = $this->params;
        $AnalyzerInstance = new Analyzer();
        $template = file_get_contents(dirname(__FILE__)."/systemplate/debug_table.html");
        return $AnalyzerInstance->Main($template, compact("version", "engine_name", "dtype", "codename", "mode", "params"), False, "phper");
    }

    public function ArrayAnalyzer(Array $array, String $keywords)
    {
        $parsemode = $this->parsemode;
        if(strtolower($parsemode) == "phper"){
            $exploded_keyword = explode("->", $keywords);
        }else{
            $exploded_keyword = explode(".", $keywords);
        }
        if(count($exploded_keyword) == 1 || count($exploded_keyword) == 0){
            ob_start();
            var_dump($array);
            $line = "<pre><code>".ob_get_contents()."</code></pre>";
            ob_end_clean();
            return $line;
        }else{
            $based_array = $array;
            $key = 1;
            while(True){
                if(array_key_exists($key, $exploded_keyword) && is_array($based_array)){
                    $based_array = $based_array[$exploded_keyword[$key]];
                    $key++;
                }else{
                    break;
                }
            }
            if(is_array($based_array)){
                ob_start();
                var_dump($based_array);
                $line = "<pre><code>".ob_get_contents()."</code></pre>";
                ob_end_clean();
                return $line;
            }else{
                return htmlspecialchars($based_array);
            }
        }
    }

    public function paste_here(String $parts_name)
    {
        if(array_key_exists("[ORTHIABLOCKOBJECT]".trim(trim(trim($parts_name), "'"), '"'), $this->params)){
            return $this->params["[ORTHIABLOCKOBJECT]".$parts_name];
        }else{
            return "";
        }
    }

    public function convey(String $path)
    {
        $path = dirname(__FILE__)."/../Template/".trim(trim(trim(trim(trim($path), "/"), "\\"), "'"), '"');
        if(file_exists($path)){
            $template = file_get_contents($path);
            $AnalyzerInstance = new Analyzer();
            $template = $AnalyzerInstance->Main($template, $this->params, False, "phper");
            return $template;
        }else{
            return "";
        }
    }
}