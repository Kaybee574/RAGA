<?php
/**
 * About Us Page - Minimal Editorial Style
 * Describes the vision and community focus of Raga Marketplace.
 */
include 'includes/header.php';
?>

<div style="padding: 6% 4%;">
    <div style="margin-bottom: 6rem; border-bottom: 1px solid black; padding-bottom: 2rem;">
        <h1 style="font-size: 5rem; letter-spacing: -4px; line-height: 0.9;">ABOUT<br>RAGA-</h1>
        <p style="font-size: 0.8rem; letter-spacing: 2px; color: #888; font-weight: 700; margin-top: 2rem;">ESTABLISHED 2026. RHODES UNIVERSITY.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem;">
        <div>
            <h2 style="font-size: 2rem; margin-bottom: 2rem; font-weight: 300;">OUR VISION-</h2>
            <p style="line-height: 1.8; text-transform: none; color: #333; margin-bottom: 2rem;">
                Raga was born out of a simple need: a safer, more transparent way for students at Rhodes University to buy and sell items within their own community. No more meeting strangers from the internet; just verified campus peer-to-peer trading.
            </p>
            <p style="line-height: 1.8; text-transform: none; color: #333;">
                We believe in circular fashion, sustainable tech consumption, and most importantly, looking after our fellow students' pockets.
            </p>
        </div>
        
        <div style="border-left: 1px solid black; padding-left: 4rem;">
            <h2 style="font-size: 2rem; margin-bottom: 2rem; font-weight: 300;">THE ETHOS-</h2>
            <ul style="list-style: none; line-height: 2.5; font-weight: 700; font-size: 0.9rem;">
                <li>01. CAMPUS FIRST-</li>
                <li>02. RADICAL TRANSPARENCY-</li>
                <li>03. MINIMALIST TRADING-</li>
                <li>04. COMMUNITY DRIVEN-</li>
            </ul>
        </div>
    </div>

    <!-- High Impact Visual Block -->
    <div style="margin-top: 8rem; border: 1px solid black; height: 60vh; position: relative; overflow: hidden;">
        <img src="https://images.unsplash.com/photo-1541339907198-e08756ebafe1?q=80&w=1600" alt="Rhodes Campus Style" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
            <p style="font-size: 3rem; color: white; text-align: center; font-weight: 900; text-shadow: 2px 2px 0 black;">JOIN THE<br>MOVEMENT-</p>
        </div>
    </div>

    <div style="margin-top: 4rem; text-align: center;">
        <a href="register.php" class="btn btn-primary" style="padding: 1.5rem 4rem; font-size: 1rem;">JOIN NOW-</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
