<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'code' => 'IT',
                'name' => 'Information Technology Office',
                'description' => 'Information Technology and Systems Management',
                'is_active' => true,
            ],
            [
                'code' => 'HR',
                'name' => 'Human Resources',
                'description' => 'Human Resources and Personnel Management',
                'is_active' => true,
            ],
            [
                'code' => 'FIN',
                'name' => 'Finance and Accounting',
                'description' => 'Finance, Accounting, and Budget Management',
                'is_active' => true,
            ],
            [
                'code' => 'ACAD',
                'name' => 'Academic Affairs',
                'description' => 'Academic Programs and Curriculum Development',
                'is_active' => true,
            ],
            [
                'code' => 'SA',
                'name' => 'Student Affairs',
                'description' => 'Student Services and Support',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'description' => $department['description'],
                    'is_active' => $department['is_active'],
                ]
            );
        }
    }
}
