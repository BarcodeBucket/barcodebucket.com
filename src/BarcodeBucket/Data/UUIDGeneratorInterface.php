<?php
namespace BarcodeBucket\Data;

interface UUIDGeneratorInterface
{
    /**
     * @return string UUID
     */
    public function generate();
}
