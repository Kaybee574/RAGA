-- Raga Marketplace | Image Recovery Script
-- This script updates common products to use high-availability Unsplash CDN images.

-- Fix for T-Shirt
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1000&q=80' 
WHERE title LIKE '%T-Shirt%' OR title LIKE '%T Shirt%';

-- Fix for Sneakers
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1000&q=80' 
WHERE title LIKE '%Sneakers%' OR title LIKE '%Shoes%';

-- Fix for Headphones
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=80' 
WHERE title LIKE '%Headphones%';

-- Fix for Laptop
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1000&q=80' 
WHERE title LIKE '%Laptop%';

-- Fix for Textbook
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80' 
WHERE title LIKE '%Textbook%' OR title LIKE '%Book%';

-- Fix for any other empty or likely broken placeholders
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?auto=format&fit=crop&w=1000&q=80'
WHERE image_url IS NULL OR image_url = '' OR image_url LIKE '%placeholder.com%';
