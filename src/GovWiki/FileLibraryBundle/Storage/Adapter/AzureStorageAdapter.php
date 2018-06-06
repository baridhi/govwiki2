<?php

namespace GovWiki\FileLibraryBundle\Storage\Adapter;

use GuzzleHttp\Psr7\Stream;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\SharedAccessSignatureHelper;
use MicrosoftAzure\Storage\File\FileRestProxy;
use MicrosoftAzure\Storage\File\Models\ListDirectoriesAndFilesOptions;
use Psr\Http\Message\StreamInterface;

use function GuzzleHttp\Psr7\stream_for;

/**
 * Class AzureStorageAdapter
 *
 * @package GovWiki\FileLibraryBundle\Storage\Adapter
 */
class AzureStorageAdapter implements StorageAdapterInterface
{

    /**
     * @var string
     */
    private $accountName;

    /**
     * @var FileRestProxy
     */
    private $client;

    /**
     * @var SharedAccessSignatureHelper
     */
    private $sasHelper;

    /**
     * @var string
     */
    private $share;

    /**
     * AzureStorageAdapter constructor.
     *
     * @param string $accountName Azure file storage account name.
     * @param string $accountKey  Azure file storage account key.
     * @param string $share       Used file storage share.
     */
    public function __construct(array $credentials)
    {
        if (! self::validateCredentials($credentials)) {
            throw new \LogicException(\sprintf('Got invalid credentials'));
        }

        $this->accountName = $credentials['account_name'];
        $this->client = FileRestProxy::createFileService(\sprintf(
            'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s;',
            $this->accountName,
            $credentials['account_key']
        ));
        $this->sasHelper = new SharedAccessSignatureHelper($this->accountName, $credentials['account_key']);
        $this->share = $credentials['share'];
    }

    /**
     * @param string $path Path to created directory.
     *
     * @return void
     *
     * @api
     */
    public function createDirectory(string $path)
    {
        $path = self::normalizePath($path);
        $parts = \explode('/', $path);
        $currPath = '';

        foreach ($parts as $dir) {
            $currPath .= $dir .'/';
            try {
                $this->client->getDirectoryMetadata($this->share, $currPath);
            } catch (ServiceException $exception) {
                if ($exception->getCode() !== 404) {
                    throw $exception;
                }

                $this->client->createDirectory($this->share, $currPath);
            }
        }
    }

    /**
     * @param string          $path    Path where file should be created.
     * @param StreamInterface $content Stored file content.
     *
     * @return void
     *
     * @api
     */
    public function createFile(string $path, StreamInterface $content)
    {
        $path = self::normalizePath($path);

        $this->createDirectory(\dirname($path));

        $this->client->createFileFromContent(
            $this->share,
            $path,
            $content
        );
    }

    /**
     * @param string $path Path to checked file.
     *
     * @return boolean
     *
     * @api
     */
    public function isFileExists(string $path): bool
    {
        $path = self::normalizePath($path);

        try {
            $this->client->getFileMetadata($this->share, $path);

            return true;
        } catch (ServiceException $exception) {
            return false;
        }
    }

    /**
     * @param string $path Path to file.
     *
     * @return string
     */
    public function generatePublicUrl(string $path): string
    {
        $startDateTime = new \DateTime();

        $token = $this->sasHelper->generateAccountSharedAccessSignatureToken(
            '2017-07-29',
            'r',
            'f',
            'sco',
            (clone $startDateTime)->modify('+ 100 years'),
            $startDateTime,
            '',
            'https'
        );

        return \sprintf(
            'https://%s.file.core.windows.net/%s/%s?%s',
            $this->accountName,
            $this->share,
            \ltrim($path, '/'),
            $token
        );
    }

    /**
     * Get list of files for specified path.
     *
     * @param string $path Path to listed directory.
     *
     * @return \Traversable
     * @psalm-return \Traversable<AdapterFile>
     *
     * @api
     */
    public function listFiles(string $path): \Traversable
    {
        $options = new ListDirectoriesAndFilesOptions();
        $options->setMaxResults(1000);

        do {
            $result = $this->client->listDirectoriesAndFiles(
                $this->share,
                self::normalizePath($path),
                $options
            );

            $path = $path === '/' ? '' : $path;

            foreach ($result->getDirectories() as $directory) {
                yield AdapterFile::createDirectory(
                    $path .'/'. $directory->getName()
                );
            }

            foreach ($result->getFiles() as $file) {
                yield AdapterFile::createFile(
                    $path .'/'. $file->getName(),
                    $file->getLength()
                );
            }

            $marker = $result->getNextMarker();
            $options->setMarker($marker);
        } while ($marker);
    }

    /**
     * @param string $srcPath  Path from which we should move file.
     * @param string $destPath Path to which we should move.
     *
     * @return void
     *
     * @api
     */
    public function move(string $srcPath, string $destPath)
    {
        //
        // For some reasons copyFile() method don't works ...
        //
        $srcPath = self::normalizePath($srcPath);
        $destPath = self::normalizePath($destPath);

        //
        // We should call stream get contents 'cause otherwise we don't get any
        // data here.
        //
        $stream = $this->client->getFile($this->share, $srcPath)->getContentStream();
        $stream = stream_for(\stream_get_contents($stream));

        $this->createDirectory(\dirname($destPath));
        $this->client->createFileFromContent(
            $this->share,
            self::normalizePath($destPath),
            $stream
        );
        $this->remove($srcPath);
    }

    /**
     * @param string $path A path to removed file.
     *
     * @return void
     *
     * @api
     */
    public function remove(string $path)
    {
        $path = self::normalizePath($path);

        $this->client->deleteFile($this->share, $path);
    }

    /**
     * @param string $path A path to file.
     *
     * @return StreamInterface
     *
     * @api
     */
    public function read(string $path): StreamInterface
    {
        $path = self::normalizePath($path);
        $result = $this->client->getFile($this->share, $path);

        return new Stream($result->getContentStream());
    }

    /**
     * Check that specified credential is valid for this adapter.
     *
     * @param array $credential Array of credentials.
     *
     * @return boolean
     */
    public static function validateCredentials(array $credential): bool
    {
        if (! isset($credential['share'], $credential['account_name'], $credential['account_key'])) {
            return false;
        }

        return \is_string($credential['share'])
            && \is_string($credential['account_name'])
            && \is_string($credential['account_key']);
    }

    /**
     * @param string $path Path to directory.
     *
     * @return string
     */
    private static function normalizePath(string $path): string
    {
        //
        // For some reasons Azure file storage return "The specifed resource name
        // contains invalid characters." error if we try to request directory by
        // path with "/"  as first character (like /some/path/to/file) as normal
        // people do ...
        //
        return \ltrim($path, '/');
    }
}
