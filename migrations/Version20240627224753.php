<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240627224753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique index on title column in program table';
    }

    public function up(Schema $schema): void
    {
        // Check if the index already exists before attempting to create it
        if (!$schema->getTable('program')->hasIndex('UNIQ_92ED77842B36786B')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_92ED77842B36786B ON program (title)');
        }
    }

    public function down(Schema $schema): void
    {
        // Drop the index if it exists
        if ($schema->getTable('program')->hasIndex('UNIQ_92ED77842B36786B')) {
            $this->addSql('DROP INDEX UNIQ_92ED77842B36786B ON program');
        }
    }
}
