# 📘 الدليل الفني الشامل والتوثيق المرجعي لمنظومة تدريب وتشغيل الخريجين
### جامعة طرابلس — مكتب تدريب وتأهيل الخريجين
**University of Tripoli — Graduate Training & Career Placement System (GTO)**

---

## 📑 فهرس المحتويات
1. [نظرة عامة على المنظومة والأهداف](#1-نظرة-عامة-على-المنظومة-والأهداف)
2. [المعمارية التقنية للمنظومة (System Architecture)](#2-المعمارية-التقنية-للمنظومة-system-architecture)
3. [الأدوار والصلاحيات (User Roles & RBAC Matrix)](#3-الأدوار-والصلاحيات-user-roles--rbac-matrix)
4. [مخططات حالات الاستخدام الشاملة (Use Case Diagrams)](#4-مخططات-حالات-الاستخدام-الشاملة-use-case-diagrams)
   - [4.1 المخطط العام لكافة الفئات (Master Use Case)](#41-المخطط-العام-لكافة-الفئات-master-use-case)
   - [4.2 مخطط حالات استخدام الإرشاد المهني والتوظيف](#42-مخطط-حالات-استخدام-الإرشاد-المهني-والتوظيف)
   - [4.3 مخطط حالات استخدام إدارة التدريب والتأهيل](#43-مخطط-حالات-استخدام-إدارة-التدريب-والتأهيل)
   - [4.4 مخطط حالات استخدام الخريج والباحث عن عمل](#44-مخطط-حالات-استخدام-الخريج-والباحث-عن-عمل)
   - [4.5 مخطط حالات استخدام وحدة الإعلام والتغطيات](#45-مخطط-حالات-استخدام-وحدة-الإعلام-والتغطيات)
   - [4.6 مخطط حالات استخدام الجودة والتقييم والمتابعة](#46-مخطط-حالات-استخدام-الجودة-والتقييم-والمتابعة)
5. [المخطط الهيكلي للبيانات (Entity Relationship Diagram - ERD)](#5-المخطط-الهيكلي-للبيانات-entity-relationship-diagram---erd)
6. [معمارية المساعد الذكي التنفيذي (AI Assistant & Copilot Architecture)](#6-معمارية-المساعد-الذكي-التنفيذي-ai-assistant--copilot-architecture)
   - [6.1 مخطط مسار تنفيذ الأوامر والقرارات الحساسة (Human-in-the-Loop)](#61-مخطط-مسار-تنفيذ-الأوامر-والقرارات-الحساسة-human-in-the-loop)
   - [6.2 جدول أدوات المساعد الذكي المسجلة](#62-جدول-أدوات-المساعد-الذكي-المسجلة)
7. [القطاعات والإدارات الوظيفية بالتفصيل](#7-القطاعات-والإدارات-الوظيفية-بالتفصيل)
8. [الأمان، التدقيق، ومراقبة العمليات (Security & Audit Logging)](#8-الأمان-التدقيق-ومراقبة-العمليات-security--audit-logging)

---

## 1. نظرة عامة على المنظومة والأهداف

**منظومة تدريب وتشغيل الخريجين** بجامعة طرابلس هي منصة مؤسسية ذكية متكاملة تهدف إلى:
1. **تجسير الفجوة** بين المخرجات الأكاديمية واحتياجات سوق العمل الليبي والإقليمي.
2. **أتمتة دورة التدريب والتأهيل** للخريجين عبر برامج تدريبية متخصصة ومقاعد تدريبية محددة.
3. **الترشيح الذكي للخريجين** إلى الفرص الوظيفية المتاحة لدى الشركات الشريكة استناداً للمعدل، التخصص، والمهارات.
4. **التغطية والرصد الإعلامي** لفعاليات معارض التوظيف وورش العمل وتوثيق التدريبات وإدارة منصة الأخبار والإعلانات.
5. **قياس الجودة والرضا المؤسسي** عبر استطلاعات رأي رقمية موثوقة.
6. **التمكين الإداري بالذكاء الاصطناعي (AI Copilot)** لتنفيذ العمليات الإدارية المعقدة بالأوامر المباشرة وتقديم إرشاد فوري.

---

## 2. المعمارية التقنية للمنظومة (System Architecture)

تم بناء المنظومة وفق نمط المعمارية الطبقية ثلاثية المستويات (**3-Tier MVC Architecture**) المدعومة بخدمات الذكاء الاصطناعي:

```mermaid
graph TB
    subgraph Client_Layer ["طبقة العميل وواجهة المستخدم (Frontend Layer)"]
        UI_Web["واجهات الويب المتجاوبة (Blade + Bootstrap 5.3)"]
        UI_Copilot["درج المساعد الذكي التفاعلي (AI Drawer & Chat)"]
        UI_Tables["جداول البيانات فائق الضغط (Compact Fast DataTables)"]
    end

    subgraph Security_Layer ["طبقة الحماية والتفويض (Security & Middleware)"]
        Auth_MW["توثيق الهوية (Auth & Session Management)"]
        Role_MW["التحقق من الدور والصلاحيات (Role & Permissions RBAC)"]
        Audit_MW["سجل الرقابة والتتبع اللحظي (Audit Trail Logging)"]
        CSRF_MW["حماية الطلبات (CSRF & Rate Limiting)"]
    end

    subgraph Business_Layer ["طبقة منطق الأعمال (Application & Domain Layer)"]
        C_Career["إدارة الإرشاد المهني والترشيح (CareerGuidanceController)"]
        C_Train["إدارة برامج التدريب (TrainingController)"]
        C_Media["إدارة الإعلام والتغطيات (News & MediaController)"]
        C_Quality["إدارة التقييم والجودة (Survey & QualityController)"]
        C_Admin["إدارة النظام والمستخدمين (UserController & AdminReportController)"]
        
        subgraph AI_Engine ["محرك الذكاء الاصطناعي (AI Copilot Subsystem)"]
            AI_Service["خدمة المساعد الذكي (AiAssistantService)"]
            AI_Registry["سجل الأدوات التنفيذية (AiToolRegistry)"]
            AI_Guide["محرك التوجيه والإرشاد (Domain Expert How-To Engine)"]
            AI_HIL["نظام الاعتماد البشري (Human-in-the-Loop Proposal Engine)"]
        end
    end

    subgraph Data_Layer ["طبقة البيانات والتخزين (Data & Storage Layer)"]
        DB_MySQL[("قاعدة بيانات MySQL المنطقية")]
        Storage_Local["ملفات السير الذاتية والصور (Public/Private Storage)"]
        Cache_System["المؤقتات والجلسات (Cache & Session Store)"]
    end

    Client_Layer --> Security_Layer
    Security_Layer --> Business_Layer
    Business_Layer --> AI_Engine
    Business_Layer --> Data_Layer
    AI_Engine --> Data_Layer
```

---

## 3. الأدوار والصلاحيات (User Roles & RBAC Matrix)

| الدور الوظيفي | الرمز البرمجي | الصلاحيات والمسؤوليات الرئيسية |
| :--- | :--- | :--- |
| **مدير النظام** | `admin` | الصلاحيات العليا المطلقة لكافة القطاعات: إدارة المستخدمين، السجلات الرقابية، التقارير التنفيذية، وإدارة كافة الإدارات. |
| **مسؤول الإرشاد المهني** | `career_guidance_officer` | إدارة سجلات الخريجين، البحث المتقدم، استيراد وتصدير الخريجين، تجميد/تنشيط/حذف الحسابات، ترشيح الخريجين للوظائف، ونشر فرص العمل. |
| **منسق التدريب** | `training_coordinator` | إنشاء وإدارة البرامج التدريبية، حجز المقاعد، إدارة وفرز طلبات التدريب، قبول ورفض الطلبات فردياً وجماعياً. |
| **مسؤول التقييم والجودة** | `evaluation_officer` / `staff` | بناء وتوزيع استطلاعات الرأي، متابعة التقييمات، استخراج مؤشرات الأداء والرضا المؤسسي. |
| **مسؤول وحدة الإعلام** | `media_officer` | نشر الأخبار الصحفية، إدارة شريط الإعلانات، تقويم وجدول التغطيات، وتوثيق الفعاليات والمعارض. |
| **الشركة الشريكة / جهة العمل** | `company` | استعراض ملفات وسير الخريجين المرشحين، تحديد حالة المقابلات والتوظيف، وطلب مرشحين إضافيين. |
| **الخريج / الطالب** | `graduate` | بناء السيرة الذاتية، التقديم على البرامج التدريبية، التقديم على فرص العمل، واستخدام المساعد الذكي الخاص به. |

---

## 4. مخططات حالات الاستخدام الشاملة (Use Case Diagrams)

### 4.1 المخطط العام لكافة الفئات (Master Use Case)

```mermaid
flowchart LR
    Admin["👑 مدير النظام (Admin)"]
    Officer["🎯 مسؤول الإرشاد المهني"]
    Coord["🎓 منسق التدريب"]
    Media["📹 مسؤول الإعلام والتغطيات"]
    Quality["📝 مسؤول الجودة والتقييم"]
    Grad["👨‍🎓 الخريج (Graduate)"]
    Company["🏢 الشركة الشريكة (Company)"]

    subgraph System_Boundary ["منظومة تدريب وتشغيل الخريجين — جامعة طرابلس"]
        UC1(["إدارة الحسابات وتعيين الصلاحيات"])
        UC2(["تجميد / تنشيط / حذف حساب خريج"])
        UC3(["إدارة قاعدة بيانات الخريجين واستيراد Excel"])
        UC4(["ترشيح الخريجين للوظائف (فردي وجماعي)"])
        UC5(["إضافة ونشر فرص العمل والشركات"])
        UC6(["إنشاء برامج التدريب وتحديد المقاعد"])
        UC7(["فرز وقبول/رفض طلبات الالتحاق بالتدريب"])
        UC8(["نشر الأخبار والإعلانات الرسمية"])
        UC9(["إدارة تقويم وجدول التغطيات الصحفية"])
        UC10(["إعداد وتوزيع استبيانات الرضا والتقييم"])
        UC11(["تقديم طلب تدريب أو وظيفة"])
        UC12(["بناء وتحديث السيرة الذاتية"])
        UC13(["مراجعة المرشحين وتأكيد التوظيف"])
        UC14(["استخدام المساعد الذكي لإدارة العمليات"])
    end

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC14

    Officer --> UC2
    Officer --> UC3
    Officer --> UC4
    Officer --> UC5
    Officer --> UC14

    Coord --> UC6
    Coord --> UC7
    Coord --> UC14

    Media --> UC8
    Media --> UC9
    Media --> UC14

    Quality --> UC10
    Quality --> UC14

    Grad --> UC11
    Grad --> UC12
    Grad --> UC14

    Company --> UC13
    Company --> UC5
```

---

### 4.2 مخطط حالات استخدام الإرشاد المهني والتوظيف
*(مسؤول الإرشاد المهني والمدير العام)*

```mermaid
flowchart TB
    Actor_Career["🎯 مسؤول الإرشاد المهني / المدير"]

    subgraph Module_Career ["قطاع الإرشاد المهني وتشغيل الخريجين"]
        UC_ViewGrads(["استعراض جدول الخريجين المضغوط (3,000+ خريج)"])
        UC_SearchGrads(["البحث المتقدم (التخصص، المعدل، الهاتف، السيرة الذاتية)"])
        UC_ImportGrads(["استيراد قوائم الخريجين دفعة واحدة عبر ملفات Excel/CSV"])
        UC_ExportGrads(["تصدير تقارير الخريجين وسجلات التوظيف (Excel / PDF)"])
        UC_FreezeGrad(["تجميد حساب الخريج وإيقاف تسجيل الدخول"])
        UC_UnfreezeGrad(["إلغاء تجميد الحساب وتنشيطه فوراً"])
        UC_DeleteGrad(["حذف وسحب سجلات الخريج نهائياً مع حماية التدقيق"])
        UC_SingleNominate(["الترشيح الفردي المباشر لخريج إلى وظيفة"])
        UC_BulkNominate(["الترشيح الجماعي الذكي (أفضل الخريجين بحسب المعدل والتخصص)"])
        UC_ManageJobs(["إضافة وتعديل الفرص الوظيفية وربطها بالشركات الشريكة"])
        UC_ManagePartners(["إدارة سجلات الشركات الشريكة وأرباب العمل"])
    end

    Actor_Career --> UC_ViewGrads
    Actor_Career --> UC_SearchGrads
    Actor_Career --> UC_ImportGrads
    Actor_Career --> UC_ExportGrads
    Actor_Career --> UC_FreezeGrad
    Actor_Career --> UC_UnfreezeGrad
    Actor_Career --> UC_DeleteGrad
    Actor_Career --> UC_SingleNominate
    Actor_Career --> UC_BulkNominate
    Actor_Career --> UC_ManageJobs
    Actor_Career --> UC_ManagePartners

    UC_BulkNominate -.->|يتضمن فحص الأهلية| UC_SearchGrads
    UC_SingleNominate -.->|إشعار تلقائي| Actor_Career
```

---

### 4.3 مخطط حالات استخدام إدارة التدريب والتأهيل
*(منسق التدريب والمدير العام)*

```mermaid
flowchart TB
    Actor_Coord["🎓 منسق التدريب والتأهيل"]

    subgraph Module_Training ["قطاع برامج التدريب والتطوير المهني"]
        UC_CreateTraining(["إنشاء دورة تدريبية جديدة (المقاعد، المدرب، التواريخ)"])
        UC_EditTraining(["تحديث بيانات البرنامج التدريبي وحالة التسجيل"])
        UC_ViewCalendar(["استعراض التقويم الزمني التفاعلي للبرامج التدريبية"])
        UC_ListApps(["استعراض طلبات الالتحاق المعلقة والمقدمة"])
        UC_AcceptSingle(["قبول طلب تدريب فردي وإصدار إشعار القبول"])
        UC_RejectSingle(["رفض طلب تدريب مع كتابة سبب وملاحظات الإدارة"])
        UC_BulkAccept(["قبول جماعي لكافة الطلبات المعلقة لبرنامج معين"])
        UC_BulkAcceptAll(["اعتماد وقبول جميع طلبات التدريب في المنظومة دفعة واحدة"])
        UC_TrainingReport(["توليد تقارير نسب شغل المقاعد ومعدلات الإنجاز"])
    end

    Actor_Coord --> UC_CreateTraining
    Actor_Coord --> UC_EditTraining
    Actor_Coord --> UC_ViewCalendar
    Actor_Coord --> UC_ListApps
    Actor_Coord --> UC_AcceptSingle
    Actor_Coord --> UC_RejectSingle
    Actor_Coord --> UC_BulkAccept
    Actor_Coord --> UC_BulkAcceptAll
    Actor_Coord --> UC_TrainingReport
```

---

### 4.4 مخطط حالات استخدام الخريج والباحث عن عمل
*(الخريجون والطلبة في سنتهم النهائية)*

```mermaid
flowchart TB
    Actor_Grad["👨‍🎓 الخريج (Graduate)"]

    subgraph Module_Grad ["بوابة الخريج والخدمات الذاتية"]
        UC_G_Profile(["إدارة الملف الشخصي والبيانات الأكاديمية"])
        UC_G_CV(["رفع وتحديث ملف السيرة الذاتية (CV PDF/Word)"])
        UC_G_BrowseTrainings(["استعراض البرامج التدريبية المتاحة للتسجيل"])
        UC_G_ApplyTraining(["تقديم طلب التحاق بدورة تدريبية"])
        UC_G_TrackApps(["متابعة حالة طلبات التدريب (قيد المراجعة / مقبول / مرفوض)"])
        UC_G_BrowseJobs(["استعراض فرص العمل والتوظيف الشاغرة"])
        UC_G_ApplyJob(["التقديم الذاتي على فرصة وظيفية معلنة"])
        UC_G_TrackNominations(["متابعة ترشيحات مكتب الإرشاد المهني لدى الشركات"])
        UC_G_AnswerSurveys(["تعبئة استطلاعات الرأي وقياس جودة التدريب والمعارض"])
        UC_G_AskAI(["التحدث مع المساعد الذكي لمراجعة حالته واستخراج النصائح"])
    end

    Actor_Grad --> UC_G_Profile
    Actor_Grad --> UC_G_CV
    Actor_Grad --> UC_G_BrowseTrainings
    Actor_Grad --> UC_G_ApplyTraining
    Actor_Grad --> UC_G_TrackApps
    Actor_Grad --> UC_G_BrowseJobs
    Actor_Grad --> UC_G_ApplyJob
    Actor_Grad --> UC_G_TrackNominations
    Actor_Grad --> UC_G_AnswerSurveys
    Actor_Grad --> UC_G_AskAI
```

---

### 4.5 مخطط حالات استخدام وحدة الإعلام والتغطيات
*(مسؤول وحدة الإعلام والمدير العام)*

```mermaid
flowchart TB
    Actor_Media["📹 مسؤول وحدة الإعلام والتغطيات"]

    subgraph Module_Media ["قطاع الإعلام والتغطيات والاتصال المؤسسي"]
        UC_M_News(["كتابة وتحرير ونشر الأخبار الصحفية مدعمة بالصور"])
        UC_M_Announce(["صياغة ونشر التعميمات والإعلانات في الشريط المتحرك"])
        UC_M_CoverageCalendar(["إدارة جدول المواعيد والتغطيات الإعلامية الميدانية"])
        UC_M_MediaReports(["توليد تقارير نسب تغطية الفعاليات والمؤشرات الإعلامية"])
        UC_M_PublicFair(["إدارة بوابة معرض التوظيف العامة (Blue Theme Fair Portal)"])
        UC_M_PlatformStats(["إدارة الإحصائيات الرسمية المعروضة على المنصة"])
    end

    Actor_Media --> UC_M_News
    Actor_Media --> UC_M_Announce
    Actor_Media --> UC_M_CoverageCalendar
    Actor_Media --> UC_M_MediaReports
    Actor_Media --> UC_M_PublicFair
    Actor_Media --> UC_M_PlatformStats
```

---

### 4.6 مخطط حالات استخدام الجودة والتقييم والمتابعة
*(مسؤول الجودة والتقييم والمدير العام)*

```mermaid
flowchart TB
    Actor_Quality["📝 مسؤول التقييم والمتابعة والجودة"]

    subgraph Module_Quality ["قطاع الجودة والتقييم المؤسسي"]
        UC_Q_CreateSurvey(["إنشاء وتصميم استبيان إلكتروني (أسئلة متعددة ومقاييس ليكرت)"])
        UC_Q_TargetAudience(["تحديد الفئات المستهدفة (خريجون، متدربون، أصحاب عمل)"])
        UC_Q_Distribute(["نشر وتفعيل الاستبيان عبر المنظومة والإشعارات"])
        UC_Q_AnalyzeResponses(["التحليل الإحصائي المباشر لنتائج واستجابات المشاركين"])
        UC_Q_SatisfactionMetrics(["استخراج مؤشر الرضا المؤسسي ونقاط التحسين"])
        UC_Q_QualityReport(["توليد التقرير الختامي لجودة البرامج والفعاليات"])
    end

    Actor_Quality --> UC_Q_CreateSurvey
    Actor_Quality --> UC_Q_TargetAudience
    Actor_Quality --> UC_Q_Distribute
    Actor_Quality --> UC_Q_AnalyzeResponses
    Actor_Quality --> UC_Q_SatisfactionMetrics
    Actor_Quality --> UC_Q_QualityReport
```

---

## 5. المخطط الهيكلي للبيانات (Entity Relationship Diagram - ERD)

يوضح المخطط التالي العلاقات الرئيسية بين جداول قاعدة بيانات المنظومة:

```mermaid
erDiagram
    USERS ||--o| GRADUATE_DATA : "has profile"
    USERS ||--o{ TRAINING_APPLICATIONS : "submits"
    USERS ||--o{ AUDIT_LOGS : "triggers"
    USERS ||--o{ AI_CHAT_MESSAGES : "chats"
    USERS ||--o{ USER_PERMISSIONS : "assigned"
    
    COMPANIES ||--o{ JOB_OPPORTUNITIES : "offers"
    
    JOB_OPPORTUNITIES ||--o{ NOMINATIONS : "receives"
    GRADUATE_DATA ||--o{ NOMINATIONS : "nominated for"
    
    TRAININGS ||--o{ TRAINING_APPLICATIONS : "enrolled in"
    
    SURVEYS ||--o{ SURVEY_QUESTIONS : "contains"
    SURVEYS ||--o{ SURVEY_RESPONSES : "gathers"
    USERS ||--o{ SURVEY_RESPONSES : "fills"
    
    USERS ||--o{ NEWS : "authors"
    USERS ||--o{ ANNOUNCEMENTS : "publishes"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        string phone
        boolean is_active
        timestamp created_at
    }

    GRADUATE_DATA {
        bigint id PK
        bigint user_id FK
        string name
        string email
        string phone
        string national_id
        string faculty
        string major
        decimal gpa
        int graduation_year
        json skills
        json languages
        string employment_status
        string cv_path
        boolean is_active
    }

    COMPANIES {
        bigint id PK
        string name
        string industry
        string contact_person
        string email
        string phone
        string address
        boolean is_active
    }

    JOB_OPPORTUNITIES {
        bigint id PK
        bigint company_id FK
        string title
        text description
        string type
        string contract_type
        string location
        int seats
        date application_deadline
        string status
        decimal salary
        bigint created_by FK
    }

    NOMINATIONS {
        bigint id PK
        bigint job_opportunity_id FK
        bigint graduate_id FK
        string nomination_status
        string final_status
        text notes
        bigint nominated_by FK
        timestamp created_at
    }

    TRAININGS {
        bigint id PK
        string title
        text description
        string trainer_name
        int seats
        date start_date
        date end_date
        string location
        string status
    }

    TRAINING_APPLICATIONS {
        bigint id PK
        bigint training_id FK
        bigint user_id FK
        string status
        text admin_feedback
        timestamp created_at
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string entity
        bigint entity_id
        json old_values
        json new_values
        string ip_address
        timestamp created_at
    }
```

---

## 6. معمارية المساعد الذكي التنفيذي (AI Assistant & Copilot Architecture)

المساعد الذكي في المنظومة ليس مجرد روبوت محادثة (Chatbot) سطحي، بل هو **مساعد تنفيذي متكامل (Autonomous Action Copilot)** مبني وفق نموذج الأمان الصارم **Human-in-the-Loop (HITL)** لضمان الحوكمة المؤسسية.

### 6.1 مخطط مسار تنفيذ الأوامر والقرارات الحساسة (Human-in-the-Loop)

```mermaid
sequenceDiagram
    autonumber
    actor User as مسؤول النظام / الإرشاد المهني
    participant UI as واجهة المساعد الذكي (Copilot UI)
    participant Service as AiAssistantService
    participant Registry as AiToolRegistry
    participant DB as قاعدة البيانات (MySQL)
    participant Audit as سجل العمليات (AuditLog)

    User->>UI: إرسال أمر: "جمد حساب الخريج المنيب"
    UI->>Service: طلب المعالجة chat(user, message)
    Service->>Service: تحليل النية واستخراج المعرف (Intent Recognition & Sanitization)
    Service->>Registry: فحص الصلاحية وتشغيل أداة toggle_graduate_status
    Registry->>DB: الاستعلام عن حساب الخريج والتحقق من حالته الحالية
    Registry-->>Service: إرجاع بطاقة مقترح تفاعلية (Action Proposal Card)
    Service-->>UI: عرض بطاقة التأكيد في الدردشة (العنوان، الحالة المستهدفة، تفاصيل الخريج)
    Note over User,UI: لا يتم تطبيق أي تغيير في قاعدة البيانات حتى هذه اللحظة!

    User->>UI: الضغط على زر [تأكيد وحفظ]
    UI->>Service: إرسال تأكيد الإجراء confirmAction(sessionId, actionType, data)
    Service->>Service: التحقق الأمني المزدوج من هوية وصلاحية المنفذ
    Service->>DB: تنفيذ التحديث الفعلي (is_active = false) داخل DB Transaction
    Service->>Audit: تسجيل العملية بالكامل في AuditLog (IP, Time, Target, Old/New Values)
    Service-->>UI: إرجاع رسالة النجاح والتوثيق الرسمية
    UI-->>User: إظهار إشعار النجاح وتحديث شارة الخريج إلى مجمد 🔒
```

---

### 6.2 جدول أدوات المساعد الذكي المسجلة

تم توزيع وتنظيم أدوات الذكاء الاصطناعي في [`app/Services/Ai/AiToolRegistry.php`](file:///c:/Users/Sohib/graduate_training_system/app/Services/Ai/AiToolRegistry.php) إلى **8 قطاعات متخصصة ومستقلة**:

| القطاع الوظيفي | الدالة المعمارية | الأدوات المتاحة | الصلاحية المطلوبة | طبيعة التنفيذ |
| :--- | :--- | :--- | :--- | :--- |
| **1. أدوات عامة** | `defineGeneralTools` | `get_my_info`, `search_site_content`, `get_active_announcements` | كافة المستخدمين | استعلام مباشر |
| **2. بوابة الخريج** | `defineGraduateTools` | `get_my_applications`, `get_available_trainings`, `search_job_opportunities`, `apply_for_training` | الخريج والمدير | استعلام / مقترح تقديم |
| **3. وحدة الإعلام** | `defineMediaOfficerTools` | `get_media_statistics`, `post_news_draft` | مسؤول الإعلام والمدير | استعلام / مسودة خبر |
| **4. منسق التدريب** | `defineTrainingTools` | `get_training_coordinator_stats`, `propose_training_program`, `list_pending_training_applicants` | منسق التدريب والمدير | استعلام / مقترح اعتماد |
| **5. الإرشاد المهني** | `defineCareerGuidanceTools` | `search_graduates_advanced`, `nominate_graduate_for_job`, `publish_job_vacancy` | مسؤول الإرشاد والمدير | استعلام / مقترح ترشيح |
| **6. الجودة والتقييم** | `defineQualityAndSurveyTools` | `draft_survey`, `get_survey_analytics`, `generate_training_report` | مسؤول الجودة والمدير | استعلام / مقترح استبيان |
| **7. الإعلام والرقابة** | `defineMediaTools` | `draft_announcement`, `generate_media_report`, `get_platform_system_overview` | مسؤول الإعلام والمدير | استعلام / مسودة إعلان |
| **8. الصلاحيات التنفيذية** | `defineExecutiveTools` | `toggle_graduate_status`, `delete_graduate_account`, `bulk_nominate_graduates`, `manage_training_applications`, `change_user_password` | الإرشاد المهني والمدير | **بطاقات مقترحات تفاعلية مشددة (HITL)** |

---

## 7. القطاعات والإدارات الوظيفية بالتفصيل

### 1️⃣ إدارة الإرشاد المهني والتوجيه والتشغيل
- **جدول الخريجين فائق الضغط (Ultra-Compact Table)**:
  - صُمم بارتفاع ثابت `50px` لكل صف مانعاً تكسر الأزرار مع ملاءمة استيعاب أكثر من 3,000 سجل في الصفحة الواحدة بسلاسة تامة.
  - إيقاف كامل لأي اهتزاز بصري عند تحريك الماوس (`Zero Hover Jitter`) عبر ظلال داخلية ثابتة `box-shadow: inset 0 0 0 1px #e2e8f0`.
- **القرارات التنفيذية السريعة**:
  - تجميد الحساب فوري بزر القفل السريع أو عبر أمر *"جمد حساب الخريج [الاسم]"*.
  - إلغاء التجميد والتنشيط فوري بزر القفل أو عبر أمر *"فك تجميد حساب [الاسم]"* أو *"إلغاء التجميد عن [الاسم]"*.
  - حذف الحساب نهائياً مع حماية النوافذ التحذيرية ومسح كافة الارتباطات (ترشيحات، طلبات، استجابات) داخل معاملة آمنة `DB::transaction`.
- **نظام الترشيح المزدوج**:
  - ترشيح فردي مباشر من صفحة الخريج أو عبر الدردشة.
  - ترشيح جماعي ذكي يفرز أعلى الخريجين معدلاً تراكمياً المتوافقين مع شروط الوظيفة.

### 2️⃣ إدارة التدريب والتأهيل والتطوير المستمر
- **إدارة الدورات ومقاعد التدريب**: متابعة عدد المقاعد المشغولة والمتبقية آلياً.
- **إدارة وفرز الطلبات**: قبول أو رفض الطلبات بنقرة واحدة فردياً أو جماعياً لكافة الطلبات المعلقة عبر أمر *"اقبل جميع طلبات التدريب"*.
- **التقويم الزمني**: تقويم شهري تفاعلي يوضح جداول الدورات الحالية والمستقبلية ومواعيد انتهاء التسجيل.

### 3️⃣ إدارة الجودة والتقييم والمتابعة
- **صانع الاستبيانات الرقمي**: صياغة استطلاعات تقييم البرامج والفعاليات.
- **مؤشرات الأداء اللحظية**: حساب نسب الرضا التلقائي وتصنيف الملاحظات.

### 4️⃣ وحدة الإعلام والتغطيات الصحفية والاتصال
- **تقويم وجدول التغطيات الصحفية**: متابعة وجدولة التغطيات الميدانية للتدريبات والورش.
- **المركز الصحفي وشريط الأخبار**: نشر الأخبار والتعميمات اللحظية المنبثقة للزوار والطلاب.
- **البوابة العامة لمعرض التوظيف (Blue Fair Portal)**: واجهة زرقاء عصرية تعرض فعاليات المعرض والفرص المتاحة لعموم المواطنين والطلاب.
- **إحصائيات المنصة الرئيسية**: شاشة تحكم رسمية لضبط مؤشرات وأرقام الصفحة الرئيسية.

---

## 8. الأمان، التدقيق، ومراقبة العمليات (Security & Audit Logging)

تطبق المنظومة أحدث معايير الأمان المؤسسي:
1. **التحقق الثنائي للتفويض (Dual-Layer Authorization)**:
   - فحص الصلاحية على مستوى مسارات التطبيق ومتحكمات الـ Middleware.
   - فحص مستقل إضافي داخل محرك الأدوات الذكية `AiToolRegistry::executeTool` قبل السماح بأي إجراء.
2. **سجل العمليات والرقابة المشددة (`AuditLog`)**:
   - توثيق كل عملية تجميد، تنشيط، حذف، ترشيح، أو تغيير كلمة مرور.
   - حفظ: هوية المنفذ (`user_id`)، عنوان الإنترنت (`IP Address`)، نوع المتصفح والجهاز (`User Agent`)، نوع الإجراء، المعرف المستهدف، والبيانات القديمة والجديدة بتنسيق JSON.
3. **حماية التعديل الجماعي والحذف (Data Integrity)**:
   - تنفيذ كافة عمليات الحذف والترشيح الجماعي وتغيير الحالات ضمن **معاملات ذرية مشفرة (`Database Transactions`)** لضمان عدم تلف البيانات في حال حدوث أي انقطاع.

---
**تم إعداد هذا التوثيق ليكون المرجع الفني والهيكلي الكامل لمسؤولي ومطوري منظومة تدريب وتشغيل الخريجين بجامعة طرابلس.**
