# ORTHIA PHP Template engine
The simply PHP template engine

## 概要
フレームワーク学習の一環でテンプレートエンジンを作成しました。
htmlテンプレート内で変数展開・関数の使用を行うことができます。  

## 制御構文
制御構文は以下のような形で記述します。   
```
{% if(1 == 1) %}
    <p>同じ数字です！</p>
{% endif %}
```

twigやPythonのjinjaに良く似た記述方式で制御構文を記述できます。  
また、ユーザー自身で制御構文を定義することもできます。  
このような複数行に渡る制御構文を「ブロック関数」と呼びます。  

## ワンライナー構文
通常ifやfor等の制御構文を一行で書くことはできませんが、以下のような書き方をすることで一行での制御構文を実現できます。  
```
<p style="color: {% if(isset($err)) | red | else() | blue | endif() %}">
```
このような一行で完結する制御構文のことを「ワンライナー構文」と呼びます。  

## キャッシュ
現在テンプレートのキャッシュ機能は装備していませんが、今後の開発で実装する予定です。