<?php
namespace Orthia;

use Orthia\ClearSkyOrthiaException;
use Orthia\UUIDFactory;

class OrthiaBuildInFunctions
{
    public function csrf_token()
    {
        return False;
    }

    public function break()
    {
        //TODO forまたはforeachブロックを停止
    }

    public function frame()
    {
        //TODO パーツを取得・返却
    }

    public function versioning()
    {
        return "v0.0.1 (Proto type system)";
    }

    public function debug()
    {
        //TODO バージョン番号などを返却
    }
}