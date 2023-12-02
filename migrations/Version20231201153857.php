<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201153857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Вставка товаров';
    }

    public function up(Schema $schema): void
    {
		// Вставка продуктов в таблицу
		$this->addSql("INSERT INTO product (title, price, created_at) VALUES ('Iphone', 100.00, CURRENT_TIMESTAMP)");
		$this->addSql("INSERT INTO product (title, price, created_at) VALUES ('Наушники', 20.00, CURRENT_TIMESTAMP)");
		$this->addSql("INSERT INTO product (title, price, created_at) VALUES ('Чехол', 10.00, CURRENT_TIMESTAMP)");
	}

    public function down(Schema $schema): void
    {

    }
}
