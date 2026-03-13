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
     * Upload a file to Google Drive
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

        $content = file_get_contents($filePath);

        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        // Make the file readable by anyone with the link (for streaming)
        $this->service->permissions->create($file->id, new \Google\Service\Drive\Permission([
            'type' => 'anyone',
            'role' => 'reader'
        ]));

        return $file->id;
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
