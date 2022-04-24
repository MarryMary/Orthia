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
`` 

