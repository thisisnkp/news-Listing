<?php

namespace App\Imports;

use App\Models\Package;
use App\Models\Plan;
use App\Models\Language;
use App\Models\TableRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class RowsImport implements ToCollection, WithHeadingRow
{
    protected $entity;
    protected $columns;
    protected $languages;
    protected $defaultLanguage;
    protected bool $isPackage;

    /**
     * @param Plan|Package $entity
     * @param bool $isPackage Whether the entity is a Package (media type)
     */
    public function __construct($entity, bool $isPackage = false)
    {
        $this->entity = $entity;
        $this->isPackage = $isPackage;
        $this->columns = $entity->columns()->ordered()->get();
        $this->languages = Language::active()->get();
        $this->defaultLanguage = Language::getDefault();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [];
            $translations = [];

            foreach ($this->columns as $column) {
                $value = $row[$column->slug] ?? $row[strtolower($column->name)] ?? null;

                if ($column->is_translatable) {
                    // For translatable columns, look for language-specific headers
                    // e.g., name_en, name_hi or just use the default
                    foreach ($this->languages as $language) {
                        $langKey = $column->slug . '_' . $language->code;
                        $langValue = $row[$langKey] ?? null;

                        if ($langValue !== null) {
                            if (!isset($translations[$language->id])) {
                                $translations[$language->id] = [];
                            }
                            $translations[$language->id][$column->slug] = $langValue;
                        } elseif ($this->defaultLanguage && $language->id === $this->defaultLanguage->id) {
                            // Use base value for default language
                            if (!isset($translations[$language->id])) {
                                $translations[$language->id] = [];
                            }
                            $translations[$language->id][$column->slug] = $value;
                        }
                    }
                } else {
                    // Non-translatable data
                    if ($value !== null) {
                        // Convert to appropriate type
                        if ($column->type === 'number' || $column->type === 'currency') {
                            $value = is_numeric($value) ? (float) $value : 0;
                        }
                        $data[$column->slug] = $value;
                    }
                }
            }

            // Create the row with appropriate foreign key
            $rowData = [
                'data' => $data,
                'order' => $this->entity->rows()->max('order') + 1,
            ];

            if ($this->isPackage) {
                $rowData['package_id'] = $this->entity->id;
            } else {
                $rowData['plan_id'] = $this->entity->id;
            }

            $tableRow = TableRow::create($rowData);

            // Save translations
            foreach ($translations as $languageId => $translatedData) {
                if (!empty(array_filter($translatedData))) {
                    $tableRow->setTranslation($languageId, $translatedData);
                }
            }
        }
    }
}
