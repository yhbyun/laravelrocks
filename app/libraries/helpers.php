<?php

/**
 * 변수가 설정되어 있으면 그 값을 반환하고, 그렇지 않으면 value(default는 null)를 반환한다.
 *
 * @param $var
 * @param null $value
 * @return null
 */
function get_if_set(&$var, $value = null) {
    if (isset($var)) {
        return $var;
    }
    return $value;
}