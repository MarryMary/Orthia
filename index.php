<?php
include "vendor/autoload.php";

use Orthia\OrthiaGate;

$path = "Template/test.html";
$lang = "ja";
$tu = "Hello";
$hypn = ",";
$ka = "world!";
$title = "AnalyzerTest";
$array_test = ["a" => "clear!", "b" => "clear!", "c" => "clear!", "d" => "clear!", "e" => "clear", "f" => "clear!", "g" => "clear!"];

$template_string = file_get_contents($path);
$ReaderInstance = new OrthiaGate();
echo $ReaderInstance->CallAnalyzer($template_string, compact("lang", "tu", "hypn", "ka", "title", "array_test"), "pythonista");