<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190105133857 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE books ADD reviews VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFACBE5A331');
        $this->addSql('DROP INDEX IDX_885DBAFACBE5A331 ON posts');
        $this->addSql('ALTER TABLE posts CHANGE book book_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA16A2B381 FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_885DBAFA16A2B381 ON posts (book_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE books DROP reviews');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA16A2B381');
        $this->addSql('DROP INDEX IDX_885DBAFA16A2B381 ON posts');
        $this->addSql('ALTER TABLE posts CHANGE book_id book INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFACBE5A331 FOREIGN KEY (book) REFERENCES books (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_885DBAFACBE5A331 ON posts (book)');
    }
}
