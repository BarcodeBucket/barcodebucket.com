<?php

namespace BarcodeBucket\Data;

class LinuxUUIDGenerator implements UUIDGeneratorInterface
{
    public function generate()
    {
        return `uuidgen -r`;
    }
}
