<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$error = '';
$success = '';

function uploadProjectImage(array $file, string $prefix = 'project_'): ?string
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if (empty($file['tmp_name']) || empty($file['name'])) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new Exception('Er ging iets mis tijdens het uploaden van de afbeelding.');
    }

    $fileTmpPath = $file['tmp_name'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileType = mime_content_type($fileTmpPath);

    if (!in_array($fileType, $allowedTypes, true)) {
        throw new Exception('Alleen JPG, PNG en WEBP afbeeldingen zijn toegestaan.');
    }

    if ($fileSize > $maxFileSize) {
        throw new Exception('Een afbeelding mag maximaal 5 MB zijn.');
    }

    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $safeFileName = uniqid($prefix, true) . '.' . $extension;

    $uploadDir = __DIR__ . '/../uploads/projects/';
    $targetPath = $uploadDir . $safeFileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($fileTmpPath, $targetPath)) {
        throw new Exception('Uploaden van afbeelding is mislukt.');
    }

    return 'uploads/projects/' . $safeFileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $coverImagePath = null;

    if ($title === '' || $category === '') {
        $error = 'Titel en categorie zijn verplicht.';
    } elseif (!in_array($status, ['concept', 'published'], true)) {
        $error = 'Ongeldige status gekozen.';
    } else {
        try {
            if (!empty($_FILES['cover_image']['name'])) {
                $coverImagePath = uploadProjectImage($_FILES['cover_image'], 'project_cover_');
            }

            $stmt = $pdo->prepare("
                INSERT INTO projects 
                (
                    title,
                    category,
                    location,
                    project_date,
                    description,
                    cover_image,
                    hours_worked,
                    materials,
                    material_costs,
                    work_notes,
                    status,
                    created_by
                )
                VALUES 
                (
                    :title,
                    :category,
                    :location,
                    :project_date,
                    :description,
                    :cover_image,
                    :hours_worked,
                    :materials,
                    :material_costs,
                    :work_notes,
                    :status,
                    :created_by
                )
            ");

            $stmt->execute([
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
                'created_by' => currentUserId()
            ]);

            $projectId = $pdo->lastInsertId();

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

                    $imagePath = uploadProjectImage($singleFile, 'project_extra_');

                    if ($imagePath) {
                        $imageStmt = $pdo->prepare("
                            INSERT INTO project_images 
                            (project_id, image_path, alt_text)
                            VALUES 
                            (:project_id, :image_path, :alt_text)
                        ");

                        $imageStmt->execute([
                            'project_id' => $projectId,
                            'image_path' => $imagePath,
                            'alt_text' => $title
                        ]);
                    }
                }
            }

            $success = 'Project succesvol toegevoegd.';
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project toevoegen - Tuinman Piet</title>
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
                <a href="project-toevoegen.php" class="active">Project toevoegen</a>
                <a href="projecten-beheren.php">Projecten beheren</a>
            </nav>
        </aside>

        <section class="admin-content admin-content-wide">
            <div class="dashboard-card form-card">
                <h2>Nieuw project / klus</h2>

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

                <form method="POST" enctype="multipart/form-data">
                    <h3 class="form-section-title">Publieke projectinformatie</h3>

                    <div class="form-group">
                        <label for="title">Projecttitel *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            required
                            placeholder="Bijvoorbeeld: Tuinonderhoud in Musselkanaal"
                        >
                    </div>

                    <div class="form-group">
                        <label for="category">Categorie *</label>
                        <select id="category" name="category" required>
                            <option value="">Kies een categorie</option>
                            <option value="Aanleg">Aanleg</option>
                            <option value="Onderhoud">Onderhoud</option>
                            <option value="Renovatie">Renovatie</option>
                            <option value="Bestrating">Bestrating</option>
                            <option value="Schuttingen">Schuttingen</option>
                            <option value="Snoeiwerk">Snoeiwerk</option>
                            <option value="Overig">Overig</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Locatie</label>
                        <input 
                            type="text" 
                            id="location" 
                            name="location" 
                            placeholder="Bijvoorbeeld: Musselkanaal"
                        >
                    </div>

                    <div class="form-group">
                        <label for="project_date">Datum uitgevoerd</label>
                        <input 
                            type="date" 
                            id="project_date" 
                            name="project_date"
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Omschrijving voor website</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5" 
                            placeholder="Korte omschrijving die bezoekers mogen zien"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="published">Gepubliceerd</option>
                            <option value="concept">Concept</option>
                        </select>
                        <small>Concepten zijn bedoeld voor beheer en kunnen later gepubliceerd worden.</small>
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
                            placeholder="Bijvoorbeeld: 6.5"
                        >
                    </div>

                    <div class="form-group">
                        <label for="materials">Gebruikte materialen</label>
                        <textarea 
                            id="materials" 
                            name="materials" 
                            rows="4"
                            placeholder="Bijvoorbeeld: schuttingdelen, palen, schroeven, grond, planten"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="material_costs">Materiaalkosten ongeveer</label>
                        <input 
                            type="number" 
                            id="material_costs" 
                            name="material_costs" 
                            step="0.01" 
                            min="0"
                            placeholder="Bijvoorbeeld: 240.00"
                        >
                    </div>

                    <div class="form-group">
                        <label for="work_notes">Interne werknotities</label>
                        <textarea 
                            id="work_notes" 
                            name="work_notes" 
                            rows="5"
                            placeholder="Bijzonderheden, afspraken, wat er precies gedaan is, aandachtspunten"
                        ></textarea>
                    </div>

                    <h3 class="form-section-title">Foto’s</h3>

                    <div class="form-group">
                        <label for="cover_image">Hoofdafbeelding</label>
                        <input 
                            type="file" 
                            id="cover_image" 
                            name="cover_image" 
                            accept="image/jpeg,image/png,image/webp"
                        >
                        <small>Deze afbeelding wordt gebruikt als thumbnail/hoofdfoto van het project.</small>
                    </div>

                    <div class="form-group">
                        <label for="project_images">Extra projectfoto’s</label>
                        <input 
                            type="file" 
                            id="project_images" 
                            name="project_images[]" 
                            accept="image/jpeg,image/png,image/webp" 
                            multiple
                        >
                        <small>Je kunt meerdere foto’s tegelijk selecteren. Deze blijven gekoppeld aan dit project.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Project opslaan
                    </button>
                </form>
            </div>
        </section>
    </main>

</body>
</html>