<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201015202227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE shop_categories (
id UUID NOT NULL, 
slug VARCHAR(255) NOT NULL, 
title VARCHAR(255) NOT NULL, 
version INT DEFAULT 1 NOT NULL, 
PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN shop_categories.id IS \'(DC2Type:shop_category_id)\'');
        $this->addSql('CREATE TABLE shop_product_photo (
id UUID NOT NULL,
product_id INT DEFAULT NULL,
photo_path VARCHAR(255) NOT NULL,
photo_title VARCHAR(255) NOT NULL, 
photo_size INT NOT NULL, 
PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ABF7684B4584665A ON shop_product_photo (product_id)');
        $this->addSql('COMMENT ON COLUMN shop_product_photo.id IS \'(DC2Type:shop_product_photo_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_product_photo.product_id IS \'(DC2Type:shop_product_id)\'');
        $this->addSql('CREATE TABLE shop_products (
id INT NOT NULL,
title VARCHAR(255) NOT NULL,
price INT NOT NULL,
version INT DEFAULT 1 NOT NULL,
PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN shop_products.id IS \'(DC2Type:shop_product_id)\'');
        $this->addSql('CREATE TABLE shop_category_product (product_id INT NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(product_id, category_id))');
        $this->addSql('CREATE INDEX IDX_35CF43624584665A ON shop_category_product (product_id)');
        $this->addSql('CREATE INDEX IDX_35CF436212469DE2 ON shop_category_product (category_id)');
        $this->addSql('COMMENT ON COLUMN shop_category_product.product_id IS \'(DC2Type:shop_product_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_category_product.category_id IS \'(DC2Type:shop_category_id)\'');
        $this->addSql('ALTER TABLE shop_product_photo ADD CONSTRAINT FK_ABF7684B4584665A FOREIGN KEY (product_id) REFERENCES shop_products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_category_product ADD CONSTRAINT FK_35CF43624584665A FOREIGN KEY (product_id) REFERENCES shop_products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_category_product ADD CONSTRAINT FK_35CF436212469DE2 FOREIGN KEY (category_id) REFERENCES shop_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shop_category_product DROP CONSTRAINT FK_35CF436212469DE2');
        $this->addSql('ALTER TABLE shop_product_photo DROP CONSTRAINT FK_ABF7684B4584665A');
        $this->addSql('ALTER TABLE shop_category_product DROP CONSTRAINT FK_35CF43624584665A');
        $this->addSql('DROP TABLE shop_categories');
        $this->addSql('DROP TABLE shop_product_photo');
        $this->addSql('DROP TABLE shop_products');
        $this->addSql('DROP TABLE shop_category_product');
    }
}
