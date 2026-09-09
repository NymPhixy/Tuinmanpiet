<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("
    SELECT *
    FROM projects
    WHERE status = 'published'
    ORDER BY created_at DESC
");

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = ['Alles', 'Aanleg', 'Onderhoud', 'Renovatie', 'Bestrating', 'Schuttingen', 'Snoeiwerk', 'Overig'];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projecten - Tuinman Piet</title>
    <meta 
        name="description" 
        content="Bekijk projecten en tuinwerk van Tuinman Piet in Musselkanaal en omgeving."
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
            <a href="index.php">Home</a>
            <a href="index.php#werkzaamheden">Werkzaamheden</a>
            <a href="projecten.php" class="active">Projecten</a>
            <a href="index.php#over">Over Piet</a>
            <a href="index.php#contact">Contact</a>
        </div>
    </nav>
</header>

<main>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">Portfolio</p>
            <h1>Projecten van Tuinman Piet</h1>
            <p>
                Bekijk voorbeelden van aanleg, onderhoud, renovatie en ander tuinwerk
                in Musselkanaal en omgeving.
            </p>
        </div>
    </section>

    <section class="projects-section">
        <div class="container">

            <div class="filter-bar" aria-label="Projectfilters">
                <?php foreach ($categories as $category): ?>
                    <button 
                        class="filter-btn <?= $category === 'Alles' ? 'active' : '' ?>" 
                        data-filter="<?= htmlspecialchars($category) ?>"
                        type="button"
                    >
                        <?= htmlspecialchars($category) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <h2>Nog geen projecten geplaatst</h2>
                    <p>Binnenkort worden hier gepubliceerde projecten van Tuinman Piet getoond.</p>
                </div>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($projects as $project): ?>
                        <article 
                            class="project-card" 
                            data-category="<?= htmlspecialchars($project['category']) ?>"
                        >
                            <a href="project.php?id=<?= (int) $project['id'] ?>" class="project-card-link">
                                <div class="project-card-image">
                                    <?php if (!empty($project['cover_image'])): ?>
                                        <img 
                                            src="<?= htmlspecialchars($project['cover_image']) ?>" 
                                            alt="<?= htmlspecialchars($project['title']) ?>"
                                        >
                                    <?php else: ?>
                                        <div class="public-image-placeholder">
                                            Geen afbeelding
                                        </div>
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

                                    <?php if (!empty($project['project_date'])): ?>
                                        <p class="project-date">
                                            Uitgevoerd op: <?= htmlspecialchars(date('d-m-Y', strtotime($project['project_date']))) ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($project['description'])): ?>
                                        <p>
                                            <?= htmlspecialchars(mb_strimwidth($project['description'], 0, 120, '...')) ?>
                                        </p>
                                    <?php endif; ?>

                                    <span class="text-link">Bekijk project →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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