<?php

namespace Max\Routing;

class Url
{

    /**
     * 别名
     *
     * @var array
     */
    protected $alias = [];

    /**
     * 使用url获取路由别名
     *
     * @param $url
     *
     * @return false|int|string
     */
    public function getAliasByUri($url)
    {
        if (false !== ($key = array_search($url, $this->alias))) {
            return $key;
        }
        return null;
    }

    /**
     * 获取路由别名
     *
     * @param string $alias
     * @param array  $args
     *
     * @return mixed|string
     * @throws Exception
     */
    public function getAlias(string $alias, array $args = [])
    {
        if (isset($this->alias[$alias])) {
            if (preg_match('/\(.+\)/i', $this->alias[$alias])) {
                $rep = explode(',', preg_replace(['#\\\#', '#\(.+\)#Ui'], ['', ','], $this->alias[$alias]));
                if (($argNums = count($rep) - 1) != count($args)) {
                    throw new Exception("别名:{$alias}需要传入{$argNums}个参数！");
                }
                $match = '';
                $args  = array_values($args);
                foreach ($rep as $k => $r) {
                    $match .= ($r . ($args[$k] ?? ''));
                }
                return $match;
            }
            return $this->alias[$alias];
        }
        return $alias;
    }
}