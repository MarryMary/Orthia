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

    public function frame($path)
    {
        $path = trim($path, '"');
        $path = trim($path, "'");
        $AnalyzerInstance = new Analyzer();
        $template = file_get_contents(dirname(__FILE__)."/../Template/".trim(trim(trim($path), "/")), "\\");
        return $AnalyzerInstance->Main($template, $this->params, False, $this->parsemode);
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
}