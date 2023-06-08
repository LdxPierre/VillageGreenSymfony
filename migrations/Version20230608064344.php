<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608064344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, date DATE NOT NULL, shipping_date DATE NOT NULL, shipping_name VARCHAR(100) NOT NULL, shipping_address VARCHAR(100) NOT NULL, shipping_city VARCHAR(70) NOT NULL, shipping_zip_code VARCHAR(15) NOT NULL, shipping_country VARCHAR(70) NOT NULL, billing_name VARCHAR(100) NOT NULL, billing_address VARCHAR(100) NOT NULL, billing_city VARCHAR(70) NOT NULL, billing_zip_code VARCHAR(15) NOT NULL, billing_country VARCHAR(70) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `order`');
    }
}
