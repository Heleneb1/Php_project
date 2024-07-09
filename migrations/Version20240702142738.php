<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702142738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('episode');
        if (!$table->hasColumn('duration')) {
            $this->addSql('ALTER TABLE episode ADD duration INT NOT NULL');
        }
    }
    

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('episode');
        if ($table->hasColumn('duration')) {
            $this->addSql('ALTER TABLE episode DROP duration');
        }
    }
    
}
