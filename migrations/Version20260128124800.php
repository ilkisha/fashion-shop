<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create support_ticket table
 */
final class Version20260128124800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create support_ticket table with all required fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE support_ticket (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            order_id INT DEFAULT NULL,
            subject VARCHAR(50) NOT NULL,
            message LONGTEXT NOT NULL,
            status VARCHAR(20) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            admin_note LONGTEXT DEFAULT NULL,
            INDEX IDX_1F5A4D53A76ED395 (user_id),
            INDEX IDX_1F5A4D538D9F6D38 (order_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE support_ticket ADD CONSTRAINT FK_1F5A4D53A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE support_ticket ADD CONSTRAINT FK_1F5A4D538D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53A76ED395');
        $this->addSql('ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D538D9F6D38');
        $this->addSql('DROP TABLE support_ticket');
    }
}
