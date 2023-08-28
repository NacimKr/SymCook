<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828174729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes ADD recette_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C89312FE9 FOREIGN KEY (recette_id) REFERENCES recettes (id)');
        $this->addSql('CREATE INDEX IDX_11BA68C89312FE9 ON notes (recette_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C89312FE9');
        $this->addSql('DROP INDEX IDX_11BA68C89312FE9 ON notes');
        $this->addSql('ALTER TABLE notes DROP recette_id, DROP created_at');
    }
}
