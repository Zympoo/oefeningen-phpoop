<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\Flash;
use Admin\Core\View;
use Admin\Repositories\MediaRepository;

final class MediaController
{
    private MediaRepository $media;

    public function __construct(MediaRepository $media)
    {
        $this->media = $media;
    }

    public function index(): void
    {
        View::render('media.php', [
            'title' => 'Upload afbeelding',
            'items' => $this->media->getAllImages(),
        ]);
    }

    public function uploadForm(): void
    {
        $old = Flash::get('old');
        if (!is_array($old)) {
            $old = ['alt_text' => ''];
        }

        View::render('media-upload.php', [
            'title' => 'Upload afbeelding',
            'old' => $old,
        ]);
    }

    public function store(): void
    {
        $altTextRaw = trim((string)($_POST['alt_text'] ?? ''));
        Flash::set('old', ['alt_text' => $altTextRaw]);

        if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
            Flash::set('warning', ['Geen bestand ontvangen.']);
            header('Location: ' . ADMIN_BASE_PATH . '/media/upload');
            exit;
        }

        $file = $_FILES['image'];

        if (!isset($file['error']) || (int)$file['error'] !== UPLOAD_ERR_OK) {
            Flash::set('warning', ['Upload mislukt.']);
            header('Location: ' . ADMIN_BASE_PATH . '/media/upload');
            exit;
        }

        $maxBytes = 5 * 1024 * 1024;
        if ((int)$file['size'] > $maxBytes) {
            Flash::set('warning', ['Bestand is te groot. Max 5 MB.']);
            header('Location: ' . ADMIN_BASE_PATH . '/media/upload');
            exit;
        }

        $tmpPath = (string)$file['tmp_name'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$finfo->file($tmpPath);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!array_key_exists($mime, $allowed)) {
            Flash::set('warning', ['Ongeldig bestandstype. Enkel JPG, PNG of WEBP.']);
            header('Location: ' . ADMIN_BASE_PATH . '/media/upload');
            exit;
        }

        // ✅ correct pad vanaf admin/classes/Admin/Controllers => projectroot
        $projectRoot = dirname(__DIR__, 4);
        $uploadDir = $projectRoot . '/public/uploads';

        if (!is_dir($uploadDir)) {
            Flash::set('warning', ['Upload map ontbreekt: public/uploads']);
            header('Location: ' . ADMIN_BASE_PATH . '/media/upload');
            exit;
        }

        $ext = $allowed[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            Flash::set('warning', ['Kon bestand niet opslaan in uploads.']);
            header('Location: ' . ADMIN_BASE_PATH . '/media/upload');
            exit;
        }

        $altText = $altTextRaw !== '' ? $altTextRaw : null;

        $this->media->createImage(
            (string)$file['name'],
            $filename,
            'uploads',
            $mime,
            (int)$file['size'],
            $altText
        );

        Flash::set('old', []);
        Flash::set('success', 'Afbeelding succesvol geüpload.');
        header('Location: ' . ADMIN_BASE_PATH . '/media');
        exit;
    }

    public function delete(int $id): void
    {
        $item = $this->media->findImageById($id);

        if ($item === null) {
            Flash::set('warning', ['Afbeelding niet gevonden.']);
            header('Location: ' . ADMIN_BASE_PATH . '/media');
            exit;
        }

        $projectRoot = dirname(__DIR__, 4);
        $filePath = $projectRoot . '/public/' . $item['path'] . '/' . $item['filename'];

        if (is_file($filePath)) {
            @unlink($filePath);
        }

        $this->media->deleteById($id);

        Flash::set('success', 'Afbeelding verwijderd.');
        header('Location: ' . ADMIN_BASE_PATH . '/media');
        exit;
    }
}
