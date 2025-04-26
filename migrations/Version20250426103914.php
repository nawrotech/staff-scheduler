<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426103914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__shift AS SELECT id, date, start_time, end_time, notes FROM shift
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATE NOT NULL --(DC2Type:date_immutable)
            , start_time TIME NOT NULL --(DC2Type:time_immutable)
            , end_time TIME NOT NULL --(DC2Type:time_immutable)
            , notes CLOB DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO shift (id, date, start_time, end_time, notes) SELECT id, date, start_time, end_time, notes FROM __temp__shift
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__shift
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__staff_profile AS SELECT id, user_id, name, position, phone FROM staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE staff_profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, position VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, CONSTRAINT FK_DDE1BDB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO staff_profile (id, user_id, name, position, phone) SELECT id, user_id, name, position, phone FROM __temp__staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DDE1BDB9A76ED395 ON staff_profile (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__shift AS SELECT id, date, start_time, end_time, notes FROM shift
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , start_time TIME NOT NULL --(DC2Type:time_immutable)
            , end_time TIME NOT NULL --(DC2Type:time_immutable)
            , notes CLOB DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO shift (id, date, start_time, end_time, notes) SELECT id, date, start_time, end_time, notes FROM __temp__shift
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__shift
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__staff_profile AS SELECT id, user_id, name, position, phone FROM staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE staff_profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, position VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, surname VARCHAR(100) NOT NULL, CONSTRAINT FK_DDE1BDB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO staff_profile (id, user_id, name, position, phone) SELECT id, user_id, name, position, phone FROM __temp__staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DDE1BDB9A76ED395 ON staff_profile (user_id)
        SQL);
    }
}
