<?php

declare(strict_types=1);

namespace Modules\Customer\Services;

use Psr\Http\Message\ResponseInterface as Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportServiceExcel
{
    /**
     * Exports companies as CSV or Excel.
     *
     * @param array $companies List of companies
     */
    public function exportCompanies(array $companies)
    {
        return   $this->generateExcel($companies);
    }

    /**
     * Generates an Excel file response.
     *
     * @param array $companies List of companies
    //  * @return Response Excel file response
     */
    private function generateExcel(array $companies)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = [
            'Company Name',
            'Domain',
            'Email',
            'Phone Number',
            'Street Address',
            'City',
            'Postal Code',
            'Country',
            'Created At'
        ];

        // Insert headers
        $sheet->fromArray([$headers], null, 'A1');

        // Insert company data
        $sheet->fromArray($companies, null, 'A2');

        // Write to memory
        $writer = new Xlsx($spreadsheet);
        $filePath = sys_get_temp_dir() . '/companies_export.xlsx';
        $writer->save($filePath);

         return $filePath;
    }
}
