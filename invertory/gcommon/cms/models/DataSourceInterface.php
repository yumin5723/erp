<?php
namespace gcommon\cms\models;

interface DataSourceInterface
{
    public function getData($params = null);
}
