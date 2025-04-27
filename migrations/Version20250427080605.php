<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427080605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__shift_position AS SELECT id, shift_id, position, quantity FROM shift_position
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift_position
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift_position (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_6F987C51BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO shift_position (id, shift_id, name, quantity) SELECT id, shift_id, position, quantity FROM __temp__shift_position
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__shift_position
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6F987C51BB70BC0E ON shift_position (shift_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__shift_position AS SELECT id, shift_id, name, quantity FROM shift_position
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift_position
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift_position (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, position VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_6F987C51BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO shift_position (id, shift_id, position, quantity) SELECT id, shift_id, name, quantity FROM __temp__shift_position
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__shift_position
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6F987C51BB70BC0E ON shift_position (shift_id)
        SQL);
    }
}
