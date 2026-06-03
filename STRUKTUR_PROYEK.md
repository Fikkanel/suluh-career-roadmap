# Pemetaan Struktur Folder Proyek — Suluh Career Roadmap Platform

Dokumen ini menyajikan peta struktur direktori utama proyek Laravel **Suluh**, yang menggambarkan relasi hierarki antarkomponen di folder-folder induk seperti `app`, `config`, `database`, `resources`, `routes`, dan `tests`.

---

## 1. Direktori: `app/`
Folder ini berisi inti kode logika bisnis dari aplikasi, termasuk *Controller*, *Middleware*, *Models*, *Repositories*, dan *Services*.

```
app/
├── Console/
├── Events/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Api/
│   │   │   ├── AssessmentController.php
│   │   │   ├── AuthController.php
│   │   │   ├── CareerController.php
│   │   │   ├── ImpactController.php
│   │   │   ├── ProfileController.php
│   │   │   └── ProgressController.php
│   │   ├── Institution/
│   │   ├── Mentor/
│   │   ├── ArchiveController.php
│   │   ├── AssessmentController.php
│   │   ├── AuthController.php
│   │   ├── ChatbotController.php
│   │   ├── DashboardController.php
│   │   ├── DataEthicsController.php
│   │   ├── ExportController.php
│   │   ├── OnboardingController.php
│   │   ├── PivotController.php
│   │   ├── PublicController.php
│   │   ├── PublicProfileController.php
│   │   ├── RoadmapController.php
│   │   ├── SkillProgressController.php
│   │   ├── SkillValidationController.php
│   │   └── SurveyController.php
│   └── Middleware/
│       ├── EnsureAssessmentCompleted.php
│       └── EnsureOnboardingCompleted.php
├── Listeners/
├── Models/
│   ├── AssessmentQuestion.php
│   ├── AssessmentResult.php
│   ├── Career.php
│   ├── ContextScore.php
│   ├── EthicsDecision.php
│   ├── ImpactSurvey.php
│   ├── LlmNarrativeCache.php
│   ├── MentorFeedback.php
│   ├── RoadmapArchive.php
│   ├── Skill.php
│   ├── SkillValidation.php
│   ├── User.php
│   └── UserProgress.php
├── Notifications/
├── Policies/
├── Providers/
├── Repositories/
│   ├── Contracts/
│   │   ├── UserRepositoryInterface.php
│   │   ├── AssessmentRepositoryInterface.php
│   │   ├── RoadmapRepositoryInterface.php
│   │   ├── ProgressRepositoryInterface.php
│   │   └── ImpactRepositoryInterface.php
│   └── Eloquent/
│       ├── UserRepository.php
│       ├── AssessmentRepository.php
│       ├── RoadmapRepository.php
│       ├── ProgressRepository.php
│       └── ImpactRepository.php
└── Services/
    ├── ScoringService.php
    ├── RoadmapGeneratorService.php
    ├── ContextScoreService.php
    ├── LLMNarrativeService.php
    └── ImpactCalculatorService.php
```

---

## 2. Direktori: `config/`
Folder ini menyimpan semua konfigurasi global aplikasi.

```
config/
├── app.php
├── auth.php
├── cache.php
├── database.php
├── filesystems.php
├── jwt.php
├── logging.php
├── mail.php
├── queue.php
├── services.php
└── session.php
```

---

## 3. Direktori: `database/`
Folder ini berisi konfigurasi database, berkas migrasi tabel, pabrik model (*factories*), dan data awal penyemaian (*seeders*).

