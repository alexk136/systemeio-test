<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201160914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Вставка данных о странах и налоговых ставках';
    }

    public function up(Schema $schema): void
    {
		$this->addSql("INSERT INTO country_tax (country_name, country_code, tax) VALUES ('Германия', 'DE', 19.0)");
		$this->addSql("INSERT INTO country_tax (country_name, country_code, tax) VALUES ('Италия', 'IT', 22.0)");
		$this->addSql("INSERT INTO country_tax (country_name, country_code, tax) VALUES ('Франция', 'FR', 20.0)");
		$this->addSql("INSERT INTO country_tax (country_name, country_code, tax) VALUES ('Греция', 'GR', 24.0)");

	}

    public function down(Schema $schema): void
    {

    }
}
