<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'Information Technology Office',
                'code' => 'IT',
                'description' => 'Information Technology and Systems Management',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Human Resources and Personnel Management',
                'is_active' => true,
            ],
            [
                'name' => 'Finance and Accounting',
                'code' => 'FIN',
                'description' => 'Finance, Accounting, and Budget Management',
                'is_active' => true,
            ],
            [
                'name' => 'Academic Affairs',
                'code' => 'ACAD',
                'description' => 'Academic Programs and Curriculum Development',
                'is_active' => true,
            ],
            [
                'name' => 'Student Affairs',
                'code' => 'SA',
                'description' => 'Student Services and Support',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }
}
