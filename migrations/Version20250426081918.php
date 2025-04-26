<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426081918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__shift_role AS SELECT id, shift_id, role_name, quantity FROM shift_role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift_role
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift_role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, role_name VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_6250F5A5BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO shift_role (id, shift_id, role_name, quantity) SELECT id, shift_id, role_name, quantity FROM __temp__shift_role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__shift_role
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6250F5A5BB70BC0E ON shift_role (shift_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__staff_profile AS SELECT id, user_id, name, surname, position, phone FROM staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE staff_profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) NOT NULL, position VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, CONSTRAINT FK_DDE1BDB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO staff_profile (id, user_id, name, surname, position, phone) SELECT id, user_id, name, surname, position, phone FROM __temp__staff_profile
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
            CREATE TEMPORARY TABLE __temp__shift_role AS SELECT id, shift_id, role_name, quantity FROM shift_role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift_role
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift_role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, role_name VARCHAR(50) NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_6250F5A5BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO shift_role (id, shift_id, role_name, quantity) SELECT id, shift_id, role_name, quantity FROM __temp__shift_role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__shift_role
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6250F5A5BB70BC0E ON shift_role (shift_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__staff_profile AS SELECT id, user_id, name, surname, position, phone FROM staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE staff_profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) NOT NULL, position VARCHAR(50) NOT NULL, phone VARCHAR(20) NOT NULL, CONSTRAINT FK_DDE1BDB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO staff_profile (id, user_id, name, surname, position, phone) SELECT id, user_id, name, surname, position, phone FROM __temp__staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__staff_profile
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DDE1BDB9A76ED395 ON staff_profile (user_id)
        SQL);
    }
}
