<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701172743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX `primary` ON actor_program');
        $this->addSql('ALTER TABLE actor_program ADD PRIMARY KEY (program_id, actor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON actor_program');
        $this->addSql('ALTER TABLE actor_program ADD PRIMARY KEY (actor_id, program_id)');
        $this->addSql('ALTER TABLE program DROP slug');
    }
}
