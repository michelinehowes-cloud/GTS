// بيانات كليات وأقسام جامعة طرابلس
const universitiesData = {
    'جامعة طرابلس': {
        'قاطع (أ)': {
            'كلية العلوم': [
                'قسم الرياضيات',
                'قسم علم الحيوان',
                'قسم الفيزياء',
                'قسم الكيمياء',
                'قسم علم النبات',
                'قسم الجيولوجيا',
                'قسم الحاسب الآلي',
                'قسم الإحصاء',
                'قسم علم الغلاف الجوي',
                'قسم الجيوفيزياء',
                'قسم المرحلة العامة'
            ],
            'كلية الهندسة': [
                'قسم الهندسة الكهربائية والإلكترونية',
                'قسم الهندسة الميكانيكية والصناعية',
                'قسم هندسة العمارة والتخطيط العمراني',
                'قسم الهندسة البحرية والمنصات العائمة',
                'قسم هندسة النفط',
                'قسم الهندسة الجيولوجية',
                'قسم هندسة التعدين',
                'قسم الهندسة النووية',
                'قسم هندسة الحاسب الآلي',
                'قسم هندسة المواد والمعادن',
                'قسم هندسة الطيران',
                'قسم الهندسة المدنية',
                'قسم الهندسة الطبية',
                'قسم الإدارة الهندسية',
                'قسم الهندسة الكيميائية'
            ],
            'كلية الزراعة': [
                'قسم الاقتصاد الزراعي',
                'قسم الزراعات المائية',
                'قسم المراعي والغابات',
                'قسم الاقتصاد المنزلي',
                'قسم المحاصيل الزراعية',
                'قسم الهندسة الزراعية',
                'قسم البستنة',
                'قسم وقاية النبات',
                'قسم التربة والمياه',
                'قسم الإنتاج الحيواني',
                'قسم علوم وتقنية الأغذية'
            ],
            'كلية تقنية المعلومات': [
                'قسم هندسة البرمجيات',
                'قسم الشبكات',
                'قسم نظم المعلومات',
                'قسم الحوسبة المتنقلة',
                'قسم تقنيات الإنترنت',
                'قسم المرحلة العامة',
                'قسم علم البيانات والذكاء الاصطناعي',
                'قسم الأمن السيبراني'
            ],
            'كلية القانون': [
                'قسم الشريعة الإسلامية',
                'قسم القانون الجنائي',
                'قسم القانون العام',
                'قسم القانون الخاص',
                'قسم القانون الدولي'
            ],
            'كلية الإعلام والاتصال': [
                'قسم الإذاعة والتلفزيون',
                'قسم العلاقات العامة والإعلان',
                'قسم الصحافة'
            ],
            'كلية الفنون': [
                'قسم الفنون المرئية',
                'قسم الفنون الدرامية',
                'قسم الفنون الموسيقية',
                'قسم الفنون الجميلة والتطبيقية',
                'قسم التصميم الداخلي'
            ],
            'كلية التقنية الطبية': [
                'قسم تقنية الأسنان',
                'قسم العلاج الطبيعي',
                'قسم التخدير والعناية الفائقة',
                'قسم الصحة العامة',
                'قسم علوم المختبرات الطبية',
                'قسم الأشعة التشخيصية والعلاجية'
            ],
            'كلية التمريض': [
                'قسم أساسيات التمريض',
                'قسم العمليات الجراحية',
                'قسم القبالة وحديثي الولادة',
                'قسم تخدير وعناية فائقة',
                'قسم التمريض العام'
            ],
            'كلية الصيدلة': [
                'قسم علم الأدوية والصيدلة السريرية',
                'قسم الصيدلة الصناعية',
                'قسم الكيمياء الطبية والصيدلة',
                'قسم الأحياء الدقيقة والمناعة',
                'قسم الكيمياء الحيوية السريرية',
                'قسم الصيدلانيات',
                'قسم العقاقير والنواتج الطبيعية'
            ],
            'كلية الطب البيطري': [
                'قسم الطب الوقائي',
                'قسم التشريح والنسيجة والأجنة',
                'قسم الأمراض والتشخيص المعملي',
                'قسم الرقابة الصحية على الأغذية',
                'قسم وظائف الأعضاء والكيمياء الحيوية',
                'قسم أمراض الدواجن والأسماك',
                'قسم الباطنة',
                'قسم الجراحة والتناسليات',
                'قسم الأدوية والسموم والطب الشرعي',
                'قسم الأحياء الدقيقة والطفيليات'
            ],
            'كلية الطب البشري': [
                'قسم الأحياء الدقيقة والمناعة الطبية',
                'قسم طب الأطفال',
                'قسم الأنسجة والوراثة',
                'قسم الباطنة',
                'قسم التشريح والأجنة',
                'قسم الطفيليات',
                'قسم علم الأدوية',
                'قسم علم الأمراض',
                'قسم الكيمياء الحيوية',
                'قسم وظائف الأعضاء',
                'قسم أمراض النساء والولادة',
                'قسم الجراحة',
                'قسم الطب الشرعي والسموم',
                'قسم طب الأسرة والمجتمع',
                'قسم طب وجراحة العيون',
                'قسم المهارات السريرية'
            ]
        },
        'قاطع (ب)': {
            'كلية الاقتصاد والعلوم السياسية': [
                'قسم الاقتصاد',
                'قسم المحاسبة',
                'قسم العلوم السياسية',
                'قسم التمويل والمصارف',
                'قسم التخطيط المالي',
                'قسم الإحصاء والاقتصاد القياسي',
                'قسم التجارة الإلكترونية وتحليل البيانات',
                'قسم إدارة الأعمال',
                'قسم المرحلة العامة'
            ],
            'كلية التربية (طرابلس)': [
                'قسم الأحياء',
                'قسم التربية الخاصة',
                'قسم الحاسب الآلي',
                'قسم الرياضيات',
                'قسم الفيزياء',
                'قسم الكيمياء',
                'قسم اللغة الإنجليزية',
                'قسم اللغة العربية والدراسات الإسلامية',
                'قسم رياض الأطفال',
                'قسم معلم فصل',
                'قسم التربية الفنية',
                'قسم العلوم التربوية والنفسية'
            ],
            'كلية الآداب واللغات': [
                'قسم الجغرافيا ونظم المعلومات الجغرافية',
                'قسم الفلسفة',
                'قسم اللغة الفرنسية',
                'قسم اللغة العربية',
                'قسم التاريخ',
                'قسم المكتبات والمعلومات',
                'قسم اللغة الإنجليزية',
                'قسم الدراسات الإسلامية',
                'قسم التربية وعلم النفس',
                'قسم الخدمة الاجتماعية',
                'قسم علم الاجتماع',
                'قسم الإرشاد والعلاج النفسي',
                'قسم اللغة الإسبانية',
                'قسم اللغات الأفروآسيوية',
                'قسم الترجمة (اللغة الإنجليزية)',
                'قسم اللغة الإيطالية',
                'قسم علم النفس'
            ],
            'كلية التربية البدنية وعلوم الرياضة': [
                'قسم التدريب',
                'قسم إعادة التأهيل والعلاج الطبيعي',
                'قسم التربية البدنية (التدريس)'
            ]
        },
        'قاطع (ج)': {
            'كلية العلوم الشرعية (تاجوراء)': [
                'قسم الشريعة والقانون',
                'قسم أصول الدين',
                'قسم الاقتصاد الإسلامي',
                'قسم اللغة العربية',
                'قسم الشريعة'
            ],
            'كلية الاقتصاد والإدارة (تاجوراء)': [
                'قسم إدارة الأعمال',
                'قسم المحاسبة',
                'قسم التمويل والمصارف'
            ]
        },
        'كليات أخرى': {
            'كلية طب وجراحة الفم والأسنان': [
                'قسم التقويم والأطفال والطب الوقائي',
                'قسم طب وجراحة الفم والفكين',
                'قسم الاستعاضة الصناعية',
                'قسم العلاج التحفظي وعلاج جذور الأسنان',
                'قسم علاج وجراحة اللثة'
            ],
            'كلية التربية (جنزور)': [
                'قسم الفيزياء',
                'قسم الرياضيات',
                'قسم الكيمياء',
                'قسم اللغة الإنجليزية',
                'قسم اللغة العربية',
                'قسم رياض الأطفال',
                'قسم معلم فصل',
                'قسم علم الاجتماع',
                'قسم الخدمة الاجتماعية',
                'قسم الأحياء'
            ],
            'كلية العلوم الشرعية (سوق الجمعة)': [
                'قسم أصول الدين',
                'قسم الشريعة'
            ],
            'كلية التربية (قصر بن غشير)': [
                'قسم الجغرافيا',
                'قسم الحاسوب',
                'قسم الكيمياء',
                'قسم اللغة العربية',
                'قسم اللغة الإنجليزية',
                'قسم معلم فصل',
                'قسم الفيزياء',
                'قسم رياض الأطفال',
                'قسم الأحياء',
                'قسم الخدمة الاجتماعية',
                'قسم الرياضيات',
                'قسم الدراسات الإسلامية'
            ]
        }
    }
};

