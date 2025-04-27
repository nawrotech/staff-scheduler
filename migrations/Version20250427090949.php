<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427090949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__assignment AS SELECT id, shift_id, staff_profile_id, shift_role_id, assigned_at, status FROM assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assignment
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assignment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, staff_profile_id INTEGER NOT NULL, shift_position_id INTEGER NOT NULL, assigned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , status VARCHAR(255) NOT NULL, CONSTRAINT FK_30C544BABB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30C544BA2AA80269 FOREIGN KEY (staff_profile_id) REFERENCES staff_profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30C544BA7661AC54 FOREIGN KEY (shift_position_id) REFERENCES shift_position (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO assignment (id, shift_id, staff_profile_id, shift_position_id, assigned_at, status) SELECT id, shift_id, staff_profile_id, shift_role_id, assigned_at, status FROM __temp__assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__assignment
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BA2AA80269 ON assignment (staff_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BABB70BC0E ON assignment (shift_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BA7661AC54 ON assignment (shift_position_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__assignment AS SELECT id, shift_id, staff_profile_id, shift_position_id, assigned_at, status FROM assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assignment
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assignment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, shift_id INTEGER NOT NULL, staff_profile_id INTEGER NOT NULL, shift_role_id INTEGER NOT NULL, assigned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , status VARCHAR(20) NOT NULL, CONSTRAINT FK_30C544BABB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30C544BA2AA80269 FOREIGN KEY (staff_profile_id) REFERENCES staff_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30C544BABB521ED5 FOREIGN KEY (shift_role_id) REFERENCES shift_position (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO assignment (id, shift_id, staff_profile_id, shift_role_id, assigned_at, status) SELECT id, shift_id, staff_profile_id, shift_position_id, assigned_at, status FROM __temp__assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__assignment
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BABB70BC0E ON assignment (shift_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BA2AA80269 ON assignment (staff_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BABB521ED5 ON assignment (shift_role_id)
        SQL);
    }
}
