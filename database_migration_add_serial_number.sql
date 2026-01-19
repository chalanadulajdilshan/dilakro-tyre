-- Add serial_number column to sales_invoice_items table
ALTER TABLE `sales_invoice_items` ADD COLUMN `serial_number` VARCHAR(255) NULL AFTER `next_service_date`;
