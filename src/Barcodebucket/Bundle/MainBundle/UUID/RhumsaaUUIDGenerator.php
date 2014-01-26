<?php
namespace Barcodebucket\Bundle\MainBundle\UUID;

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