```
database/
├── factories/
│   └── UserFactory.php
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2026_05_16_193625_create_careers_table.php
│   ├── 2026_05_16_193627_create_skills_table.php
│   ├── 2026_05_16_193630_create_assessment_questions_table.php
│   ├── 2026_05_16_193632_create_assessment_results_table.php
│   ├── 2026_05_16_193635_create_user_progress_table.php
│   ├── 2026_05_16_193637_create_roadmap_archives_table.php
│   ├── 2026_05_16_193640_create_impact_surveys_table.php
│   ├── 2026_05_16_193642_create_mentor_feedback_table.php
│   ├── 2026_05_16_193645_create_context_scores_table.php
│   ├── 2026_05_16_193647_create_llm_narrative_caches_table.php
│   ├── 2026_05_26_000001_add_google_id_and_current_career_to_users_table.php
│   ├── 2026_05_26_000002_add_summary_columns_to_roadmap_archives_table.php
│   ├── 2026_05_28_103353_encrypt_sensitive_columns_and_add_pseudonym.php
│   ├── 2026_05_28_103631_add_province_to_users_table.php
│   ├── 2026_05_28_104000_add_readiness_fields_to_users_table.php
│   ├── 2026_05_28_104515_create_skill_validations_table.php
│   ├── 2026_05_28_104516_add_validation_fields_to_skills_table.php
│   ├── 2026_05_29_115956_add_role_to_users_table.php
│   ├── 2026_05_29_130000_change_json_to_text_for_encrypted_columns.php
│   ├── 2026_05_29_140000_create_notifications_table.php
│   ├── 2026_05_29_150000_add_public_profile_to_users_table.php
│   ├── 2026_05_29_160000_create_ethics_decisions_table.php
│   ├── 2026_05_30_144512_add_avatar_to_users_table.php
│   ├── 2026_06_02_063144_add_major_to_users_table.php
│   └── 2026_06_03_041000_add_api_key_to_users_table.php
└── seeders/
    ├── AssessmentQuestionSeeder.php
    ├── CareerSeeder.php
    ├── DatabaseSeeder.php
    ├── DemoUserProgressSeeder.php
    ├── ImpactStatSeeder.php
    └── SkillSeeder.php
```

---

## 4. Direktori: `resources/`
Folder ini berisi berkas-berkas aset mentah seperti gaya CSS, skrip Javascript, dan tampilan Blade HTML.

```
resources/
├── css/
│   └── app.css
├── js/
│   ├── app.js
│   └── bootstrap.js
└── views/
    ├── admin/
    ├── app/
    │   ├── archive.blade.php
    │   ├── assessment.blade.php
    │   ├── assessment-result.blade.php
    │   ├── career-detail.blade.php
    │   ├── dashboard.blade.php
    │   ├── export.blade.php
    │   ├── notifications.blade.php
    │   ├── onboarding.blade.php
    │   ├── pivot.blade.php
    │   ├── profile-settings.blade.php
    │   ├── roadmap.blade.php
    │   ├── skill-progress.blade.php
    │   ├── skill-validation.blade.php
    │   └── survey.blade.php
    ├── auth/
    ├── components/
    │   ├── layouts/
    │   │   └── app.blade.php (Template Layout Utama & Chatbot)
    │   ├── assessment-question.blade.php
    │   └── career-card.blade.php
    ├── exports/
    ├── institution/
    ├── layouts/
    │   ├── admin.blade.php
    │   ├── app.blade.php
    │   ├── auth.blade.php
    │   └── public.blade.php
    ├── mentor/
    ├── public/
    └── welcome.blade.php
```

---

## 5. Direktori: `routes/`
Folder ini mendefinisikan seluruh rute web dan API dari aplikasi.

```
routes/
├── api.php
├── console.php
└── web.php
```

---

## 6. Direktori: `tests/`
Folder ini menampung unit test dan integrasi test untuk memvalidasi aplikasi.

```
tests/
├── Feature/
│   ├── Web/
│   │   ├── AdminAndInstitutionTest.php
│   │   ├── AssessmentTest.php
│   │   ├── ChatbotTest.php
│   │   ├── ImpactPageTest.php
│   │   ├── SkillValidationTest.php
│   │   └── SurveyTest.php
│   ├── ExampleTest.php
│   └── TestCase.php
├── Unit/
│   ├── Services/
│   │   ├── ContextScoreServiceTest.php
│   │   └── RoadmapGeneratorServiceTest.php
│   └── ExampleTest.php
└── TestCase.php
```
