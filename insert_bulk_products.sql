-- Bulk Insert Script for Raga Marketplace Demo
-- Adds a diverse range of items: Tech, Home, and Style

-- 1. Ensure a demo store exists if none do
INSERT IGNORE INTO sellers (seller_id, full_name, email, contact_number, password, address)
VALUES (999, 'System Demo', 'demo@raga.test', '0000000000', '$2y$10$YCo6gC.7E2e/p1OqMpxNneZ5vU5H2K4k5O8B.XyL8M8T1.', 'Rhodes University');

INSERT IGNORE INTO stores (id, seller_id, store_name, description, location)
VALUES (999, 999, 'Raga- Official Store', 'Curated products for the campus community.', 'Main Library');

-- 2. Clear then Insert diverse products (using Unsplash for high quality)
DELETE FROM products WHERE store_id = 999;

INSERT INTO products (store_id, title, description, price, stock_quantity, category, image_url) VALUES
-- TECH CATEGORY
(999, 'Apple Magic Keyboard', 'Minimalist wireless keyboard. Like new condition.', 1200.00, 2, 'Electronics', 'https://images.unsplash.com/photo-1587829741301-dc798b83aca2?q=80&w=800'),
(999, 'Sony WH-1000XM4', 'Noise cancelling headphones. Black finish.', 4500.00, 1, 'Electronics', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=800'),
(999, 'Fujifilm X100V', 'Silver body travel camera. 26.1MP.', 18000.00, 1, 'Electronics', 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=800'),

-- HOME/FURNITURE CATEGORY
(999, 'Minimal Desk Lamp', 'Matte black metal finish. Adjustable neck.', 450.00, 5, 'Home', 'https://images.unsplash.com/photo-1534073828943-f801091bb18c?q=80&w=800'),
(999, 'Potted Snake Plant', 'Easy maintenance indoor plant in ceramic pot.', 150.00, 10, 'Home', 'https://images.unsplash.com/photo-1512428813834-c702c7702b78?q=80&w=800'),
(999, 'Herman Miller Chair', 'Ergonomic office chair. Graphite frame.', 9500.00, 2, 'Furniture', 'https://images.unsplash.com/photo-1505843490701-5be5d0b19d58?q=80&w=800'),

-- ACCESSORIES/STYLE CATEGORY
(999, 'Leather Tote Bag', 'Handcrafted tan leather. Perfect for books.', 850.00, 4, 'Accessories', 'https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=800'),
(999, 'Casio F-91W Watch', 'Classic digital watch. Water resistant.', 350.00, 15, 'Accessories', 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?q=80&w=800'),
(999, 'Wool Beanie', 'Soft grey knit. One size fits all.', 120.00, 20, 'Clothing', 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?q=80&w=800'),

-- BOOKS CATEGORY
(999, 'Design for Everyday Things', 'Don Norman. Must read for UX students.', 290.00, 3, 'Books', 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?q=80&w=800');
