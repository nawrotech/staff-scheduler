<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426065733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE assignment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, staff_profile_id INTEGER NOT NULL, assigned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_30C544BABB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30C544BA2AA80269 FOREIGN KEY (staff_profile_id) REFERENCES staff_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BABB70BC0E ON assignment (shift_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BA2AA80269 ON assignment (staff_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE availability (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, staff_profile_id INTEGER NOT NULL, week_start DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_slots CLOB NOT NULL --(DC2Type:json)
            , CONSTRAINT FK_3FB7A2BF2AA80269 FOREIGN KEY (staff_profile_id) REFERENCES staff_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3FB7A2BF2AA80269 ON availability (staff_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, sent_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , type VARCHAR(50) NOT NULL, payload CLOB NOT NULL --(DC2Type:json)
            , CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , start_time TIME NOT NULL --(DC2Type:time_immutable)
            , end_time TIME NOT NULL --(DC2Type:time_immutable)
            , notes CLOB DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE staff_profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) NOT NULL, position VARCHAR(50) NOT NULL, phone VARCHAR(20) NOT NULL, CONSTRAINT FK_DDE1BDB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DDE1BDB9A76ED395 ON staff_profile (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE availability
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE staff_profile
        SQL);
    }
}
