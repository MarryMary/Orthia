# ORTHIA PHP Template engine
The simply PHP template engine

## about
This template engine is included in ClearSky PHP Framework.  
This package is released for use this only.  

## install
```
% composer require mary/orthia
```

## usage

'{{ }}' is mean the bind variable.   
e.g.↓  
```
{{ $variable }}
```

  
'{% %}' is mean the control syntax and function.  
e.g.↓  
```
{% if($foo == $bar) %}
    <p>true!</p>
{% endif %}
{% csrf_token %}
```

end keyword's shape is surly end<function_name> of control syntax.  

## OL function (OneLiner control syntax) usage

OL() is special function.  
usualy, control syntax is over multiple lines.  
But OL function can write the control syntax in one line.  
e.g.↓  
```
{% OL(if($foo == $bar) | foo = bar; else | foo not equal bar; endif) %}
```

## 補足
Japaneseの皆さんこんにちは。  
今回勉強のために車輪の再発明という大罪を犯しつつ「ClearSky」というフレームワークを作成しました。  
このフレームワークは勉強のための開発なので、「0から10までほぼ自分だけで開発」という目標を掲げて開発を進めていきました。  
その流れでテンプレートエンジンを作ってみたのですが、割と小規模開発ではサラッと使えちゃいそうな気がしたので単独で公開してみました。  
キャッシュ作成機能だけはどうしようもありませんでしたが、パラメータの安全なバインドや制御構文は普通に使えるレベルに到達していますので、よければ是非。  

