<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\ClassSchedule;
use App\Models\StudentLogin;
use App\Models\Attendance;
use App\Models\StudentProgress;
use App\Models\FacultyMessage;
use Carbon\Carbon;

class FacultyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create a faculty user
        $faculty = User::where('user_role', 5)->first();
        
        if (!$faculty) {
            $this->command->info('No faculty user found. Please create a faculty user first.');
            return;
        }

        $tcCode = $faculty->from_tc;

        // Create subjects
        $subjects = [
            [
                'name' => 'Mathematics',
                'code' => 'MATH101',
                'description' => 'Advanced Mathematics for Class 10',
                'class_level' => 'Class 10',
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY101',
                'description' => 'Physics Fundamentals for Class 11',
                'class_level' => 'Class 11',
            ],
            [
                'name' => 'Computer Science',
                'code' => 'CS101',
                'description' => 'Introduction to Computer Science for Class 12',
                'class_level' => 'Class 12',
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHEM101',
                'description' => 'Chemistry for Class 11',
                'class_level' => 'Class 11',
            ],
        ];

        foreach ($subjects as $subjectData) {
            Subject::updateOrCreate(
                ['code' => $subjectData['code']],
                array_merge($subjectData, [
                    'tc_code' => $tcCode,
                    'faculty_id' => $faculty->id,
                    'is_active' => true,
                ])
            );
        }

        // Get created subjects
        $createdSubjects = Subject::where('faculty_id', $faculty->id)->get();

        // Create class schedules
        $schedules = [
            ['subject' => 'Mathematics', 'day' => 'Monday', 'start_time' => '09:00', 'end_time' => '10:30', 'room' => 'Room 101'],
            ['subject' => 'Physics', 'day' => 'Tuesday', 'start_time' => '10:30', 'end_time' => '12:00', 'room' => 'Room 102'],
            ['subject' => 'Computer Science', 'day' => 'Wednesday', 'start_time' => '14:00', 'end_time' => '15:30', 'room' => 'Lab 1'],
            ['subject' => 'Chemistry', 'day' => 'Thursday', 'start_time' => '09:00', 'end_time' => '10:30', 'room' => 'Room 103'],
            ['subject' => 'Mathematics', 'day' => 'Friday', 'start_time' => '10:30', 'end_time' => '12:00', 'room' => 'Room 101'],
        ];

        foreach ($schedules as $scheduleData) {
            $subject = $createdSubjects->where('name', $scheduleData['subject'])->first();
            if ($subject) {
                ClassSchedule::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'day_of_week' => $scheduleData['day'],
                        'start_time' => $scheduleData['start_time'],
                    ],
                    [
                        'tc_code' => $tcCode,
                        'class_level' => $subject->class_level,
                        'end_time' => $scheduleData['end_time'],
                        'room_number' => $scheduleData['room'],
                        'faculty_id' => $faculty->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Get students for this TC
        $students = StudentLogin::where('tc_code', $tcCode)->get();

        if ($students->count() > 0) {
            // Create sample attendance records for today
            $today = Carbon::today();
            foreach ($students as $student) {
                foreach ($createdSubjects as $subject) {
                    Attendance::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'date' => $today,
                        ],
                        [
                            'faculty_id' => $faculty->id,
                            'tc_code' => $tcCode,
                            'status' => rand(0, 10) > 2 ? 'present' : 'absent', // 80% attendance rate
                            'remarks' => null,
                        ]
                    );
                }
            }

            // Create sample student progress records
            $assessmentTypes = ['quiz', 'test', 'assignment', 'exam'];
            $titles = [
                'quiz' => ['Chapter 1 Quiz', 'Mid-term Quiz', 'Weekly Quiz'],
                'test' => ['Unit Test 1', 'Unit Test 2', 'Practice Test'],
                'assignment' => ['Homework Assignment 1', 'Project Submission', 'Lab Report'],
                'exam' => ['Final Exam', 'Mid-term Exam', 'Comprehensive Test'],
            ];

            foreach ($students as $student) {
                foreach ($createdSubjects as $subject) {
                    // Create 2-4 progress records per student per subject
                    $numRecords = rand(2, 4);
                    for ($i = 0; $i < $numRecords; $i++) {
                        $assessmentType = $assessmentTypes[array_rand($assessmentTypes)];
                        $title = $titles[$assessmentType][array_rand($titles[$assessmentType])];
                        $score = rand(60, 95);
                        $maxScore = 100;
                        $assessmentDate = $today->copy()->subDays(rand(1, 30));

                        StudentProgress::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'faculty_id' => $faculty->id,
                            'tc_code' => $tcCode,
                            'assessment_type' => $assessmentType,
                            'title' => $title,
                            'score' => $score,
                            'max_score' => $maxScore,
                            'assessment_date' => $assessmentDate,
                            'comments' => 'Good performance. Keep it up!',
                            'grade' => $score >= 90 ? 'A' : ($score >= 80 ? 'B' : ($score >= 70 ? 'C' : ($score >= 60 ? 'D' : 'F'))),
                        ]);
                    }
                }
            }

            // Create sample messages
            $messageSubjects = [
                'Class Announcement',
                'Homework Reminder',
                'Exam Schedule Update',
                'Important Notice',
                'Class Cancellation',
            ];

            $messageBodies = [
                'Please remember to submit your assignments by Friday.',
                'Next week we will have a quiz on Chapter 3.',
                'The exam schedule has been updated. Please check the notice board.',
                'Important: Bring your lab notebooks for tomorrow\'s practical.',
                'Class is cancelled for tomorrow due to faculty meeting.',
            ];

            // Create individual messages
            foreach ($students->take(3) as $student) {
                FacultyMessage::create([
                    'faculty_id' => $faculty->id,
                    'student_id' => $student->id,
                    'tc_code' => $tcCode,
                    'subject' => $messageSubjects[array_rand($messageSubjects)],
                    'message' => $messageBodies[array_rand($messageBodies)],
                    'message_type' => 'individual',
                    'is_read' => rand(0, 1),
                    'read_at' => rand(0, 1) ? Carbon::now()->subHours(rand(1, 24)) : null,
                ]);
            }

            // Create class-wide messages
            $classLevels = ['Class 10', 'Class 11', 'Class 12'];
            foreach ($classLevels as $classLevel) {
                $classStudents = $students->where('class', $classLevel);
                if ($classStudents->count() > 0) {
                    foreach ($classStudents as $student) {
                        FacultyMessage::create([
                            'faculty_id' => $faculty->id,
                            'student_id' => $student->id,
                            'tc_code' => $tcCode,
                            'subject' => 'Class Announcement for ' . $classLevel,
                            'message' => 'This is a class-wide announcement for ' . $classLevel . '. Please check your schedule for updates.',
                            'message_type' => 'class',
                            'target_class' => $classLevel,
                            'is_read' => rand(0, 1),
                            'read_at' => rand(0, 1) ? Carbon::now()->subHours(rand(1, 48)) : null,
                        ]);
                    }
                }
            }
        }

        $this->command->info('Faculty data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . $createdSubjects->count() . ' subjects');
        $this->command->info('- ' . ClassSchedule::where('faculty_id', $faculty->id)->count() . ' schedules');
        $this->command->info('- ' . Attendance::where('faculty_id', $faculty->id)->count() . ' attendance records');
        $this->command->info('- ' . StudentProgress::where('faculty_id', $faculty->id)->count() . ' progress records');
        $this->command->info('- ' . FacultyMessage::where('faculty_id', $faculty->id)->count() . ' messages');
    }
}
