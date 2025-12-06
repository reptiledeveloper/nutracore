<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    // Use private properties for encapsulation
    private Sheets $service;
    private string $sheetId = '1kmCCWx6BqTjkmQm7BNEkKCMUls4MUfATw1spwbL70Ck';
    private string $sheetName = 'Sheet1';

    public function __construct()
    {
        // 1. Initialize Google Client
        $client = new Client();

        // 2. Set Auth Config (Ensure this file path is correct and accessible)
        $client->setAuthConfig(storage_path('app/public/config.json'));

        // 3. Add necessary scope for reading/writing spreadsheets
        $client->addScope(Sheets::SPREADSHEETS);

        // 4. Create the Sheets service object
        $this->service = new Sheets($client);
    }

    /**
     * Reads all rows from the specified sheet.
     * @return array The rows as a nested array, or an empty array if no data exists.
     */
    public function read(): array
    {
        $range = $this->sheetName . '!A:Z';
        try {
            $response = $this->service->spreadsheets_values->get($this->sheetId, $range);
            return $response->getValues() ?? [];
        } catch (\Google\Service\Exception $e) {
            // Log the error (recommended in a real application)
            // \Log::error("Google Sheets Read Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Adds a new row of data to the sheet.
     * @param array $data The data array for the new row.
     * @return \Google\Service\Sheets\AppendValuesResponse
     */
    public function append(array $data)
    {
        $body = new ValueRange(['values' => [$data]]);

        return $this->service->spreadsheets_values->append(
            $this->sheetId,
            $this->sheetName,
            $body,
            // USER_ENTERED handles formulas like =HYPERLINK()
            ['valueInputOption' => 'USER_ENTERED']
        );
    }

    /**
     * Updates an existing row of data.
     * @param int $row The 1-based row number to update.
     * @param array $data The data array for the row.
     * @return \Google\Service\Sheets\UpdateValuesResponse
     */
    public function updateRow(int $row, array $data)
    {
        // Construct the range, e.g., Sheet1!A6
        $range = $this->sheetName . '!A' . $row;
        $body = new ValueRange(['values' => [$data]]);

        return $this->service->spreadsheets_values->update(
            $this->sheetId,
            $range,
            $body,
            // USER_ENTERED handles formulas like =HYPERLINK()
            ['valueInputOption' => 'USER_ENTERED']
        );
    }
}
