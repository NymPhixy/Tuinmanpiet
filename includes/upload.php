<?php

function uploadProjectImage(array $file): ?string
{
    if (
        !isset($file['error']) ||
        $file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Er ging iets mis tijdens het uploaden van de afbeelding.');
    }

    $maxFileSize = 5 * 1024 * 1024;

    if ($file['size'] > $maxFileSize) {
        throw new RuntimeException('De afbeelding mag maximaal 5 MB zijn.');
    }

    $tmpPath = $file['tmp_name'];

    if (!is_uploaded_file($tmpPath)) {
        throw new RuntimeException('Ongeldige upload.');
    }

    $imageInfo = getimagesize($tmpPath);

    if ($imageInfo === false) {
        throw new RuntimeException('Het bestand is geen geldige afbeelding.');
    }

    $mimeType = $imageInfo['mime'];

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!array_key_exists($mimeType, $allowedMimeTypes)) {
        throw new RuntimeException('Alleen JPG, PNG en WEBP afbeeldingen zijn toegestaan.');
    }

    $extension = $allowedMimeTypes[$mimeType];

    $uploadDir = __DIR__ . '/../uploads/projects/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid('project_', true) . '.' . $extension;
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        throw new RuntimeException('De afbeelding kon niet worden opgeslagen.');
    }

    return 'uploads/projects/' . $fileName;
}