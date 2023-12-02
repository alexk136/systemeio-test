<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Config\CouponType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202074015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add coupons';
    }

    public function up(Schema $schema): void
    {
		$couponType = CouponType::Normal->value;
		$this->addSql("INSERT INTO coupon (code, coupon_type, amount, percentage, active, created_at) VALUES ('D15', $couponType, 0, 10, 1, CURRENT_TIMESTAMP)");

		$couponType = CouponType::NeNormal->value;
		$this->addSql("INSERT INTO coupon (code, coupon_type, amount, percentage, active, created_at) VALUES ('D16', $couponType, 10, 0, 1, CURRENT_TIMESTAMP)");


	}

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
