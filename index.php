<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("
    SELECT *
    FROM projects
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 3
");

$latestProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuinman Piet - Tuinwerk in Musselkanaal en omgeving</title>
    <meta 
        name="description" 
        content="Tuinman Piet helpt met aanleg, onderhoud en renovatie van tuinen in Musselkanaal en omgeving."
    >
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar">
        <a href="index.php" class="brand brand-logo">
            <img src="assets/img/logo.png" alt="Tuinman Piet logo">
        </a>

        <button class="menu-toggle" type="button" aria-label="Menu openen" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-links" id="navLinks">
            <a href="index.php" class="active">Home</a>
            <a href="#werkzaamheden">Werkzaamheden</a>
            <a href="projecten.php">Projecten</a>
            <a href="#over">Over Piet</a>
            <a href="#contact">Contact</a>
        </div>
    </nav>
</header>

<main>
    <section class="home-hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <p class="eyebrow">Tuinwerk in Musselkanaal en omgeving</p>
                <h1>Tuinman Piet</h1>
                <p class="hero-subtitle">
                    Voor aanleg, onderhoud en renovatie van tuinen. Praktisch, betrouwbaar en met oog voor net werk.
                </p>

                <div class="hero-actions">
                    <a href="tel:+31653797745" class="btn btn-primary">Bel Piet</a>
                    <a href="https://wa.me/31653797745" class="btn btn-secondary">Stuur WhatsApp</a>
                </div>

                <p class="phone-note">
                    Direct contact: <strong>06 53797745</strong>
                </p>
            </div>

            <div class="hero-image-card">
                <div class="hero-image-placeholder">
                    <span>Projectfoto Tuinman Piet</span>
                </div>
            </div>
        </div>
    </section>

    <section class="services-section" id="werkzaamheden">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Werkzaamheden</p>
                <h2>Wat Piet doet</h2>
                <p>
                    Van onderhoud tot renovatie: Tuinman Piet helpt met praktisch en netjes tuinwerk.
                </p>
            </div>

            <div class="services-grid">
                <article class="service-card">
                    <span class="service-icon">🌱</span>
                    <h3>Tuinaanleg</h3>
                    <p>Hulp bij het aanleggen en indelen van tuinen, borders en nette buitenruimtes.</p>
                </article>

                <article class="service-card">
                    <span class="service-icon">✂️</span>
                    <h3>Tuinonderhoud</h3>
                    <p>Onderhoudswerk zoals snoeien, opruimen, netjes maken en periodieke verzorging.</p>
                </article>

                <article class="service-card">
                    <span class="service-icon">🛠️</span>
                    <h3>Tuinrenovatie</h3>
                    <p>Bestaande tuinen opknappen, herstellen en weer overzichtelijk maken.</p>
                </article>

                <article class="service-card">
                    <span class="service-icon">🧱</span>
                    <h3>Bestrating</h3>
                    <p>Klein straatwerk, herbestrating en nette afwerking rondom de tuin.</p>
                </article>

                <article class="service-card">
                    <span class="service-icon">🪵</span>
                    <h3>Schuttingen</h3>
                    <p>Plaatsen en herstellen van schuttingen, houtwerk en praktische tuinoplossingen.</p>
                </article>

                <article class="service-card">
                    <span class="service-icon">🌳</span>
                    <h3>Snoeiwerk</h3>
                    <p>Snoeien van struiken, hagen en groen zodat de tuin weer verzorgd oogt.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="projects-preview-section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Portfolio</p>
                <h2>Recent werk</h2>
                <p>
                    Bekijk voorbeelden van projecten en werkzaamheden van Tuinman Piet.
                </p>
            </div>

            <?php if (empty($latestProjects)): ?>
                <div class="empty-state">
                    <h2>Nog geen projecten geplaatst</h2>
                    <p>Wanneer Piet projecten publiceert, verschijnen de nieuwste voorbeelden hier automatisch.</p>
                </div>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($latestProjects as $project): ?>
                        <article class="project-card" data-category="<?= htmlspecialchars($project['category']) ?>">
                            <a href="project.php?id=<?= (int) $project['id'] ?>" class="project-card-link">
                                <div class="project-card-image">
                                    <?php if (!empty($project['cover_image'])): ?>
                                        <img 
                                            src="<?= htmlspecialchars($project['cover_image']) ?>" 
                                            alt="<?= htmlspecialchars($project['title']) ?>"
                                        >
                                    <?php else: ?>
                                        <div class="public-image-placeholder">Geen afbeelding</div>
                                    <?php endif; ?>
                                </div>

                                <div class="project-card-content">
                                    <span class="project-label">
                                        <?= htmlspecialchars($project['category']) ?>
                                    </span>

                                    <h2><?= htmlspecialchars($project['title']) ?></h2>

                                    <?php if (!empty($project['location'])): ?>
                                        <p class="project-location">
                                            <?= htmlspecialchars($project['location']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($project['description'])): ?>
                                        <p>
                                            <?= htmlspecialchars(mb_strimwidth($project['description'], 0, 110, '...')) ?>
                                        </p>
                                    <?php endif; ?>

                                    <span class="text-link">Bekijk project →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="center-action">
                <a href="projecten.php" class="btn btn-primary">Bekijk alle projecten</a>
            </div>
        </div>
    </section>

    <section class="about-section" id="over">
        <div class="container about-grid">
            <div>
                <p class="eyebrow">Over Piet</p>
                <h2>Praktisch tuinwerk zonder gedoe</h2>
                <p>
                    Tuinman Piet helpt particulieren in Musselkanaal en omgeving met aanleg,
                    onderhoud en renovatie van tuinen. De aanpak is duidelijk en gericht op netjes werk.
                </p>
                <p>
                    Geen ingewikkelde verkooppraatjes, maar gewoon meedenken, aanpakken en zorgen dat de tuin er weer goed bij ligt.
                </p>
            </div>

            <div class="about-card">
                <h3>Werkgebied</h3>
                <p>Musselkanaal en omgeving</p>

                <h3>Contact</h3>
                <p>Het snelst bereikbaar via bellen of WhatsApp.</p>
                <p class="phone-note">
                    Telefoon: <strong>06 53797745</strong>
                </p>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="container contact-card">
            <p class="eyebrow">Contact</p>
            <h2>Neem contact op met Piet</h2>
            <p>
                Heeft u een tuinproject of wilt u overleggen over onderhoud, aanleg of renovatie?
                Neem eenvoudig contact op via telefoon of WhatsApp.
            </p>

            <div class="hero-actions">
                <a href="tel:+31653797745" class="btn btn-primary">Bel Piet</a>
                <a href="https://wa.me/31653797745" class="btn btn-secondary">Stuur WhatsApp</a>
            </div>

            <p class="phone-note">
                Liever bellen? <strong>06 53797745</strong>
            </p>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <p>© 2026 Tuinman Piet</p>
        <p>Website door RGB Visuals</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>