<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210613165929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE list_entry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE list_entry (id INT NOT NULL, list_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(1024) DEFAULT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE todo_list ADD title VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE list_entry_id_seq CASCADE');
        $this->addSql('DROP TABLE list_entry');
        $this->addSql('ALTER TABLE todo_list DROP title');
    }
}