// دالة لتحميل القطاعات
function loadSectors() {
    const universitySelect = document.getElementById('university');
    const sectorSelect = document.getElementById('sector');
    const facultySelect = document.getElementById('faculty');
    const specializationSelect = document.getElementById('specialization');

    if (!universitySelect || !sectorSelect) return;

    const selectedUniversity = universitySelect.value;

    // مسح القوائم
    sectorSelect.innerHTML = '<option value="">اختر القطاع</option>';
    facultySelect.innerHTML = '<option value="">اختر الكلية</option>';
    specializationSelect.innerHTML = '<option value="">اختر التخصص</option>';

    facultySelect.disabled = true;
    specializationSelect.disabled = true;

    if (selectedUniversity && universitiesData[selectedUniversity]) {
        const sectors = Object.keys(universitiesData[selectedUniversity]);
        sectors.forEach(sector => {
            const option = document.createElement('option');
            option.value = sector;
            option.textContent = sector;
            sectorSelect.appendChild(option);
        });
        sectorSelect.disabled = false;
    } else {
        sectorSelect.disabled = true;
    }
}

// دالة لتحميل الكليات
function loadFaculties() {
    const universitySelect = document.getElementById('university');
    const sectorSelect = document.getElementById('sector');
    const facultySelect = document.getElementById('faculty');
    const specializationSelect = document.getElementById('specialization');

    if (!universitySelect || !sectorSelect || !facultySelect) return;

    const selectedUniversity = universitySelect.value;
    const selectedSector = sectorSelect.value;

    // مسح القوائم
    facultySelect.innerHTML = '<option value="">اختر الكلية</option>';
    specializationSelect.innerHTML = '<option value="">اختر التخصص</option>';
    specializationSelect.disabled = true;

    if (selectedUniversity && selectedSector &&
        universitiesData[selectedUniversity] &&
        universitiesData[selectedUniversity][selectedSector]) {

        const faculties = Object.keys(universitiesData[selectedUniversity][selectedSector]);
        faculties.forEach(faculty => {
            const option = document.createElement('option');
            option.value = faculty;
            option.textContent = faculty;
            facultySelect.appendChild(option);
        });
        facultySelect.disabled = false;
    } else {
        facultySelect.disabled = true;
    }
}

