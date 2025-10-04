<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TcLms;
use App\Models\User;
use App\Models\LmsDepartment;

class LmsContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a faculty user (role 5)
        $faculty = User::where('user_role', 5)->first();
        
        if (!$faculty) {
            // Create a sample faculty user if none exists
            $faculty = User::create([
                'name' => 'Dr. Rajesh Kumar',
                'email' => 'rajesh.kumar@msme.gov.in',
                'password' => bcrypt('password'),
                'user_role' => 5,
                'from_tc' => 'TC001',
                'is_active' => true,
            ]);
        }

        // Get departments
        $departments = LmsDepartment::all();
        
        if ($departments->isEmpty()) {
            // Create sample departments if none exist
            $departments = collect([
                LmsDepartment::create([
                    'department_name' => 'Computer Science',
                    'department_slug' => 'computer-science',
                    'description' => 'Computer Science and Information Technology',
                    'is_active' => true,
                    'created_by' => 1,
                ]),
                LmsDepartment::create([
                    'department_name' => 'Civil Engineering',
                    'department_slug' => 'civil-engineering',
                    'description' => 'Civil Engineering and Construction',
                    'is_active' => true,
                    'created_by' => 1,
                ]),
                LmsDepartment::create([
                    'department_name' => 'Business Management',
                    'department_slug' => 'business-management',
                    'description' => 'Business Administration and Management',
                    'is_active' => true,
                    'created_by' => 1,
                ]),
            ]);
        }

        // Sample approved LMS content
        $sampleContent = [
            [
                'site_title' => 'Advanced Web Development with Laravel',
                'site_description' => 'Comprehensive course covering modern web development using Laravel framework, including MVC architecture, database management, and API development.',
                'site_department' => 'Computer Science',
                'site_contents' => '<h2>Course Overview</h2><p>This course provides hands-on training in Laravel framework development, covering both frontend and backend technologies.</p><h3>Learning Objectives</h3><ul><li>Master Laravel framework fundamentals</li><li>Build dynamic web applications</li><li>Implement secure authentication systems</li><li>Create RESTful APIs</li></ul><h3>Prerequisites</h3><p>Basic knowledge of PHP and HTML/CSS is recommended.</p>',
                'seo_title' => 'Advanced Web Development with Laravel - Complete Course',
                'seo_description' => 'Learn Laravel framework from scratch. Build modern web applications with PHP. Comprehensive course with hands-on projects.',
                'seo_keywords' => 'Laravel, PHP, Web Development, MVC, Framework, Programming, Backend Development',
                'seo_slug' => 'advanced-web-development-laravel',
                'status' => 'approved',
                'is_approved' => true,
            ],
            [
                'site_title' => 'Sustainable Construction Practices',
                'site_description' => 'Explore modern sustainable construction techniques, green building materials, and environmental impact assessment in civil engineering projects.',
                'site_department' => 'Civil Engineering',
                'site_contents' => '<h2>Course Overview</h2><p>This course focuses on sustainable construction methods and their environmental benefits.</p><h3>Key Topics</h3><ul><li>Green building materials</li><li>Energy-efficient design</li><li>Waste reduction strategies</li><li>LEED certification process</li></ul><h3>Case Studies</h3><p>Real-world examples of sustainable construction projects from around the world.</p>',
                'seo_title' => 'Sustainable Construction Practices - Green Building Course',
                'seo_description' => 'Learn sustainable construction techniques and green building practices. Environmental engineering course for civil engineers.',
                'seo_keywords' => 'Sustainable Construction, Green Building, Civil Engineering, Environmental, LEED, Eco-friendly',
                'seo_slug' => 'sustainable-construction-practices',
                'status' => 'approved',
                'is_approved' => true,
            ],
            [
                'site_title' => 'Digital Marketing Strategy for MSMEs',
                'site_description' => 'Comprehensive guide to digital marketing strategies specifically designed for Micro, Small, and Medium Enterprises (MSMEs) in India.',
                'site_department' => 'Business Management',
                'site_contents' => '<h2>Course Overview</h2><p>This course helps MSMEs leverage digital marketing to grow their business and reach new customers.</p><h3>Modules Covered</h3><ul><li>Social Media Marketing</li><li>Search Engine Optimization (SEO)</li><li>Content Marketing</li><li>Email Marketing</li><li>Google Ads and Facebook Ads</li></ul><h3>Government Initiatives</h3><p>Learn about Digital India initiatives and how MSMEs can benefit from government support programs.</p>',
                'seo_title' => 'Digital Marketing Strategy for MSMEs - Complete Guide',
                'seo_description' => 'Digital marketing course for MSMEs. Learn social media, SEO, content marketing strategies for small businesses in India.',
                'seo_keywords' => 'Digital Marketing, MSME, Small Business, Social Media, SEO, Content Marketing, India',
                'seo_slug' => 'digital-marketing-strategy-msmes',
                'status' => 'approved',
                'is_approved' => true,
            ],
            [
                'site_title' => 'Machine Learning Fundamentals',
                'site_description' => 'Introduction to machine learning concepts, algorithms, and practical applications using Python programming language.',
                'site_department' => 'Computer Science',
                'site_contents' => '<h2>Course Overview</h2><p>This course introduces students to the fundamentals of machine learning and artificial intelligence.</p><h3>Topics Covered</h3><ul><li>Supervised Learning</li><li>Unsupervised Learning</li><li>Neural Networks</li><li>Data Preprocessing</li><li>Model Evaluation</li></ul><h3>Practical Projects</h3><p>Hands-on projects including image classification, text analysis, and predictive modeling.</p>',
                'seo_title' => 'Machine Learning Fundamentals - AI Course for Beginners',
                'seo_description' => 'Learn machine learning from scratch. Python-based AI course covering algorithms, neural networks, and practical projects.',
                'seo_keywords' => 'Machine Learning, Artificial Intelligence, Python, Data Science, Neural Networks, Algorithms',
                'seo_slug' => 'machine-learning-fundamentals',
                'status' => 'approved',
                'is_approved' => true,
            ],
            [
                'site_title' => 'Project Management for Engineers',
                'site_description' => 'Essential project management skills for engineering professionals, covering planning, execution, and monitoring of engineering projects.',
                'site_department' => 'Civil Engineering',
                'site_contents' => '<h2>Course Overview</h2><p>This course provides comprehensive project management training specifically for engineering professionals.</p><h3>Key Areas</h3><ul><li>Project Planning and Scheduling</li><li>Resource Management</li><li>Risk Assessment</li><li>Quality Control</li><li>Team Leadership</li></ul><h3>Industry Standards</h3><p>Learn about PMI standards and best practices in engineering project management.</p>',
                'seo_title' => 'Project Management for Engineers - Professional Course',
                'seo_description' => 'Project management course for engineers. Learn planning, scheduling, risk management for engineering projects.',
                'seo_keywords' => 'Project Management, Engineering, PMI, Planning, Scheduling, Risk Management, Leadership',
                'seo_slug' => 'project-management-engineers',
                'status' => 'approved',
                'is_approved' => true,
            ],
            [
                'site_title' => 'Financial Management for MSMEs',
                'site_description' => 'Comprehensive financial management course designed for Micro, Small, and Medium Enterprises to improve financial planning and decision-making.',
                'site_department' => 'Business Management',
                'site_contents' => '<h2>Course Overview</h2><p>This course helps MSMEs understand and implement effective financial management practices.</p><h3>Core Topics</h3><ul><li>Financial Planning and Budgeting</li><li>Cash Flow Management</li><li>Investment Analysis</li><li>Risk Management</li><li>Government Schemes and Subsidies</li></ul><h3>Case Studies</h3><p>Real-world examples of successful MSME financial management strategies.</p>',
                'seo_title' => 'Financial Management for MSMEs - Complete Guide',
                'seo_description' => 'Financial management course for MSMEs. Learn budgeting, cash flow, investment analysis for small businesses.',
                'seo_keywords' => 'Financial Management, MSME, Small Business, Budgeting, Cash Flow, Investment, Government Schemes',
                'seo_slug' => 'financial-management-msmes',
                'status' => 'approved',
                'is_approved' => true,
            ],
        ];

        foreach ($sampleContent as $content) {
            $department = $departments->where('department_name', $content['site_department'])->first();
            
            if ($department) {
                TcLms::create([
                    'tc_code' => $faculty->from_tc,
                    'faculty_code' => $faculty->email,
                    'site_url' => $content['seo_slug'],
                    'site_department' => $content['site_department'],
                    'site_title' => $content['site_title'],
                    'site_description' => $content['site_description'],
                    'site_contents' => $content['site_contents'],
                    'seo_title' => $content['seo_title'],
                    'seo_description' => $content['seo_description'],
                    'seo_keywords' => $content['seo_keywords'],
                    'seo_slug' => $content['seo_slug'],
                    'structured_data' => json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'EducationalOccupationalProgram',
                        'name' => $content['site_title'],
                        'description' => $content['site_description'],
                        'provider' => [
                            '@type' => 'Organization',
                            'name' => 'MSME Technology Centre'
                        ],
                        'educationalLevel' => 'Undergraduate',
                        'occupationalCategory' => $content['site_department'],
                        'url' => url('/lms/' . $content['seo_slug'])
                    ]),
                    'status' => $content['status'],
                    'is_approved' => $content['is_approved'],
                ]);
            }
        }

        $this->command->info('Sample LMS content created successfully!');
    }
}