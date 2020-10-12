<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201022215424 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE shop_delivery_methods (
id UUID NOT NULL,
type VARCHAR(255) NOT NULL,
title VARCHAR(255) NOT NULL,
min_price INT DEFAULT NULL,
cost INT DEFAULT NULL, 
PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN shop_delivery_methods.id IS \'(DC2Type:shop_delivery_method_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_delivery_methods.type IS \'(DC2Type:shop_delivery_method_type)\'');
        $this->addSql('CREATE TABLE shop_orders (
id INT NOT NULL,
payment_method_id UUID NOT NULL,
delivery_method_id UUID NOT NULL,
status VARCHAR(255) NOT NULL,
progress INT NOT NULL,
created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
cost INT NOT NULL,
note TEXT DEFAULT NULL,
version INT DEFAULT 1 NOT NULL,
customer_email VARCHAR(255) NOT NULL,
customer_phone VARCHAR(255) NOT NULL,
customer_name VARCHAR(255) NOT NULL,
customer_city VARCHAR(255) DEFAULT NULL,
customer_street VARCHAR(255) DEFAULT NULL,
customer_house VARCHAR(255) DEFAULT NULL,
customer_apartment VARCHAR(255) DEFAULT NULL,
PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_608DDB6C5AA1164F ON shop_orders (payment_method_id)');
        $this->addSql('CREATE INDEX IDX_608DDB6C5DED75F5 ON shop_orders (delivery_method_id)');
        $this->addSql('COMMENT ON COLUMN shop_orders.id IS \'(DC2Type:shop_order_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_orders.payment_method_id IS \'(DC2Type:shop_payment_method_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_orders.delivery_method_id IS \'(DC2Type:shop_delivery_method_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_orders.status IS \'(DC2Type:shop_order_status)\'');
        $this->addSql('COMMENT ON COLUMN shop_orders.created IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE shop_order_items (
order_id INT NOT NULL,
product_id INT NOT NULL,
PRIMARY KEY(order_id, product_id))');
        $this->addSql('CREATE INDEX IDX_B0F76BA08D9F6D38 ON shop_order_items (order_id)');
        $this->addSql('CREATE INDEX IDX_B0F76BA04584665A ON shop_order_items (product_id)');
        $this->addSql('COMMENT ON COLUMN shop_order_items.order_id IS \'(DC2Type:shop_order_id)\'');
        $this->addSql('COMMENT ON COLUMN shop_order_items.product_id IS \'(DC2Type:shop_product_id)\'');
        $this->addSql('CREATE TABLE shop_payment_methods (
id UUID NOT NULL,
title VARCHAR(255) NOT NULL,
priority INT DEFAULT NULL,
PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN shop_payment_methods.id IS \'(DC2Type:shop_payment_method_id)\'');
        $this->addSql('ALTER TABLE shop_orders ADD CONSTRAINT FK_608DDB6C5AA1164F FOREIGN KEY (payment_method_id) REFERENCES shop_payment_methods (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_orders ADD CONSTRAINT FK_608DDB6C5DED75F5 FOREIGN KEY (delivery_method_id) REFERENCES shop_delivery_methods (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_order_items ADD CONSTRAINT FK_B0F76BA08D9F6D38 FOREIGN KEY (order_id) REFERENCES shop_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_order_items ADD CONSTRAINT FK_B0F76BA04584665A FOREIGN KEY (product_id) REFERENCES shop_products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shop_orders DROP CONSTRAINT FK_608DDB6C5DED75F5');
        $this->addSql('ALTER TABLE shop_order_items DROP CONSTRAINT FK_B0F76BA08D9F6D38');
        $this->addSql('ALTER TABLE shop_orders DROP CONSTRAINT FK_608DDB6C5AA1164F');
        $this->addSql('DROP TABLE shop_delivery_methods');
        $this->addSql('DROP TABLE shop_orders');
        $this->addSql('DROP TABLE shop_order_items');
        $this->addSql('DROP TABLE shop_payment_methods');
    }
}
