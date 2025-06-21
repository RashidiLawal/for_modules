<?php

declare(strict_types=1);

namespace Modules\Staff\Services;

use Modules\Customer\Services\BaseImportService;
use Modules\Staff\Requests\CreateStaffRequest;
use Modules\Staff\Repositories\StaffRepositoryInterface;
use Carbon\Carbon;

class ImportService extends BaseImportService
{
    /**
     * Constructor to initialize the import service for companies.
     *
     */
    public function __construct(StaffRepositoryInterface $staffRepository)
    {
        parent::__construct('Staffs', $staffRepository);
    }

    /**
     * Validates each row of the imported data.
     *
     * @param array $row The data row to be validated.
     *
     * @return array The validated data.
     *
     * @throws \InvalidArgumentException If the data format is invalid.
     */
    protected function validateData(array $row): array
    {
        $validated = CreateStaffRequest::validate($row);

        if (!is_array($validated)) {
            throw new \InvalidArgumentException(trans('messages.invalid_data_format'));
        }

        return $validated;
    }

    /**
     * Maps the validated data to the database structure.
     *
     * @param array $row The validated row data.
     * @param array $extraParams Additional parameters to be merged with the row data.
     *
     * @return array|null The mapped data or null if the row is empty.
     */
    protected function mapData(array $row, array $extraParams = []): ?array
    {
        if (empty($row)) {
            return null;
        }

        return array_merge($extraParams, [ // Merge extra parameters dynamically
            'profile_image'            => $row['profile_image'],
            'firstname'         => $row['firstname'] ?? null,
            'lastname'           => $row['lastname'],
            'email'      => $row['email'],
            'hour_rate'     => $row['hour_rate'] ?? null,
            'phone'      => $row['phone'] ?? null,
            'facebook'        => $row['facebook'] ?? null,
            'linkedin'            => $row['linkedin'] ?? null,
            'is_staff'             => $row['is_staff'] ?? false,
            'created_at'         => Carbon::now(),
            'updated_at'         => Carbon::now(),
        ]);
    }
}