// دالة لتحميل التخصصات
function loadSpecializations() {
    const universitySelect = document.getElementById('university');
    const sectorSelect = document.getElementById('sector');
    const facultySelect = document.getElementById('faculty');
    const specializationSelect = document.getElementById('specialization');

    if (!universitySelect || !sectorSelect || !facultySelect || !specializationSelect) return;

    const selectedUniversity = universitySelect.value;
    const selectedSector = sectorSelect.value;
    const selectedFaculty = facultySelect.value;

    // مسح القائمة
    specializationSelect.innerHTML = '<option value="">اختر التخصص</option>';

    if (selectedUniversity && selectedSector && selectedFaculty &&
        universitiesData[selectedUniversity] &&
        universitiesData[selectedUniversity][selectedSector] &&
        universitiesData[selectedUniversity][selectedSector][selectedFaculty]) {

        const specializations = universitiesData[selectedUniversity][selectedSector][selectedFaculty];
        specializations.forEach(specialization => {
            const option = document.createElement('option');
            option.value = specialization;
            option.textContent = specialization;
            specializationSelect.appendChild(option);
        });
        specializationSelect.disabled = false;
    } else {
        specializationSelect.disabled = true;
    }
}

// تهيئة القوائم عند تحميل الصفحة
function initUniversityDropdowns() {
    const universitySelect = document.getElementById('university');
    const sectorSelect = document.getElementById('sector');
    const facultySelect = document.getElementById('faculty');
    const specializationSelect = document.getElementById('specialization');

    if (!universitySelect) return;

    universitySelect.removeEventListener('change', loadSectors);
    universitySelect.addEventListener('change', loadSectors);

    if (sectorSelect) {
        sectorSelect.removeEventListener('change', loadFaculties);
        sectorSelect.addEventListener('change', loadFaculties);
    }

    if (facultySelect) {
        facultySelect.removeEventListener('change', loadSpecializations);
        facultySelect.addEventListener('change', loadSpecializations);
    }

    // قراءة القيم المحفوظة مسبقاً (old values أو بيانات الخريج)
    const oldUnivInput = document.getElementById('old_university');
    const oldSectorInput = document.getElementById('old_sector');
    const oldFacultyInput = document.getElementById('old_faculty');
    const oldSpecInput = document.getElementById('old_specialization');

    const savedUniversity = (universitySelect && universitySelect.value) ? universitySelect.value : (oldUnivInput ? oldUnivInput.value : 'جامعة طرابلس');
    const savedSector = oldSectorInput ? oldSectorInput.value : (sectorSelect ? sectorSelect.value : '');
    const savedFaculty = oldFacultyInput ? oldFacultyInput.value : (facultySelect ? facultySelect.value : '');
    const savedSpecialization = oldSpecInput ? oldSpecInput.value : (specializationSelect ? specializationSelect.value : '');

    if (universitySelect && savedUniversity) {
        universitySelect.value = savedUniversity;
        loadSectors();

        if (sectorSelect && savedSector) {
            sectorSelect.value = savedSector;
            loadFaculties();

            if (facultySelect && savedFaculty) {
                facultySelect.value = savedFaculty;
                loadSpecializations();

                if (specializationSelect && savedSpecialization) {
                    specializationSelect.value = savedSpecialization;
                }
            }
        }
    }

    // التأكد من تفعيل حقل التخصص قبل إرسال النموذج حتى يتم إرسال قيمته إلى السيرفر
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function () {
            if (specializationSelect && specializationSelect.value) {
                specializationSelect.disabled = false;
            }
            if (sectorSelect && sectorSelect.value) {
                sectorSelect.disabled = false;
            }
            if (facultySelect && facultySelect.value) {
                facultySelect.disabled = false;
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUniversityDropdowns);
} else {
    initUniversityDropdowns();
}

