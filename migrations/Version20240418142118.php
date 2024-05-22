<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418142118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, producto_id_id INT NOT NULL, sale_id_id INT NOT NULL, quantity INT NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_1F1B251E3F63963D (producto_id_id), INDEX IDX_1F1B251EAF98C6D4 (sale_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E3F63963D FOREIGN KEY (producto_id_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EAF98C6D4 FOREIGN KEY (sale_id_id) REFERENCES sale (id)');
        $this->addSql('ALTER TABLE user CHANGE is_delete isdelete TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E3F63963D');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EAF98C6D4');
        $this->addSql('DROP TABLE item');
        $this->addSql('ALTER TABLE user CHANGE isdelete is_delete TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
