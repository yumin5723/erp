<?php

function merge_array($a,$b)
{
    $args=func_get_args();
    $res=array_shift($args);
    while(!empty($args))
    {
        $next=array_shift($args);
        foreach($next as $k => $v)
        {
            if(is_integer($k))
                isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
            else if(is_array($v) && isset($res[$k]) && is_array($res[$k]))
                $res[$k]= merge_array($res[$k],$v);
            else
                $res[$k]=$v;
        }
    }
    return $res;
}

/**
 * function_description
 *
 * @param $config_file:
 * @param $suffix:
 *
 * @return
 */
function require_with_local($config_file, $suffix='-local', $force=false) {
    $p = strrpos($config_file, ".");
    $pre = substr($config_file, 0, $p);
    $post = substr($config_file,$p);
    $local_config_file = $pre.$suffix.$post;
    if (is_file($local_config_file)) {
        return merge_array(require($config_file), require($local_config_file));
    } elseif ($force) {
        trigger_error("Cannot find file:".$local_config_file, E_USER_ERROR);
    } else {
        return require($config_file);
    }
}
