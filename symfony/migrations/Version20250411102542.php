<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411102542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP CONSTRAINT fk_6eeaa67d19eb6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6eeaa67d19eb6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP user_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT fk_6eeaa67d19eb6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_6eeaa67d19eb6921 ON commande (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD user_id INT NOT NULL
        SQL);
    }
}
