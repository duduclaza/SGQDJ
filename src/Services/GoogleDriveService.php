<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveService
{
    private $client;
    private $service;
    private $folderId;

    public function __construct()
    {
        $credentialsPath = __DIR__ . '/../../storage/google/credentials.json';
        if (!file_exists($credentialsPath)) {
            throw new \Exception("Credentials file not found at $credentialsPath");
        }

        $this->client = new Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->addScope(Drive::DRIVE_METADATA);
        
        $this->service = new Drive($this->client);
        $this->folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
    }

    /**
     * Upload a file to Google Drive using resumable upload
     * 
     * @param string $filePath Local path to the file
     * @param string $fileName Name of the file on Drive
     * @param string $mimeType Mime type of the file
     * @return string Drive File ID
     */
    public function uploadFile(string $filePath, string $fileName, string $mimeType): string
    {
        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'parents' => $this->folderId ? [$this->folderId] : []
        ]);

        $this->client->setDefer(true);
        $request = $this->service->files->create($fileMetadata, [
            'mimeType' => $mimeType,
            'uploadType' => 'resumable',
            'fields' => 'id'
        ]);

        $chunkSizeBytes = 1 * 1024 * 1024; // 1MB chunks
        $media = new \Google\Http\MediaFileUpload(
            $this->client,
            $request,
            $mimeType,
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($filePath));

        $status = false;
        $handle = fopen($filePath, "rb");
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
        fclose($handle);
        $this->client->setDefer(false);

        if ($status->id) {
            // Make the file readable by anyone with the link (for streaming)
            try {
                $this->service->permissions->create($status->id, new \Google\Service\Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'reader'
                ]));
            } catch (\Exception $e) {
                error_log("GoogleDriveService: Error setting permissions for file {$status->id}: " . $e->getMessage());
            }
            return $status->id;
        }

        throw new \Exception("Failed to upload file to Google Drive.");
    }

    /**
     * Delete a file from Google Drive
     */
    public function deleteFile(string $fileId): void
    {
        try {
            $this->service->files->delete($fileId);
        } catch (\Exception $e) {
            // Log error or ignore if already deleted
        }
    }

    /**
     * Get a streamable link for the file
     */
    public function getStreamLink(string $fileId): string
    {
        // For public files, we can use the webContentLink or a direct stream URL
        $file = $this->service->files->get($fileId, ['fields' => 'webContentLink']);
        return $file->getWebContentLink();
    }
}
