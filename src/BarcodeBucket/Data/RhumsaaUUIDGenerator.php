<?php
namespace BarcodeBucket\Data;

use Rhumsaa\Uuid\Uuid;

class RhumsaaUUIDGenerator implements UUIDGeneratorInterface
{
    /**
     * @return string UUID
     */
    public function generate()
    {
        return Uuid::uuid4()->toString();
    }
}
