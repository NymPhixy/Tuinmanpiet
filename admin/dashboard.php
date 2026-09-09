<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userName = currentUserName();
$userRole = currentUserRole();

$totalProjectsStmt = $pdo->query("SELECT COUNT(*) AS total FROM projects");
$totalProjects = $totalProjectsStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$publishedStmt = $pdo->query("SELECT COUNT(*) AS total FROM projects WHERE status = 'published'");
$publishedProjects = $publishedStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$conceptStmt = $pdo->query("SELECT COUNT(*) AS total FROM projects WHERE status = 'concept'");
$conceptProjects = $conceptStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$hoursStmt = $pdo->query("SELECT SUM(hours_worked) AS total FROM projects");
$totalHours = $hoursStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$costsStmt = $pdo->query("SELECT SUM(material_costs) AS total FROM projects");
$totalMaterialCosts = $costsStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$latestStmt = $pdo->query("
    SELECT *
    FROM projects
    ORDER BY updated_at DESC
    LIMIT 5
");
$latestProjects = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tuinman Piet</title>
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
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="project-toevoegen.php">Project toevoegen</a>
                <a href="projecten-beheren.php">Projecten beheren</a>
            </nav>
        </aside>

        <section class="admin-content admin-content-wide">
            <div class="dashboard-intro">
                <h2>Beheeromgeving</h2>
                <p>
                    Hier beheer je projecten, foto’s en klusdocumentatie van Tuinman Piet.
                    Bezoekers kunnen deze beheeromgeving niet zien.
                </p>
            </div>

            <div class="dashboard-stats-grid">
                <div class="dashboard-card">
                    <h2>Totaal projecten</h2>
                    <p class="dashboard-number"><?= htmlspecialchars($totalProjects) ?></p>
                    <p>Alle projecten in het systeem.</p>
                </div>

                <div class="dashboard-card">
                    <h2>Gepubliceerd</h2>
                    <p class="dashboard-number"><?= htmlspecialchars($publishedProjects) ?></p>
                    <p>Zichtbaar op de publieke website.</p>
                </div>

                <div class="dashboard-card">
                    <h2>Concepten</h2>
                    <p class="dashboard-number"><?= htmlspecialchars($conceptProjects) ?></p>
                    <p>Nog niet zichtbaar voor bezoekers.</p>
                </div>

                <div class="dashboard-card">
                    <h2>Uren totaal</h2>
                    <p class="dashboard-number"><?= htmlspecialchars(number_format((float)$totalHours, 2, ',', '.')) ?></p>
                    <p>Geregistreerde werkuren.</p>
                </div>

                <div class="dashboard-card">
                    <h2>Materiaalkosten</h2>
                    <p class="dashboard-number small-number">
                        €<?= htmlspecialchars(number_format((float)$totalMaterialCosts, 2, ',', '.')) ?>
                    </p>
                    <p>Geschatte materiaalkosten totaal.</p>
                </div>

                <div class="dashboard-card">
                    <h2>Snel actie</h2>
                    <p>Voeg een nieuwe klus toe met foto’s, uren en materialen.</p>
                    <a href="project-toevoegen.php" class="btn btn-primary">Nieuw project toevoegen</a>
                </div>
            </div>

            <div class="dashboard-card latest-projects-card">
                <div class="admin-section-header">
                    <div>
                        <h2>Laatst bijgewerkte projecten</h2>
                        <p>De meest recente wijzigingen in het portfolio.</p>
                    </div>

                    <a href="projecten-beheren.php" class="btn btn-secondary">Alle projecten beheren</a>
                </div>

                <?php if (empty($latestProjects)): ?>
                    <p>Er zijn nog geen projecten toegevoegd.</p>
                <?php else: ?>
                    <div class="latest-project-list">
                        <?php foreach ($latestProjects as $project): ?>
                            <article class="latest-project-item">
                                <div>
                                    <h3><?= htmlspecialchars($project['title']) ?></h3>
                                    <p>
                                        <?= htmlspecialchars($project['category']) ?>
                                        <?php if (!empty($project['location'])): ?>
                                            · <?= htmlspecialchars($project['location']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="latest-project-meta">
                                    <span class="status-badge status-<?= htmlspecialchars($project['status']) ?>">
                                        <?= $project['status'] === 'published' ? 'Gepubliceerd' : 'Concept' ?>
                                    </span>

                                    <a href="project-bewerken.php?id=<?= (int)$project['id'] ?>" class="text-link">
                                        Bewerken →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h2>Jouw rol</h2>
                <p>Je bent ingelogd als: <strong><?= htmlspecialchars($userRole) ?></strong></p>

                <?php if ($userRole === 'admin'): ?>
                    <p>
                        Als admin beheer je de technische kant, projecten en algemene structuur van de website.
                    </p>
                <?php else: ?>
                    <p>
                        Als moderator kun je projecten, klusinformatie en foto’s beheren.
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </main>

</body>
</html>