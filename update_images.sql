-- Update initial product images with real Unsplash URLs for Raga Marketplace
-- These are the products from the screenshot (IDs 1-5)

UPDATE products SET image_url = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=500&q=80' WHERE title = 'T-Shirt';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80' WHERE title = 'Sneakers';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&q=80' WHERE title = 'Headphones';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=500&q=80' WHERE title = 'Laptop';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=500&q=80' WHERE title = 'Textbook';
