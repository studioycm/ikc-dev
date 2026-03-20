<?php

use App\Livewire\Legacy\Pedigree\PedigreeTree;
use App\Services\Legacy\Pedigree\PedigreeTreeBuilderService;
use Livewire\Livewire;

beforeEach(function (): void {
    config()->set('pedigree_tree.defaults', [
        'depth' => 4,
        'density' => 'comfortable',
        'font_scale' => 'normal',
        'card_height' => 'normal',
        'root_titles_mode' => 'compact',
        'show_placeholders' => true,
        'visible_fields' => [
            'sagir_id' => true,
            'import_number' => false,
            'breeding_house' => true,
            'breed_name' => false,
            'color_name' => true,
            'birth_date' => true,
            'age' => false,
            'titles' => false,
        ],
    ]);

    $this->app->bind(PedigreeTreeBuilderService::class, fn() => new class extends PedigreeTreeBuilderService {
        public function __construct()
        {
        }

        public function build(
            int    $dogId,
            int    $depth = 4,
            string $direction = 'rtl',
            bool   $includeNodeTitles = false,
        ): array
        {
            return [
                'root' => [
                    'id' => $dogId,
                    'sagir_id' => 'IL-123',
                    'import_number' => null,
                    'name_he' => null,
                    'name_en' => null,
                    'name_primary' => 'Test Dog',
                    'name_secondary' => null,
                    'full_name' => 'Test Dog',
                    'breed_name' => null,
                    'breed_name_secondary' => null,
                    'breeding_house' => null,
                    'color_name' => null,
                    'color_name_secondary' => null,
                    'birth_date' => null,
                    'age' => null,
                    'gender_value' => null,
                    'gender_label_raw' => null,
                    'father_sagir' => null,
                    'mother_sagir' => null,
                    'chip' => null,
                    'reg_date' => null,
                    'pedigree_notes' => null,
                    'owners' => [],
                    'owner_names_text' => null,
                    'owner_address_display' => null,
                    'breeder_names' => [],
                    'breeder_text' => null,
                    'titles' => [],
                    'titles_count' => 0,
                    'titles_text' => null,
                ],
                'depth' => $depth,
                'column_count' => max(1, $depth),
                'row_count' => 0,
                'generation_headers' => [],
                'ancestor_columns' => [],
                'nodes' => [],
                'meta' => [
                    'depth' => $depth,
                    'direction' => $direction,
                    'include_node_titles' => $includeNodeTitles,
                ],
            ];
        }
    });
});

it('uses config defaults and can hide the builder form', function () {
    Livewire::test(PedigreeTree::class, [
        'dogId' => 123,
        'showBuilder' => false,
    ])
        ->assertSet('showBuilder', false)
        ->assertSet('depth', 4)
        ->assertSet('density', 'comfortable')
        ->assertSet('visibleNodeFields.age', false)
        ->assertSet('visibleNodeFields.titles', false)
        ->assertDontSee(__('Apply'));
});

it('respects caller overrides and resets back to the initial caller settings', function () {
    Livewire::test(PedigreeTree::class, [
        'dogId' => 456,
        'showBuilder' => true,
        'settings' => [
            'depth' => 5,
            'density' => 'compact',
            'visible_fields' => [
                'titles' => true,
                'age' => true,
            ],
        ],
    ])
        ->assertSet('depth', 5)
        ->assertSet('density', 'compact')
        ->assertSet('visibleNodeFields.titles', true)
        ->assertSet('visibleNodeFields.age', true)
        ->set('settingsData.depth', 7)
        ->set('settingsData.density', 'comfortable')
        ->set('settingsData.visible_fields.titles', false)
        ->call('submitSettings')
        ->assertSet('depth', 7)
        ->assertSet('density', 'comfortable')
        ->assertSet('visibleNodeFields.titles', false)
        ->call('resetCertificateSettings')
        ->assertSet('depth', 5)
        ->assertSet('density', 'compact')
        ->assertSet('visibleNodeFields.titles', true)
        ->assertSet('visibleNodeFields.age', true)
        ->assertSee(__('Apply'));
});
