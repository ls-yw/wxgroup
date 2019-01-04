<?php
namespace Library;

use Phalcon\DI;

class Redis
{
    public $obj;
    private static $_instance = null;

    /**
     * @return \Redis|null
     */
    public static function getInstance($config=[])
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance->init($config);
    }

    /**
     * Redis constructor.
     */
    public function init($config)
    {
        $redisConfig = empty($config) ? DI::getDefault()->get('config')->redis->toArray() : $config;
        //Log::write('pack', 'redis配置：' . json_encode($redisConfig, JSON_UNESCAPED_UNICODE), 'redis_config');
        try {
            $this->obj = new \Redis();
            $this->obj->connect($redisConfig['default']['host'], $redisConfig['default']['port']);
//             $this->obj = new \RedisCluster(NULL, $redisConfig['default']['host']);

            if ($this->obj) {
                $connect = true;
            } else {
                trigger_error('redis|redis连接失败，host：'.json_encode($redisConfig['default']['host'], JSON_UNESCAPED_UNICODE));
                return false;
                $connect = false;
            }
        } catch(\Exception $e){
            trigger_error('redis|'.$e->getMessage());
            return false;
        }
        
        //设置前缀
        $this->obj->setOption(\Redis::OPT_PREFIX, $redisConfig['default']['prefix']);

        return $this->obj;
    }

    /**
     * @param $key
     * @param $expire
     * @return array|void
     */
    public static function lock(string $key, int $expire = 0)
    {
        if ($expire !== 0) {
            $extend = array('nx', 'ex' => $expire);
        }
        return self::getInstance()->set($key, 1, $extend);
    }
    
}