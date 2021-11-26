<?php

namespace Max\Routing;

class Url
{

    /**
     * 别名
     *
     * @var array
     */
    protected static array $alias = [];

    /**
     * 使用url获取路由别名
     *
     * @param $url
     *
     * @return false|int|string
     */
    public static function getAliasByUri($url)
    {
        if (false !== ($key = array_search($url, static::$alias))) {
            return $key;
        }
        return null;
    }

    public static function set(string $alias, string $uri)
    {
        // TODO 重复alias
        static::$alias[$alias] = $uri;
    }

    /**
     * 获取路由别名
     *
     * @param string $alias
     * @param array  $args
     *
     * @return mixed|string
     * @throws \Exception
     */
    public static function build(string $alias, array $args = [])
    {
        if (isset(static::$alias[$alias])) {
            if (preg_match('/\(.+\)/i', static::$alias[$alias])) {
                $rep = explode(',', preg_replace(['#\\\#', '#\(.+\)#Ui'], ['', ','], static::$alias[$alias]));
                if (($argNums = count($rep) - 1) != count($args)) {
                    throw new \Exception("别名:{$alias}需要传入{$argNums}个参数！");
                }
                $match = '';
                $args  = array_values($args);
                foreach ($rep as $k => $r) {
                    $match .= ($r . ($args[$k] ?? ''));
                }
                return $match;
            }
            return static::$alias[$alias];
        }
        return $alias;
    }
}
