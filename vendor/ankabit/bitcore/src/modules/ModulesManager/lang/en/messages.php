<?php

return [
    'module_created'               => 'Module created successfully.',
    'module_creation_failed'       => 'Failed to create module. Please try again.',
    'unexpected_error'             => 'An unexpected error occurred. Please try again later.',
    'fetch_failed'                 => 'Failed to retrieve data. Please try again.',

    'invalid_module_ids'           => 'Invalid module ID list provided for bulk deletion.',
    'bulk_modules_delete_failed'   => 'Bulk module delete failed. Please try again.',
    'bulk_modules_delete_success'  => 'Modules deleted successfully.',
    'module_exists'          => 'A module with this name already exists.',
    'module_not_found'             => 'Module not found.',
    'module_delete_failed'         => 'Failed to delete module. Please try again.',
    'module_deleted'               => 'Module deleted successfully.',
    'module_activated'       => 'Module activated successfully.',
    'module_deactivated'     => 'Module deactivated successfully.',
    'modules_fetched'               => 'Module fetched successfully.',
    'module_uploaded' => 'Module uploaded successfully.',
    'module_update_failed'         => 'Failed to update module. Please try again.',
    'module_updated'               => 'Module updated successfully.',

    'upload_error_open_zip'             => 'Unable to open uploaded zip file.',
    'upload_error_multiple_folders'     => 'Module archive must contain a single folder.',
    'upload_error_invalid_json'         => 'Invalid JSON format in module.json.',
    'upload_error_missing_entry'        => 'Missing entry file (:module.php or public/:module.js).',
    'upload_error_file_upload_usage'    => 'Direct use of $_FILES is prohibited. Use upload() helper instead.',
    'upload_error_disallowed_namespace' => 'Disallowed namespace usage detected.',
    'upload_error_parsed_body'          => 'Use $this->input->post() instead of getParsedBody().',
    'upload_error_raw_queries'          => 'Use query builder instead of raw database queries.',
    'upload_error_disallowed_filetype'  => 'Disallowed file type detected: :filename.',
    'upload_error_disallowed_public_filetype' => 'The file ":filename" is not allowed 
    in the public directory of a module for security reasons.',
    'upload_error_override_core'        => 'Cannot override core module: :module.',
    'module_installation_failed'        => 'Module installation failed.'
];
