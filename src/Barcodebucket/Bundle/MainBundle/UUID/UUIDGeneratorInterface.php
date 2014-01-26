<?php
namespace Barcodebucket\Bundle\MainBundle\UUID;

/**
 * Interface UUIDGeneratorInterface
 * @package Barcodebucket\Bundle\MainBundle\UUID
 */
interface UUIDGeneratorInterface
{
    /**
     * @return string UUID
     */
    public function generate();
}
