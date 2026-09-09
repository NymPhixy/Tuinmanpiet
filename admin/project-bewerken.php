<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: projecten-beheren.php');
    exit;
}

$error = '';
$success = '';

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: projecten-beheren.php');
    exit;
}

$imageStmt = $pdo->prepare("
    SELECT * 
    FROM project_images 
    WHERE project_id = :project_id 
    ORDER BY created_at ASC
");
$imageStmt->execute(['project_id' => $id]);
$projectImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete_project'])) {
            if (!empty($project['cover_image'])) {
                $oldImagePath = __DIR__ . '/../' . $project['cover_image'];

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            foreach ($projectImages as $image) {
                $imagePath = __DIR__ . '/../' . $image['image_path'];

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $deleteStmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
            $deleteStmt->execute(['id' => $id]);

            header('Location: projecten-beheren.php');
            exit;
        }

        if (isset($_POST['delete_image_id'])) {
            $imageId = (int) $_POST['delete_image_id'];

            $findImageStmt = $pdo->prepare("
                SELECT * 
                FROM project_images 
                WHERE id = :id AND project_id = :project_id 
                LIMIT 1
            ");
            $findImageStmt->execute([
                'id' => $imageId,
                'project_id' => $id
            ]);

            $imageToDelete = $findImageStmt->fetch(PDO::FETCH_ASSOC);

            if ($imageToDelete) {
                $filePath = __DIR__ . '/../' . $imageToDelete['image_path'];

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $deleteImageStmt = $pdo->prepare("DELETE FROM project_images WHERE id = :id");
                $deleteImageStmt->execute(['id' => $imageId]);

                $success = 'Foto succesvol verwijderd.';
            }
        } else {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $projectDate = trim($_POST['project_date'] ?? '');
            $description = trim($_POST['description'] ?? '');

            $hoursWorked = trim($_POST['hours_worked'] ?? '');
            $materials = trim($_POST['materials'] ?? '');
            $materialCosts = trim($_POST['material_costs'] ?? '');
            $workNotes = trim($_POST['work_notes'] ?? '');
            $status = trim($_POST['status'] ?? 'published');

            $coverImagePath = $project['cover_image'];

            if ($title === '' || $category === '') {
                $error = 'Titel en categorie zijn verplicht.';
            } elseif (!in_array($status, ['concept', 'published'], true)) {
                $error = 'Ongeldige status gekozen.';
            } else {
                if (!empty($_FILES['cover_image']['name'])) {
                    $newCoverImage = uploadProjectImage($_FILES['cover_image']);

                    if ($newCoverImage) {
                        if (!empty($project['cover_image'])) {
                            $oldImagePath = __DIR__ . '/../' . $project['cover_image'];

                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }

                        $coverImagePath = $newCoverImage;
                    }
                }

                if (!empty($_FILES['project_images']['name'][0])) {
                    foreach ($_FILES['project_images']['tmp_name'] as $index => $tmpName) {
                        if (empty($tmpName)) {
                            continue;
                        }

                        $singleFile = [
                            'name' => $_FILES['project_images']['name'][$index],
                            'type' => $_FILES['project_images']['type'][$index],
                            'tmp_name' => $_FILES['project_images']['tmp_name'][$index],
                            'error' => $_FILES['project_images']['error'][$index],
                            'size' => $_FILES['project_images']['size'][$index],
                        ];

                        if ($singleFile['error'] !== UPLOAD_ERR_OK) {
                            continue;
                        }

                        $imagePath = uploadProjectImage($singleFile);

                        if ($imagePath) {
                            $extraImageStmt = $pdo->prepare("
                                INSERT INTO project_images 
                                (project_id, image_path, alt_text)
                                VALUES 
                                (:project_id, :image_path, :alt_text)
                            ");

                            $extraImageStmt->execute([
                                'project_id' => $id,
                                'image_path' => $imagePath,
                                'alt_text' => $title
                            ]);
                        }
                    }
                }

                $updateStmt = $pdo->prepare("
                    UPDATE projects
                    SET title = :title,
                        category = :category,
                        location = :location,
                        project_date = :project_date,
                        description = :description,
                        cover_image = :cover_image,
                        hours_worked = :hours_worked,
                        materials = :materials,
                        material_costs = :material_costs,
                        work_notes = :work_notes,
                        status = :status
                    WHERE id = :id
                ");

                $updateStmt->execute([
                    'title' => $title,
                    'category' => $category,
                    'location' => $location !== '' ? $location : null,
                    'project_date' => $projectDate !== '' ? $projectDate : null,
                    'description' => $description !== '' ? $description : null,
                    'cover_image' => $coverImagePath,
                    'hours_worked' => $hoursWorked !== '' ? $hoursWorked : null,
                    'materials' => $materials !== '' ? $materials : null,
                    'material_costs' => $materialCosts !== '' ? $materialCosts : null,
                    'work_notes' => $workNotes !== '' ? $workNotes : null,
                    'status' => $status,
                    'id' => $id
                ]);

                $success = 'Project succesvol bijgewerkt.';
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        $imageStmt = $pdo->prepare("
            SELECT * 
            FROM project_images 
            WHERE project_id = :project_id 
            ORDER BY created_at ASC
        ");
        $imageStmt->execute(['project_id' => $id]);
        $projectImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$categories = ['Aanleg', 'Onderhoud', 'Renovatie', 'Bestrating', 'Schuttingen', 'Snoeiwerk', 'Overig'];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project bewerken - Tuinman Piet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-page">

<header class="admin-header">
    <div>
        <h1>Project bewerken</h1>
        <p>Pas projectinformatie, foto’s en interne klusdocumentatie aan.</p>
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
        <div class="dashboard-card form-card">
            <h2><?= htmlspecialchars($project['title']) ?></h2>

            <div class="project-meta-box">
                <p><strong>Aangemaakt:</strong> <?= htmlspecialchars($project['created_at']) ?></p>
                <p><strong>Laatst bijgewerkt:</strong> <?= htmlspecialchars($project['updated_at']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($project['status']) ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['cover_image'])): ?>
                <div class="current-image-preview">
                    <p><strong>Huidige hoofdafbeelding:</strong></p>
                    <img 
                        src="../<?= htmlspecialchars($project['cover_image']) ?>" 
                        alt="<?= htmlspecialchars($project['title']) ?>"
                    >
                </div>
            <?php endif; ?>

            <?php if (!empty($projectImages)): ?>
                <div class="current-image-preview">
                    <p><strong>Extra projectfoto’s:</strong></p>

                    <div class="extra-image-grid">
                        <?php foreach ($projectImages as $image): ?>
                            <div class="extra-image-item">
                                <img 
                                    src="../<?= htmlspecialchars($image['image_path']) ?>" 
                                    alt="<?= htmlspecialchars($image['alt_text'] ?: $project['title']) ?>"
                                >

                                <form 
                                    method="POST" 
                                    class="delete-image-form"
                                    onsubmit="return confirm('Weet je zeker dat je deze foto wilt verwijderen?');"
                                >
                                    <input 
                                        type="hidden" 
                                        name="delete_image_id" 
                                        value="<?= (int) $image['id'] ?>"
                                    >
                                    <button type="submit" class="delete-image-button">
                                        Verwijder
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <h3 class="form-section-title">Publieke projectinformatie</h3>

                <div class="form-group">
                    <label for="title">Projecttitel *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="<?= htmlspecialchars($project['title']) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="category">Categorie *</label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $categoryOption): ?>
                            <option 
                                value="<?= htmlspecialchars($categoryOption) ?>"
                                <?= $project['category'] === $categoryOption ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($categoryOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location">Locatie</label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        value="<?= htmlspecialchars($project['location'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="project_date">Datum uitgevoerd</label>
                    <input 
                        type="date" 
                        id="project_date" 
                        name="project_date"
                        value="<?= htmlspecialchars($project['project_date'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="description">Omschrijving voor website</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="5"
                    ><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="published" <?= $project['status'] === 'published' ? 'selected' : '' ?>>
                            Gepubliceerd
                        </option>
                        <option value="concept" <?= $project['status'] === 'concept' ? 'selected' : '' ?>>
                            Concept
                        </option>
                    </select>
                    <small>Concepten verschijnen niet op de publieke projectenpagina.</small>
                </div>

                <h3 class="form-section-title">Interne klusdocumentatie</h3>

                <div class="form-group">
                    <label for="hours_worked">Aantal gewerkte uren</label>
                    <input 
                        type="number" 
                        id="hours_worked" 
                        name="hours_worked" 
                        step="0.25" 
                        min="0"
                        value="<?= htmlspecialchars($project['hours_worked'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="materials">Gebruikte materialen</label>
                    <textarea 
                        id="materials" 
                        name="materials" 
                        rows="4"
                    ><?= htmlspecialchars($project['materials'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="material_costs">Materiaalkosten ongeveer</label>
                    <input 
                        type="number" 
                        id="material_costs" 
                        name="material_costs" 
                        step="0.01" 
                        min="0"
                        value="<?= htmlspecialchars($project['material_costs'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="work_notes">Interne werknotities</label>
                    <textarea 
                        id="work_notes" 
                        name="work_notes" 
                        rows="5"
                    ><?= htmlspecialchars($project['work_notes'] ?? '') ?></textarea>
                </div>

                <h3 class="form-section-title">Foto’s</h3>

                <div class="form-group">
                    <label for="cover_image">Nieuwe hoofdafbeelding</label>
                    <input 
                        type="file" 
                        id="cover_image" 
                        name="cover_image" 
                        accept="image/jpeg,image/png,image/webp"
                    >
                    <small>Laat leeg als je de huidige hoofdafbeelding wilt behouden.</small>
                </div>

                <div class="form-group">
                    <label for="project_images">Extra projectfoto’s toevoegen</label>
                    <input 
                        type="file" 
                        id="project_images" 
                        name="project_images[]" 
                        accept="image/jpeg,image/png,image/webp" 
                        multiple
                    >
                    <small>Nieuwe foto’s worden toegevoegd aan dit project. Bestaande foto’s blijven staan.</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Wijzigingen opslaan
                </button>
            </form>

            <form 
                method="POST" 
                class="delete-form" 
                onsubmit="return confirm('Weet je zeker dat je dit hele project wilt verwijderen? Alle gekoppelde foto’s worden ook verwijderd.');"
            >
                <button type="submit" name="delete_project" class="btn btn-danger">
                    Project verwijderen
                </button>
            </form>
        </div>
    </section>
</main>

</body>
</html>