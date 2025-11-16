<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114113606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parameter_dependencies (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, parameter_value_id INTEGER NOT NULL, allowed_parameter_value_id INTEGER NOT NULL, CONSTRAINT FK_5B7A0D434584665A FOREIGN KEY (product_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5B7A0D431452663E FOREIGN KEY (parameter_value_id) REFERENCES parameter_values (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5B7A0D43A2E57282 FOREIGN KEY (allowed_parameter_value_id) REFERENCES parameter_values (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5B7A0D434584665A ON parameter_dependencies (product_id)');
        $this->addSql('CREATE INDEX IDX_5B7A0D431452663E ON parameter_dependencies (parameter_value_id)');
        $this->addSql('CREATE INDEX IDX_5B7A0D43A2E57282 ON parameter_dependencies (allowed_parameter_value_id)');
        $this->addSql('CREATE TABLE parameter_values (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parameter_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(50) NOT NULL, CONSTRAINT FK_DED946177C56DBD6 FOREIGN KEY (parameter_id) REFERENCES parameters (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DED946177C56DBD6 ON parameter_values (parameter_id)');
        $this->addSql('CREATE TABLE parameters (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_69348FE77153098 ON parameters (code)');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE parameter_dependencies');
        $this->addSql('DROP TABLE parameter_values');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE products');
    }
}
