-- Raga Marketplace | Definitive Seeding & Image Restoration 🚀
-- Populates the marketplace with a diverse, high-quality catalog for final testing.

-- 1. Setup Master Store (If not exists)
INSERT IGNORE INTO sellers (seller_id, full_name, email, contact_number, password, address)
VALUES (101, 'Raga Collections', 'curator@raga.test', '081 234 5678', '$2y$10$YCo6gC.7E2e/p1OqMpxNneZ5vU5H2K4k5O8B.XyL8M8T1.', 'Rhodes Campus');

INSERT IGNORE INTO stores (id, seller_id, store_name, description, location)
VALUES (101, 101, 'Raga- Signature', 'The finest curation of campus essentials.', 'Main Campus');

-- 2. Clear then Seed Diverse Catalog (High-Res Unsplash Only)
DELETE FROM products WHERE store_id = 101;

INSERT INTO products (store_id, title, description, price, stock_quantity, category, image_url) VALUES
-- APPAREL & STYLE
(101, 'Heritage T-Shirt', 'Classic white pocket tee. 100% Cotton.', 250.00, 15, 'Clothing', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1200'),
(101, 'Minimal Sneakers', 'Flat sole canvas sneakers in matte black.', 850.00, 8, 'Clothing', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200'),
(101, 'Canvas Tote Bag', 'Durable, high-capacity student tote.', 180.00, 20, 'Accessories', 'https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=1200'),
(101, 'Digital Retro Watch', 'Stainless steel vintage style timepiece.', 450.00, 5, 'Accessories', 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?q=80&w=1200'),
(101, 'Wool Knit Beanie', 'Soft textured winter essential.', 120.00, 25, 'Clothing', 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?q=80&w=1200'),

-- TECH & GADGETS
(101, 'Apple Wireless Keyboard', 'Compact, responsive Bluetooth typing.', 1400.00, 3, 'Electronics', 'https://images.unsplash.com/photo-1587829741301-dc798b83aca2?q=80&w=1200'),
(101, 'Sony Noise-Cancelling Headphones', 'Industry leading audio quality.', 4200.00, 2, 'Electronics', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200'),
(101, 'Fujifilm Travel Camera', 'Compact mirrorless excellence.', 16500.00, 1, 'Electronics', 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1200'),
(101, 'MacBook Pro 14"', 'M2 Chip, 16GB RAM. Perfect for power users.', 28000.00, 1, 'Electronics', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200'),
(101, 'Gaming Mouse', 'RGB lighting, 12K DPI sensitivity.', 750.00, 6, 'Electronics', 'https://images.unsplash.com/photo-1527814732934-7658fc34873f?q=80&w=1200'),

-- ACADEMICS & OFFICE
(101, 'Scientific Calculator', 'Complex 500+ function support.', 350.00, 12, 'Electronics', 'https://images.unsplash.com/photo-1588600030303-023007604ce0?q=80&w=1200'),
(101, 'Algorithm Design Textbook', 'Current edition for Computer Science.', 550.00, 4, 'Books', 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?q=80&w=1200'),
(101, 'Matte Black Desk Lamp', 'Adjustable LED studio illumination.', 680.00, 5, 'Home', 'https://images.unsplash.com/photo-1534073828943-f801091bb18c?q=80&w=1200'),
(101, 'Premium Notebook Set', 'Linen cover, high-GSM paper.', 220.00, 15, 'Books', 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?q=80&w=1200'),

-- LIFESTYLE
(101, 'Hydro Flask', 'Vacuum insulated 32oz bottle.', 480.00, 10, 'Accessories', 'https://images.unsplash.com/photo-1602143399827-bd9349449f1a?q=80&w=1200'),
(101, 'Ceramic Plant Pot', 'Minimalist white with snake plant included.', 250.00, 8, 'Home', 'https://images.unsplash.com/photo-1512428813834-c702c7702b78?q=80&w=1200');

-- 3. Cleanup existing broken/placeholder products from old runs
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=1200' 
WHERE image_url LIKE '%placeholder%' OR image_url IS NULL OR image_url = '';

-- Ensure core products from previous screens are updated to real images
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200' WHERE title = 'SNEAKERS';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1200' WHERE title = 'T-SHIRT';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200' WHERE title = 'HEADPHONES';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200' WHERE title = 'LAPTOP';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?q=80&w=1200' WHERE title = 'TEXTBOOK';
