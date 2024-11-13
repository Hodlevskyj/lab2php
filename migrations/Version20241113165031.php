<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113165031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, tourist_id INT NOT NULL, tour_id INT NOT NULL, booking_date DATETIME NOT NULL, number_of_people INT NOT NULL, total_price NUMERIC(10, 2) NOT NULL, INDEX IDX_E00CEDDEEC61B273 (tourist_id), INDEX IDX_E00CEDDE15ED8D43 (tour_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE destination (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, country VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guide (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(15) DEFAULT NULL, language VARCHAR(50) NOT NULL, bio LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_date DATETIME NOT NULL, status VARCHAR(50) DEFAULT NULL, INDEX IDX_6D28840D3301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_method (id INT AUTO_INCREMENT NOT NULL, method VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, tour_id INT NOT NULL, tourist_id INT NOT NULL, rating INT NOT NULL, comment LONGTEXT NOT NULL, review_date DATETIME NOT NULL, INDEX IDX_794381C615ED8D43 (tour_id), INDEX IDX_794381C6EC61B273 (tourist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, duration INT NOT NULL, price NUMERIC(8, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour_guide (id INT AUTO_INCREMENT NOT NULL, tour_id INT NOT NULL, guide_id INT DEFAULT NULL, INDEX IDX_F1D1D71D15ED8D43 (tour_id), INDEX IDX_F1D1D71DD7ED1D4B (guide_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tourist (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(15) DEFAULT NULL, registration_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, role_name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEEC61B273 FOREIGN KEY (tourist_id) REFERENCES tourist (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE15ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C615ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6EC61B273 FOREIGN KEY (tourist_id) REFERENCES tourist (id)');
        $this->addSql('ALTER TABLE tour_guide ADD CONSTRAINT FK_F1D1D71D15ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('ALTER TABLE tour_guide ADD CONSTRAINT FK_F1D1D71DD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEEC61B273');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE15ED8D43');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D3301C60');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C615ED8D43');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6EC61B273');
        $this->addSql('ALTER TABLE tour_guide DROP FOREIGN KEY FK_F1D1D71D15ED8D43');
        $this->addSql('ALTER TABLE tour_guide DROP FOREIGN KEY FK_F1D1D71DD7ED1D4B');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE destination');
        $this->addSql('DROP TABLE guide');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tour');
        $this->addSql('DROP TABLE tour_guide');
        $this->addSql('DROP TABLE tourist');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
