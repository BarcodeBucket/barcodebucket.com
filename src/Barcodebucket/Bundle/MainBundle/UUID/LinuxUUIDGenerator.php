<?php

namespace Barcodebucket\Bundle\MainBundle\UUID;

/**
 * Class LinuxUUIDGenerator
 * @package BarcodeBucket\Data
 */
class LinuxUUIDGenerator implements UUIDGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return trim(`uuidgen -r`);
    }
}
