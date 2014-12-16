<?php
namespace gcommon\cms\components\filters;
use gcommon\cms\components\filters\CmsFilter;
class DateFilter extends CmsFilter {

    public function filter($value, $options=array()) {
        if (is_string($value)) {
            $t = strtotime($value);
        }
        return date("Y-m-d", $t);
    }

}