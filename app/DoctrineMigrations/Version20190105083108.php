<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190105083108 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFABF396750');
        $this->addSql('ALTER TABLE posts ADD author INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFABDAFD8C8 FOREIGN KEY (author) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFABDAFD8C8 ON posts (author)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFABDAFD8C8');
        $this->addSql('DROP INDEX UNIQ_885DBAFABDAFD8C8 ON posts');
        $this->addSql('ALTER TABLE posts DROP author');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFABF396750 FOREIGN KEY (id) REFERENCES users (id)');
    }
}
