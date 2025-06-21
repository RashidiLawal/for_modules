<?php

declare(strict_types=1);

namespace Modules\Customer\Services;

use League\Csv\Writer;

class ExportService
{
    /**
     * Exports companies as CSV or Excel.
     *
     * @param array $companies List of companies
     */
    public function exportCompanies(array $companies)
    {
        return $this->generateCsv($companies);
    }

    /**
     * Generates a CSV file response.
     *
     * @param array $companies List of companies
     */
    private function generateCsv(array $companies)
    {
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'Company Name',
            'Domain',
            'Email',
            'Phone Number',
            'Street Address',
            'City',
            'Postal Code',
            'Country',
            'Created At'
        ]);

        foreach ($companies as $company) {
            $csv->insertOne([
                $company['company_name'], $company['domain'], $company['email'],
                $company['phone_number'], $company['street_address'],
                $company['city'], $company['postal_code'], $company['country'],
                $company['created_at']
            ]);
        }

        return $csv;
    }
}
