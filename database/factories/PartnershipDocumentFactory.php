<?php

namespace Database\Factories;

use App\Models\PartnershipDocument;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnershipDocument>
 */
class PartnershipDocumentFactory extends Factory
{
    protected $model = PartnershipDocument::class;

    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'uploaded_by' => User::factory(),
            'document_name' => 'Partnership Agreement',
            'document_type' => 'mou',
            'file_path' => 'partnership-documents/test.pdf',
            'file_name' => 'test.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'document_date' => now(),
            'document_status' => 'active',
        ];
    }
}
