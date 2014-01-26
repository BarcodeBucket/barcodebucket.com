<?php

namespace Barcodebucket\Bundle\MainBundle\Cache;

use Zend\Cache\Storage\StorageInterface;

/**
 * Class CacheFactory
 * @package Barcodebucket\Bundle\MainBundle\Cache
 */
class CacheFactory
{
    /**
     * @return StorageInterface
     */
    public function get()
    {
        return \Zend\Cache\StorageFactory::factory(array(
            'adapter' => array(
                'name'    => 'filesystem',
                'options' => array('ttl' => 3600),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => false),
            ),
        ));
    }
}
