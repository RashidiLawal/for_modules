<?php

namespace BitCore\Application\Services\Requests;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use BitCore\Application\Services\Requests\UploadedFile;

/**
 * Class RequestInput
 *
 * A class to handle input data retrieval and optional sanitization for Slim 4 applications.
 * By default, all retrieved data is sanitized against XSS attacks using htmlspecialchars.
 * SQL injection protection would require additional database-specific handling out of the
 * context of the class i.e ORM
 */
class RequestInput
{
    protected $request;
    protected $sanitizeInput;

    /**
     * RequestInput constructor.
     *
     * @param Request $request The PSR-7 request object
     * @param bool $sanitizeInput Whether to sanitize input data (default: true)
     */
    public function __construct(Request $request, $sanitizeInput = true)
    {
        $this->request = $request;
        $this->sanitizeInput = $sanitizeInput;
    }

    /**
     * Retrieve POST data with optional sanitization.
     *
     * @param string|array|null $key The key of the POST data to retrieve (optional - to retrieve all post data)
     * @param mixed $default The default value if the key does not exist (optional)
     * @return mixed The sanitized or unsanitized POST data
     */
    public function post($key = null, $default = null)
    {
        $parsedBody = (array)$this->request->getParsedBody();

        if ($key === null) {
            return $parsedBody;
        }

        return $this->getValue($parsedBody, $key, $default);
    }

    /**
     * Retrieve GET data with optional sanitization.
     *
     * @param string|array|null $key The key of the GET data to retrieve (optional - to retrieve all post data)
     * @param mixed $default The default value if the key does not exist (optional)
     * @return mixed The sanitized or unsanitized GET data
     */
    public function get($key = null, $default = null)
    {
        $queryParams = (array)$this->request->getQueryParams();

        return $this->getValue($queryParams, $key, $default);
    }

    /**
     * Retrieves and sanitizes uploaded files from the request.
     *
     * This method ensures that only valid and safe files are included
     * by filtering out potentially dangerous files based on their
     * extensions, size, and validity.
     *
     * @param string|null $key The key of the file data to retrieve (optional - to retrieve all files)
     *
     * @return UploadedFile[]|UploadedFile|null List of uploaded files or single file or null.
     */
    public function files($key = null)
    {
        // Get the uploaded files from the request
        $uploadedFiles = $this->request->getUploadedFiles();

        // Return null if no files are uploaded
        if (empty($uploadedFiles)) {
            return null;
        }

        $files = [];

        // Anonymous function to process each file
        $processFile = function (string $inputName, UploadedFileInterface $file) use (&$files) {
            // Ensure the file is a valid UploadedFileInterface instance
            if (!$file instanceof UploadedFileInterface) {
                return;
            }

            // Convert to a safe file object
            $sanitizedFile = UploadedFile::fromPsr7File($file);

            // If sanitization is enabled and the file is not safe, skip it
            if ($this->sanitizeInput && !$this->isSafeFile($sanitizedFile)) {
                return;
            }

            // Store the sanitized file in the corresponding input name array
            if (isset($files[$inputName])) {
                // If it's an array (multiple files), append to the list
                $files[$inputName][] = $sanitizedFile;
            } else {
                // If it's a single file, store it directly
                $files[$inputName] = $sanitizedFile;
            }
        };

        foreach ($uploadedFiles as $inputName => $value) {
            // If the uploaded value is an array (multiple files), handle them
            if (is_array($value)) {
                $files[$inputName] = [];

                foreach ($value as $file) {
                    // Process each file individually using the anonymous function
                    $processFile($inputName, $file);
                }
            } else {
                // Process the single file using the anonymous function
                $processFile($inputName, $value);
            }

            if ($key && $inputName == $key) {
                return $files[$inputName];
            }
        }

        return $key ? ($files[$key] ?? null) : $files;
    }


    /**
     * Determines whether an uploaded file is safe based on its type and size.
     *
     * This method checks if the file's extension is in a list of known unsafe file types
     * (e.g., executable or script files), and also verifies that the file's size is within
     * a reasonable range (not zero and not exceeding 100MB).
     *
     * The logic performs the following checks:
     * 1. Verifies the file extension against a list of unsafe extensions such as 'php', 'exe', 'js', etc.
     * 2. Uses both the file's actual extension and a guessed extension to determine if the file is potentially unsafe.
     * 3. Ensures that the file size is greater than zero and does not exceed 100MB.
     *
     * @param UploadedFile $file The uploaded file to be validated.
     * @return bool Returns true if the file is considered safe, false otherwise.
     *
     * @todo Add hooks for dynamic extension validation, possibly move extension list to a configuration file.
     */
    public function isSafeFile(Uploadedfile $file)
    {
        // Check for common unsafe file types (e.g., executable files)
        // @todo Make hooks here and or move extension to config file
        $unsafeExtensions = [
            'php', 'php3', 'php4', 'php5',
            'exe', 'asp', 'aspx', 'jsp', 'js', 'sh', 'bat', 'cmd', 'vbs', 'vbe', 'pl', 'py', 'msi',
            'htaccess', 'env', 'java', 'wshell', 'wsf', 'cgi-bin', 'cgi', 'phar', 'jar', 'dll',
        ];
        $filename = $file->getPsr7File()->getClientFilename();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $guessedExtension = $file->guessExtension();

        // Disallow files with unsafe extensions
        if (
            in_array($extension, $unsafeExtensions) ||
            ($guessedExtension && in_array($guessedExtension, $unsafeExtensions))
        ) {
            return false;
        }

        // Ensure the file size is not zero or exceeds a sensible limit (e.g., 100MB)
        $fileSize = $file->getSize();
        if ($fileSize <= 0 || $fileSize > 100 * 1024 * 1024) { // 100MB limit
            return false;
        }

        return true;
    }



    /**
     * Get the request object
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }


    /**
     * Enable input data sanitization for subsequent retrieval methods.
     *
     * @return void
     */
    public function enableSanitization()
    {
        $this->sanitizeInput = true;
    }

    /**
     * Disable input data sanitization for subsequent retrieval methods.
     *
     * @return void
     */
    public function disableSanitization()
    {
        $this->sanitizeInput = false;
    }

    /**
     * Retrieve a value from an array with optional sanitization.
     *
     * @param array $data The data array from which to retrieve the value
     * @param string|array|null $key The key of the value to retrieve (optional)
     * @param mixed $default The default value if the key does not exist (optional)
     * @return mixed The sanitized or unsanitized value
     */
    protected function getValue(array $data, $key = null, $default = null)
    {

        $value = $default;

        if ($key === null) {
            $value = $data;
        } elseif (is_array($key)) {
            $value = array_intersect_key($data, array_flip($key));
        } else {
            $value = isset($data[$key]) ? $data[$key] : $default;
        }

        if ($this->sanitizeInput) {
            $value = $this->sanitizeValue($value);
        }

        return $value;
    }

    /**
     * Sanitize a value against XSS attacks using htmlspecialchars.
     *
     * @param mixed $value The value to sanitize
     * @return mixed The sanitized value
     * @todo Review and extend sanitation
     */
    public function sanitizeValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return $this->sanitizeArray((array)$value);
        }

        if (is_numeric($value) || empty($value)) {
            return $value;
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize all values in an array against XSS attacks using htmlspecialchars.
     *
     * @param array $data The array of data to sanitize
     * @return array The sanitized array of data
     */
    protected function sanitizeArray(array $data)
    {
        if ($this->sanitizeInput) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeValue($value);
            }
        }
        return $data;
    }
}
