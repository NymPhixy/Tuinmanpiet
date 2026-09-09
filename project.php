<?php
require_once __DIR__ . '/includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: projecten.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT * 
    FROM projects 
    WHERE id = :id AND status = 'published' 
    LIMIT 1
");

$stmt->execute([
    'id' => $id
]);

$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: projecten.php');
    exit;
}

$imageStmt = $pdo->prepare("
    SELECT * 
    FROM project_images 
    WHERE project_id = :project_id 
    ORDER BY created_at ASC
");

$imageStmt->execute([
    'project_id' => $id
]);

$projectImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project['title']) ?> - Tuinman Piet</title>
    <meta 
        name="description" 
        content="<?= htmlspecialchars(mb_strimwidth($project['description'] ?? 'Project van Tuinman Piet in Musselkanaal en omgeving.', 0, 155, '...')) ?>"
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
    <section class="project-detail-hero">
        <div class="container project-detail-grid">
            <div class="project-detail-text">
                <a href="projecten.php" class="back-link">← Terug naar projecten</a>

                <p class="eyebrow"><?= htmlspecialchars($project['category']) ?></p>

                <h1><?= htmlspecialchars($project['title']) ?></h1>

                <?php if (!empty($project['location'])): ?>
                    <p class="project-detail-location">
                        Locatie: <?= htmlspecialchars($project['location']) ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($project['project_date'])): ?>
                    <p class="project-detail-location">
                        Uitgevoerd op: <?= htmlspecialchars(date('d-m-Y', strtotime($project['project_date']))) ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($project['description'])): ?>
                    <p class="project-detail-description">
                        <?= nl2br(htmlspecialchars($project['description'])) ?>
                    </p>
                <?php endif; ?>

                <div class="project-detail-actions">
                    <a href="tel:+31653797745" class="btn btn-primary">Bel Piet</a>
                    <a href="https://wa.me/31653797745" class="btn btn-secondary">Stuur WhatsApp</a>
                </div>

                <p class="phone-note">
                    Direct contact: <strong>06 53797745</strong>
                </p>
            </div>

            <div class="project-detail-cover">
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
        </div>
    </section>

    <section class="project-gallery-section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Foto’s</p>
                <h2>Projectfoto’s</h2>
                <p>Bekijk meerdere foto’s die bij dit project horen.</p>
            </div>

            <?php if (empty($project['cover_image']) && empty($projectImages)): ?>
                <div class="empty-state">
                    <h2>Nog geen foto’s</h2>
                    <p>Bij dit project zijn nog geen foto’s geplaatst.</p>
                </div>
            <?php else: ?>
                <div class="project-gallery-grid">
                    <?php if (!empty($project['cover_image'])): ?>
                        <button 
                            class="gallery-item" 
                            type="button"
                            data-image="<?= htmlspecialchars($project['cover_image']) ?>"
                            data-title="<?= htmlspecialchars($project['title']) ?>"
                        >
                            <img 
                                src="<?= htmlspecialchars($project['cover_image']) ?>" 
                                alt="<?= htmlspecialchars($project['title']) ?>"
                            >
                        </button>
                    <?php endif; ?>

                    <?php foreach ($projectImages as $image): ?>
                        <button 
                            class="gallery-item" 
                            type="button"
                            data-image="<?= htmlspecialchars($image['image_path']) ?>"
                            data-title="<?= htmlspecialchars($image['alt_text'] ?: $project['title']) ?>"
                        >
                            <img 
                                src="<?= htmlspecialchars($image['image_path']) ?>" 
                                alt="<?= htmlspecialchars($image['alt_text'] ?: $project['title']) ?>"
                            >
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<div class="lightbox" id="lightbox" aria-hidden="true">
    <div class="lightbox-backdrop" data-close-lightbox></div>

    <div class="lightbox-content" role="dialog" aria-modal="true" aria-label="Projectfoto bekijken">
        <button class="lightbox-close" type="button" data-close-lightbox aria-label="Sluit foto">
            ×
        </button>

        <img id="lightboxImage" src="" alt="">
        <p id="lightboxTitle"></p>
    </div>
</div>

<footer class="site-footer">
    <div class="container footer-inner">
        <p>© 2026 Tuinman Piet</p>
        <p>Website door RGB Visuals</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>