<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

$menu = [
    ['id' => 1, 'name' => 'Burrata & Heirloom Tomato', 'category' => 'Starters', 'description' => 'Creamy burrata, sun-ripened tomatoes, basil oil, grilled sourdough.', 'price' => 13, 'image' => 'photo-1608039829572-78524f79c4c7'],
    ['id' => 2, 'name' => 'Crispy Calamari', 'category' => 'Starters', 'description' => 'Lightly fried calamari, lemon, parsley, and our house marinara.', 'price' => 15, 'image' => 'photo-1599487488170-d11ec9c172f0'],
    ['id' => 3, 'name' => 'Wild Mushroom Risotto', 'category' => 'Mains', 'description' => 'Arborio rice, woodland mushrooms, parmesan, a little white wine.', 'price' => 24, 'image' => 'photo-1476124369491-e7addf5db371'],
    ['id' => 4, 'name' => 'Sunday Roast Chicken', 'category' => 'Mains', 'description' => 'Herb-roasted chicken, buttery mash, seasonal greens, pan jus.', 'price' => 27, 'image' => 'photo-1532550907401-a500c9a57435'],
    ['id' => 5, 'name' => 'The AnnSatu Burger', 'category' => 'Mains', 'description' => 'Grass-fed beef, aged cheddar, onion jam, brioche, hand-cut fries.', 'price' => 22, 'image' => 'photo-1568901346375-23c9450c58cd'],
    ['id' => 6, 'name' => 'Seared Salmon', 'category' => 'Mains', 'description' => 'Crisp-skinned salmon, lemon butter, asparagus, spring peas.', 'price' => 28, 'image' => 'photo-1467003909585-2f8a72700288'],
    ['id' => 7, 'name' => 'Olive Oil Cake', 'category' => 'Desserts', 'description' => 'Citrus-soaked cake, whipped mascarpone, seasonal berries.', 'price' => 11, 'image' => 'photo-1578985545062-69928b1d9587'],
    ['id' => 8, 'name' => 'Warm Chocolate Tart', 'category' => 'Desserts', 'description' => 'Dark chocolate ganache, flaky sea salt, vanilla bean gelato.', 'price' => 12, 'image' => 'photo-1563805042-7684c019e1cb'],
    ['id' => 9, 'name' => 'Garden Spritz', 'category' => 'Drinks', 'description' => 'Elderflower, fresh cucumber, sparkling wine, a little sunshine.', 'price' => 14, 'image' => 'photo-1513558161293-cdaf765edfd7'],
    ['id' => 10, 'name' => 'House Lemonade', 'category' => 'Drinks', 'description' => 'Freshly squeezed lemons, garden mint, just enough sparkle.', 'price' => 7, 'image' => 'photo-1513558161293-cdaf765edfd7'],
];
$csrf = csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8f6f1">
    <meta name="description" content="A neighborhood table for good food and good company. Explore the AnnSatu menu, order online, or book a table.">
    <title>AnnSatu — Good food, good company</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="#home" aria-label="AnnSatu home"><span class="brand-mark">a.</span><span>annsatu<span class="brand-period">.</span></span></a>
    <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false"><span></span><span></span></button>
    <nav class="main-nav" aria-label="Main navigation">
        <a href="#menu">Our menu</a><a href="#story">Our story</a><a href="#visit">Find us</a>
        <a class="nav-book" href="#visit">Book a table <span aria-hidden="true">↗</span></a>
    </nav>
</header>

