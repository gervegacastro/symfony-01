<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418142852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E3F63963D');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EAF98C6D4');
        $this->addSql('DROP INDEX IDX_1F1B251E3F63963D ON item');
        $this->addSql('DROP INDEX IDX_1F1B251EAF98C6D4 ON item');
        $this->addSql('ALTER TABLE item ADD producto_id INT NOT NULL, ADD sale_id INT NOT NULL, DROP producto_id_id, DROP sale_id_id');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E7645698E FOREIGN KEY (producto_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E7645698E ON item (producto_id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E4A7E4868 ON item (sale_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E7645698E');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E4A7E4868');
        $this->addSql('DROP INDEX IDX_1F1B251E7645698E ON item');
        $this->addSql('DROP INDEX IDX_1F1B251E4A7E4868 ON item');
        $this->addSql('ALTER TABLE item ADD producto_id_id INT NOT NULL, ADD sale_id_id INT NOT NULL, DROP producto_id, DROP sale_id');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E3F63963D FOREIGN KEY (producto_id_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EAF98C6D4 FOREIGN KEY (sale_id_id) REFERENCES sale (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1F1B251E3F63963D ON item (producto_id_id)');
        $this->addSql('CREATE INDEX IDX_1F1B251EAF98C6D4 ON item (sale_id_id)');
    }
}
