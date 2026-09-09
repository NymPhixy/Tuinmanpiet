<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$stmt = $pdo->query("
    SELECT projects.*, users.name AS creator_name
    FROM projects
    LEFT JOIN users ON projects.created_by = users.id
    ORDER BY projects.updated_at DESC
");

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projecten beheren - Tuinman Piet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-page">

<header class="admin-header">
    <div>
        <h1>Dashboard</h1>
        <p>Welkom, <?= htmlspecialchars($userName ?? currentUserName()) ?>.</p>
    </div>

   <div class="admin-header-actions">
    <a href="../index.php" class="btn btn-outline" target="_blank">Bekijk website</a>
    <a href="logout.php" class="btn btn-secondary">Uitloggen</a>
</div>
</header>

<main class="admin-layout">
    <aside class="admin-sidebar">
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="project-toevoegen.php">Project toevoegen</a>
            <a href="projecten-beheren.php" class="active">Projecten beheren</a>
        </nav>
    </aside>

    <section class="admin-content admin-content-wide">
        <div class="dashboard-card">
            <div class="admin-section-header">
                <div>
                    <h2>Alle projecten / klussen</h2>
                    <p><?= count($projects) ?> project(en) gevonden.</p>
                </div>

                <a href="project-toevoegen.php" class="btn btn-primary">Nieuw project</a>
            </div>

            <?php if (empty($projects)): ?>
                <p>Er zijn nog geen projecten toegevoegd.</p>
            <?php else: ?>
                <div class="admin-project-list">
                    <?php foreach ($projects as $project): ?>
                        <article class="admin-project-item admin-project-item-large">
                            <div class="admin-project-image">
                                <?php if (!empty($project['cover_image'])): ?>
                                    <img 
                                        src="../<?= htmlspecialchars($project['cover_image']) ?>" 
                                        alt="<?= htmlspecialchars($project['title']) ?>"
                                    >
                                <?php else: ?>
                                    <div class="image-placeholder">Geen afbeelding</div>
                                <?php endif; ?>
                            </div>

                            <div class="admin-project-info">
                                <div class="admin-project-title-row">
                                    <h3><?= htmlspecialchars($project['title']) ?></h3>

                                    <span class="status-badge status-<?= htmlspecialchars($project['status']) ?>">
                                        <?= $project['status'] === 'published' ? 'Gepubliceerd' : 'Concept' ?>
                                    </span>
                                </div>

                                <div class="admin-project-details-grid">
                                    <p>
                                        <strong>Categorie:</strong><br>
                                        <?= htmlspecialchars($project['category']) ?>
                                    </p>

                                    <p>
                                        <strong>Locatie:</strong><br>
                                        <?= htmlspecialchars($project['location'] ?: 'Niet ingevuld') ?>
                                    </p>

                                    <p>
                                        <strong>Datum uitgevoerd:</strong><br>
                                        <?= htmlspecialchars($project['project_date'] ?: 'Niet ingevuld') ?>
                                    </p>

                                    <p>
                                        <strong>Uren:</strong><br>
                                        <?= htmlspecialchars($project['hours_worked'] ?: '0') ?> uur
                                    </p>

                                    <p>
                                        <strong>Materiaalkosten:</strong><br>
                                        €<?= htmlspecialchars(number_format((float)($project['material_costs'] ?? 0), 2, ',', '.')) ?>
                                    </p>

                                    <p>
                                        <strong>Aangemaakt door:</strong><br>
                                        <?= htmlspecialchars($project['creator_name'] ?: 'Onbekend') ?>
                                    </p>
                                </div>

                                <?php if (!empty($project['materials'])): ?>
                                    <p class="admin-project-note">
                                        <strong>Materialen:</strong>
                                        <?= htmlspecialchars(mb_strimwidth($project['materials'], 0, 140, '...')) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($project['work_notes'])): ?>
                                    <p class="admin-project-note">
                                        <strong>Notities:</strong>
                                        <?= htmlspecialchars(mb_strimwidth($project['work_notes'], 0, 140, '...')) ?>
                                    </p>
                                <?php endif; ?>

                                <p class="admin-small-meta">
                                    Aangemaakt: <?= htmlspecialchars($project['created_at']) ?> |
                                    Bijgewerkt: <?= htmlspecialchars($project['updated_at']) ?>
                                </p>
                            </div>

                            <div class="admin-project-actions">
                                <a 
                                    href="project-bewerken.php?id=<?= (int) $project['id'] ?>" 
                                    class="btn btn-secondary"
                                >
                                    Bewerken
                                </a>

                                <?php if ($project['status'] === 'published'): ?>
                                    <a 
                                        href="../project.php?id=<?= (int) $project['id'] ?>" 
                                        class="btn btn-outline"
                                        target="_blank"
                                    >
                                        Bekijk live
                                    </a>
                                <?php else: ?>
                                    <span class="concept-note">
                                        Niet live zichtbaar
                                    </span>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>