<main>
    <section class="hero" id="home">
        <div class="hero-copy">
            <p class="eyebrow"><span class="eyebrow-line"></span> YOUR NEIGHBORHOOD TABLE</p>
            <h1>Good food.<br>Better <em>together.</em></h1>
            <p class="hero-description">Seasonal plates, a well-poured glass, and a seat waiting just for you. Come as you are.</p>
            <div class="hero-actions"><a class="button button-dark" href="#menu">Explore our menu <span aria-hidden="true">↗</span></a><a class="text-link" href="#visit">Book a table <span aria-hidden="true">→</span></a></div>
            <div class="hero-note"><span class="note-stars" aria-label="5 stars">★★★★★</span><span>Gather around. Stay a little longer.</span></div>
        </div>
        <div class="hero-visual">
            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&amp;fit=crop&amp;w=1400&amp;q=85" alt="A beautifully set table ready for dinner" fetchpriority="high">
            <div class="hero-sticker"><span>MADE WITH</span><strong>♥</strong><span>SHARED WITH YOU</span></div>
            <div class="image-caption"><span class="caption-dot"></span> FRESH FROM OUR KITCHEN, EVERY DAY</div>
        </div>
        <div class="hero-scroll" aria-hidden="true">SCROLL TO SAVOR <span>↓</span></div>
    </section>

    <section class="intro-strip" id="story">
        <span class="strip-label">A LITTLE ABOUT US</span>
        <p>At AnnSatu, we believe the best memories start with <em>“pass the plate.”</em> Thoughtful ingredients, familiar flavors, and always room for one more.</p>
        <span class="strip-sun" aria-hidden="true">✳</span>
    </section>

    <section class="menu-section section-wrap" id="menu">
        <div class="section-heading">
            <div><p class="eyebrow"><span class="eyebrow-line"></span> SOMETHING FOR EVERYONE</p><h2>A seat at <em>our table.</em></h2></div>
            <p class="section-aside">Thoughtfully sourced. Happily served.<br>Made fresh, right here.</p>
        </div>
        <div class="menu-toolbar">
            <div class="menu-tabs" role="group" aria-label="Filter menu">
                <button class="menu-tab is-active" type="button" data-category="All">All the good stuff</button>
                <button class="menu-tab" type="button" data-category="Starters">To start</button>
                <button class="menu-tab" type="button" data-category="Mains">The main event</button>
                <button class="menu-tab" type="button" data-category="Desserts">Something sweet</button>
                <button class="menu-tab" type="button" data-category="Drinks">To sip</button>
            </div>
            <button class="cart-button" type="button" data-open-cart aria-label="Open your order"><span aria-hidden="true">♧</span> Your order <span class="cart-count">0</span></button>
        </div>
        <div class="menu-grid">
            <?php foreach ($menu as $item): ?>
                <article class="menu-card" data-menu-card data-category="<?= e($item['category']) ?>">
                    <div class="menu-image"><img src="https://images.unsplash.com/<?= e($item['image']) ?>?auto=format&amp;fit=crop&amp;w=700&amp;q=80" alt="<?= e($item['name']) ?>" loading="lazy"><span class="menu-category"><?= e($item['category']) ?></span><button class="add-item" type="button" data-add-item="<?= (int) $item['id'] ?>" aria-label="Add <?= e($item['name']) ?> to your order">+</button></div>
                    <div class="menu-item-heading"><h3><?= e($item['name']) ?></h3><span>$<?= number_format((float) $item['price'], 2) ?></span></div>
                    <p><?= e($item['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="menu-footnote">Have an allergy or dietary question? <a href="#visit">Just ask us</a> — we’re happy to help.</p>
    </section>

    <section class="quote-band">
        <span class="quote-mark" aria-hidden="true">“</span>
        <p>There is no sincerer love than the love of food.</p>
        <span class="quote-credit">— GEORGE BERNARD SHAW, PROBABLY THINKING ABOUT DINNER</span>
    </section>

    <section class="visit-section section-wrap" id="visit">
        <div class="visit-copy">
            <p class="eyebrow"><span class="eyebrow-line"></span> COME ON IN</p>
            <h2>Make yourself<br><em>at home.</em></h2>
            <p class="visit-description">A long lunch, a last-minute dinner, a reason to celebrate — we’ll save you a place.</p>
            <div class="visit-details"><div><span class="detail-icon">◷</span><span><strong>Good times</strong><br>Mon–Fri, 11am–10pm<br>Sat–Sun, 9am–10pm</span></div><div><span class="detail-icon">⌖</span><span><strong>Right around the corner</strong><br>42 Garden Lane, Your City<br><a href="mailto:hello@annsatu.com">hello@annsatu.com</a></span></div></div>
        </div>
        <div class="booking-stack"><form class="booking-card" data-api-form>
            <input type="hidden" name="action" value="reservation"><input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <p class="eyebrow"><span class="eyebrow-line"></span> SAVE YOUR SEAT</p>
            <h3>Let’s make it a date.</h3>
            <label>Your name<input name="name" type="text" autocomplete="name" placeholder="How should we greet you?" required maxlength="100"></label>
            <div class="field-row"><label>Date<input name="date" type="date" required></label><label>Time<select name="time" required><option value="">Pick a time</option><option>11:00 AM</option><option>12:00 PM</option><option>1:00 PM</option><option>5:00 PM</option><option>6:00 PM</option><option>7:00 PM</option><option>8:00 PM</option><option>9:00 PM</option></select></label></div>
            <div class="field-row"><label>Your crew<select name="guests" required><option value="1">Just me</option><option value="2" selected>2 people</option><option value="3">3 people</option><option value="4">4 people</option><option value="5">5 people</option><option value="6">6 people</option><option value="7">7 people</option><option value="8">8 people</option></select></label><label>Phone<input name="phone" type="tel" autocomplete="tel" placeholder="For the confirmation" required maxlength="30"></label></div>
            <button class="button button-dark button-full" type="submit">Find us a table <span aria-hidden="true">→</span></button><p class="form-message" aria-live="polite"></p>
            <p class="booking-note">For parties of 9 or more, give us a call. We’ll find a way.</p>
        </form>
        <form class="cancel-booking-form" data-api-form><input type="hidden" name="action" value="cancel_reservation"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><p class="cancel-booking-title">Plans change. We understand.</p><div class="field-row"><label>Reservation number<input name="reservation_id" type="number" min="1" required placeholder="From your confirmation"></label><label>Phone number<input name="phone" type="tel" required maxlength="30" placeholder="Used to book"></label></div><button class="text-link" type="submit">Cancel a reservation →</button><p class="form-message" aria-live="polite"></p></form></div>
    </section>

    <section class="feedback-section">
        <div class="feedback-inner"><div><p class="eyebrow"><span class="eyebrow-line"></span> A NOTE FROM YOU</p><h2>How was your <em>visit?</em></h2><p>We’re always learning. Tell us what made your day (or what we could do better).</p></div>
            <form class="feedback-form" data-api-form><input type="hidden" name="action" value="feedback"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><label class="sr-only" for="feedback-name">Your name</label><input id="feedback-name" name="name" placeholder="Your name" required maxlength="100"><label class="sr-only" for="feedback-email">Your email (optional)</label><input id="feedback-email" name="email" type="email" placeholder="Your email (optional)" maxlength="190"><label class="sr-only" for="feedback-message">Your note</label><textarea id="feedback-message" name="message" rows="3" placeholder="Leave us a little note..." required maxlength="1500"></textarea><button class="button button-light" type="submit">Send a little love <span aria-hidden="true">↗</span></button><p class="form-message" aria-live="polite"></p></form>
        </div>
    </section>
</main>

<footer class="site-footer"><a class="brand brand-footer" href="#home"><span class="brand-mark">a.</span><span>annsatu<span class="brand-period">.</span></span></a><span>GOOD FOOD. BETTER TOGETHER.</span><span>© <?= date('Y') ?> AnnSatu</span><a href="admin.php">Staff sign in <span aria-hidden="true">↗</span></a></footer>

<div class="cart-overlay" data-cart-overlay hidden></div><aside class="cart-drawer" data-cart-drawer aria-label="Your order" aria-hidden="true"><div class="cart-header"><div><p class="eyebrow">MADE JUST FOR YOU</p><h2>Your order</h2></div><button class="cart-close" type="button" data-close-cart aria-label="Close order">×</button></div><div class="cart-content" data-cart-content></div><div class="cart-checkout" data-cart-checkout hidden><div class="cart-total"><span>Your total</span><strong data-cart-total>$0.00</strong></div><form data-order-form><input type="hidden" name="action" value="order"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><label>Name<input name="name" autocomplete="name" required maxlength="100" placeholder="Your name"></label><label>Phone<input name="phone" type="tel" autocomplete="tel" required maxlength="30" placeholder="Phone number"></label><label>Email <span>(optional)</span><input name="email" type="email" autocomplete="email" maxlength="190" placeholder="you@example.com"></label><label>Order type<select name="fulfillment" data-fulfillment><option value="pickup">Pick up</option><option value="delivery">Delivery</option></select></label><label data-address-label hidden>Delivery address<input name="address" maxlength="255" placeholder="Street address"></label><label>Anything else? <span>(optional)</span><textarea name="notes" rows="2" maxlength="500" placeholder="Allergies, extra napkins..."></textarea></label><button class="button button-dark button-full" type="submit">Place my order <span aria-hidden="true">→</span></button><p class="form-message" aria-live="polite"></p></form></div></aside>
<div class="toast" data-toast role="status" aria-live="polite"></div>
<script>window.ANNSATU_MENU = <?= json_encode($menu, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<script src="app.js" defer></script>
</body>
</html>
