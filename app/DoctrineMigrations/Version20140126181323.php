<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140126181323 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE barcodes (uuid CHAR(36) PRIMARY KEY, barcode CHAR(14) UNIQUE)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE barcodes');
    }
}
