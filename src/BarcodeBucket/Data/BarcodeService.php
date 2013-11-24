<?php

namespace BarcodeBucket\Data;

use Doctrine\DBAL\Connection;

class BarcodeService
{
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
}
