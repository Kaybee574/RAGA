-- Raga Marketplace | Master Image Restoration Script 🚀
-- Updates all demo products with high-resolution, real-world Unsplash images.

-- 1. Clothing & Style
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1200' WHERE title LIKE '%T-Shirt%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200' WHERE title LIKE '%Sneakers%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=1200' WHERE title LIKE '%Bag%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?q=80&w=1200' WHERE title LIKE '%Watch%';

-- 2. Tech & Electronics
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200' WHERE title LIKE '%Headphones%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1200' WHERE title LIKE '%Laptop%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1587829741301-dc798b83aca2?q=80&w=1200' WHERE title LIKE '%Keyboard%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1200' WHERE title LIKE '%Camera%';

-- 3. Home & Furniture
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1534073828943-f801091bb18c?q=80&w=1200' WHERE title LIKE '%Lamp%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1512428813834-c702c7702b78?q=80&w=1200' WHERE title LIKE '%Plant%';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505843490701-5be5d0b19d58?q=80&w=1200' WHERE title LIKE '%Chair%';

-- 4. Education & Books
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?q=80&w=1200' WHERE title LIKE '%Textbook%' OR title LIKE '%Book%';

-- 5. Catch-all: Update any remaining broken placeholders with a high-quality lifestyle shot
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=1200' 
WHERE image_url LIKE '%placeholder%' OR image_url IS NULL OR image_url = '';
