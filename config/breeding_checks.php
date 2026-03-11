<?php

return [
    'states' => [
        'absolute_yes' => [
            'label' => 'Yes',
            'icon' => 'heroicon-m-check-circle',
            'color' => 'success',
        ],
        'absolute_no' => [
            'label' => 'No',
            'icon' => 'heroicon-m-x-circle',
            'color' => 'danger',
        ],
        'check_needed' => [
            'label' => 'Check needed',
            'icon' => 'heroicon-m-exclamation-triangle',
            'color' => 'warning',
        ],
    ],

    'rules' => [
        'female' => [
            'min_age_months' => 12,
            'max_breedings' => 6,
            'checks' => [
                'breeding_approval',
                'age',
                'dna',
                'red_pedigree',
                'breeding_count',
                'last_breeding_date',
            ],
        ],
        'male' => [
            'min_age_months' => 12,
            'max_breedings' => null,
            'checks' => [
                'breeding_approval',
                'age',
                'dna',
                'red_pedigree',
                'breeding_count',
            ],
        ],
    ],

    'dog' => [
        'approval_attribute_candidates' => [
            'ForBreed',
            'ForBreeding',
            'DogToBreeding',
            'BreedingApproved',
            'ApproveBreeding',
        ],
    ],

    'actions' => [
        'request_dna_test' => [
            'label' => 'Request DNA test',
            'icon' => 'heroicon-m-beaker',
            'color' => 'warning',
            'visible_when' => ['dna' => 'check_needed'],
            'modal_heading' => 'DNA test request',
            'modal_description' => 'Placeholder DNA request flow.',
        ],
        'report_dna_id' => [
            'label' => 'Report DNA ID',
            'icon' => 'heroicon-m-pencil-square',
            'color' => 'gray',
            'visible_when' => ['dna' => 'check_needed'],
            'modal_heading' => 'Report DNA ID',
            'modal_description' => 'Placeholder DNA ID reporting flow.',
        ],
    ],

    'membership_types' => [
        'owner_breed_club' => [
            'label' => 'Owner / breed club',
            'color' => 'success',
            'icon' => 'heroicon-m-user-circle',
        ],
        'owner_any_club' => [
            'label' => 'Owner / any club',
            'color' => 'info',
            'icon' => 'heroicon-m-building-office-2',
        ],
        'at_least_one_co_owner_breed_club' => [
            'label' => 'Co-owner / breed club',
            'color' => 'warning',
            'icon' => 'heroicon-m-users',
        ],
        'all_owners_breed_club' => [
            'label' => 'All owners / breed club',
            'color' => 'success',
            'icon' => 'heroicon-m-shield-check',
        ],
    ],
];
