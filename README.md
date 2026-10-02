<h1>Peakscore</h1>

## Overview

**Peakscore** is a web-based examination management system designed for organizations such as schools, universities, and testing institutions to conduct and manage academic potential tests.

The application provides two separate portals:

* **Admin Portal**: Allows test organizers to manage question banks, create test schedules, manage participant accounts, review test results, and issue transcripts.
* **Participant Portal**: Allows registered participants to access scheduled tests, complete assessments, and view their published results and transcripts.

The platform uses an **organization-managed registration model**, where participant accounts are created and managed by the test organizer. Participants cannot register independently, allowing organizations to control participant access and test eligibility.

## Core Features

* Question bank management
* Test scheduling and management
* Participant CRUD management
* Online test administration
* Automated test result processing
* Result monitoring and review
* Transcript generation and issuance
* Role-based access between administrators and participants

## User Roles

### Admin
- Manage question bank
- Create and manage test schedules
- Manage participant accounts
- Review participant test results
- Issue participant transcripts

### Participant
- Access assigned tests
- Take tests within the scheduled period
- View test results
- View issued transcripts

## Application Flow

1. Admin registers participant accounts.
2. Admin creates and manages the test questions.
3. Admin creates a test schedule and assigns participants.
4. Participants log in and take the test during the scheduled period.
5. The system processes and records the test results.
6. Admin reviews the results.
7. Admin publishes the participant's transcript.
8. Participants can view their published results and transcript.

```
Admin
  │
  ├── Manage Questions
  ├── Register Participants
  └── Create Test Schedule
              │
              ▼
        Participant
              │
              ▼
          Take Test
              │
              ▼
       Process Results
              │
              ▼
            Admin
              │
              ▼
       Issue Transcript
              │
              ▼
        Participant
```
## Tech Stack

- Laravel 11
- MariaDB
- Tailwind CSS v4 (CDN)
- Alpine.js 3 (CDN)

## Project Structure
```
└── project/
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers
    │   │   └── Middleware
    │   ├── Models
    │   ├── Provider
    │   └── View/
    │       └── Components
    ├── bootstrap
    ├── config
    ├── database/
    │   ├── factories
    │   ├── migrations
    │   ├── seeders
    │   └── .gitignore
    ├── public/
    │   ├── js
    │   ├── uploads/
    │   │   └── soal
    │   └── favicon.ico
    ├── resources/
    │   ├── css
    │   ├── js
    │   └── view/
    │       ├── layouts
    │       ├── participants
    │       ├── peserta
    │       ├── questions
    │       ├── results
    │       ├── schedules
    │       ├── vendor/
    │       │   └── pagination
    │       ├── index.blade.php
    │       └── signIn.blade.php
    ├── routes/
    │   ├── console.php
    │   └── web.php
    ├── storage/
    │   ├── app
    │   ├── framework
    │   └── logs
    ├── tests/
    │   ├── Feature
    │   ├── Unit
    │   └── TestCase.php
    ├── .editorconfig
    ├── .env.example
    ├── .gitattributes
    ├── .gitignore
    ├── artisan
    ├── composer.json
    ├── composer.lock
    ├── package.json
    ├── phpunit.xml
    └── vite.config.js
```

## Screenshot

### Login Page
![Login Page](https://github.com/user-attachments/assets/941f8ec4-13c7-4327-b2d1-efbbe4366c61)

### Admin Dashboard
![Admin Dashboard](https://github.com/user-attachments/assets/87b91e60-b295-42cd-94d6-74c29625167a)

### Test Schedules (Admin)
![Test Schedules (Admin)](https://github.com/user-attachments/assets/ba74456a-4b16-4d11-b8a2-4a117fcb7c87)

### New Schedules Modal
![New Schedules Modal](https://github.com/user-attachments/assets/ec988602-53db-49be-a5bf-57440b8285be)

### Question Bank
![Question Bank](https://github.com/user-attachments/assets/9f02823b-1844-49bc-81ea-456234773346)

### Add Question 
![Add Question](https://github.com/user-attachments/assets/ab3d41d5-c65b-4106-877e-15b5309d1e45)

### Participants Management
![Participants Management](https://github.com/user-attachments/assets/9cdea711-2692-4bde-867e-a21658e6dd44)

### Add Participants Modal
![Add Participants Modal](https://github.com/user-attachments/assets/bf4636c3-5906-4637-b669-01e2c8c2fca8)

### Test Results
![Test Results (Admin)](https://github.com/user-attachments/assets/4204ff64-0318-4772-9222-a9cc9b0fa911)

### Answer Details
![Answer Details](https://github.com/user-attachments/assets/5a541d97-cf9e-4144-a9d6-203e8b9c265d)

### Transcript
![Transcript](https://github.com/user-attachments/assets/750f911f-ec3f-46f3-b43f-59f02d921b7f)

### Test Schedule (Participant)
![Test Schedule (Participant)](https://github.com/user-attachments/assets/401deebb-44f0-45a5-9902-b29b8475fdb1)

### My Results
![My Results](https://github.com/user-attachments/assets/e5d598db-8114-4a8a-a0a4-642f676bd5f4)

## Contributors
[@erumiii](https://github.com/erumiii)



