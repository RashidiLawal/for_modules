<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Actions;

use BitCore\Modules\User\Requests\CreateUserRequest;
use BitCore\Modules\User\Requests\CreateUserRequestWithFile;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        /**
         * Get data using possible options:
         */

        // Using the input class to fetch multiple data
        $data = (array)$this->input->post(['email', 'password']);

        // Using the input class to fetch all data
        $data = (array)$this->input->post();

        // Using the input class to fetch each input
        $data = [];
        $data['email'] = $this->input->post('email');

        // Get data from request class
        $data = CreateUserRequest::data();

        // Get data and auto validate - Ensure rules have all expected data, will throw exception
        $data = CreateUserRequest::validated();

        // Validate
        $errors = CreateUserRequest::validate($data);
        if ($errors) {
            return $this->respondWithData($errors->all());
        }

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData(['messag' => 'passed']);
    }

    /**
     * {@inheritdoc}
     */
    protected function action2(): Response
    {
        // Upload file using request validation
        $data = CreateUserRequestWithFile::validated();
        /** @var \BitCore\Application\Services\Requests\UploadedFile */
        $file = $data['file'];
        $filePath = $file->store('somedir', 'local'); // Save
        //exit($filePath);

        // Option1: Request validation class - Recommended

        // Upload file using request validation
        $data = CreateUserRequestWithFile::validated();
        /** @var \BitCore\Application\Services\Requests\UploadedFile */
        $file = $data['file'];
        $filePath = $file->store('somedir', 'local'); // Save

        // for multiple files
        $uploadeds = [];
        /** @var \BitCore\Application\Services\Requests\UploadedFile[] */
        $files = $data['files'];
        foreach ($files as $file) {
            $uploadeds[] = $file->store('somedir', 'local'); // Save
        }

        // Can still perform further validation or check like size e.t.c
        // by getting the prs7 instance i.e $file->getSize(), $file->guessExtension()



        // Option2: without Request validation class

        // Using the input class to fetch all data
        $uploadedFiles = $this->input->files();
        if ($uploadedFiles) {

            /** @var \BitCore\Application\Services\Requests\UploadedFile $file */
            $file = $uploadedFiles['file'];
            // Perform your validation manually i.e check for size mime type e.t.c
            $filePath = $file->store('somedir', 'disk');

            // If multiple files
            foreach ($uploadedFiles['fileS'] as $file) {
                $uploadeds[] = $file->store('somedir', 'local'); // Save
            }
        }

        // Or one line - Not recommend . Except validated through request class already
        $this->input->files('file')->store('somedir', 'disk');

        return $this->respondWithData(['messag' => 'passed']);
    }
}
