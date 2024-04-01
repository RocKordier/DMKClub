<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Model;

use Gaufrette\File;

/**
 * Represents an email-attachment.
 */
class Attachment
{
    private string $filename;

    private ?string $content;

    private string $contentType;

    /**
     * @throws \Exception
     */
    public function __construct(File $file, string $filename = '')
    {
        $this->filename = $filename ?: $file->getKey();
        $this->content = $file->getContent();

        $this->assert();
    }

    /**
     * Check the payload.
     */
    private function assert(): void
    {
        if (!$this->filename) {
            throw new \Exception('No valid "filename" found in message');
        }
        if (null === $this->content) {
            throw new \Exception('No valid "content" found in message');
        }
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        if (!$this->contentType) {
            $this->contentType = $this->extractContentType();
        }

        return $this->contentType;
    }

    private function extractContentType(): string
    {
        $fileData = explode('.', $this->filename);
        $ext = strtolower(array_pop($fileData));
        if (\array_key_exists($ext, self::$mimeTypeMap)) {
            return self::$mimeTypeMap[$ext];
        }

        return 'application/octet-stream';
    }

    private static array $mimeTypeMap = [
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    ];
}
