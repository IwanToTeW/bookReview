<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190105081943 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts ADD postedAt DATETIME NOT NULL, DROP author, CHANGE $reviewtext reviewText VARCHAR(255) DEFAULT NULL, CHANGE image helpful INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFABF396750 FOREIGN KEY (id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFABF396750');
        $this->addSql('ALTER TABLE posts ADD author INT NOT NULL, DROP postedAt, CHANGE reviewtext $reviewText VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE helpful image INT DEFAULT NULL');
    }
}
