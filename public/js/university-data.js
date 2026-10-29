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
                'طبيب عام',
                'متخصص',
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
                'قسم اللغة الروسية',
                'قسم الدراسات السياحية',
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

    const isMedicine = selectedFaculty && (selectedFaculty === 'كلية الطب البشري' || selectedFaculty.includes('الطب البشري'));

    if (isMedicine) {
        specializationSelect.disabled = false;

        // الخياران الأساسيان لخريجي كلية الطب البشري
        const optGeneral = document.createElement('option');
        optGeneral.value = 'طبيب عام';
        optGeneral.textContent = 'طبيب عام';
        specializationSelect.appendChild(optGeneral);

        const optSpecialist = document.createElement('option');
        optSpecialist.value = 'متخصص';
        optSpecialist.textContent = 'متخصص';
        specializationSelect.appendChild(optSpecialist);

        // قائمة التخصصات السريرية المعتمدة
        const optGroup = document.createElement('optgroup');
        optGroup.label = 'التخصصات السريرية (لمن أتم التخصص)';

        const rawSpecs = (universitiesData[selectedUniversity] && universitiesData[selectedUniversity][selectedSector] && universitiesData[selectedUniversity][selectedSector][selectedFaculty]) 
            ? universitiesData[selectedUniversity][selectedSector][selectedFaculty] 
            : [];

        rawSpecs.forEach(spec => {
            if (spec !== 'طبيب عام' && spec !== 'متخصص') {
                const opt = document.createElement('option');
                opt.value = spec;
                opt.textContent = spec;
                optGroup.appendChild(opt);
            }
        });
        specializationSelect.appendChild(optGroup);
    } else if (selectedUniversity && selectedSector && selectedFaculty &&
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

    // مزامنة مسار كلية الطب البشري (طبيب عام / متخصص)
    handleMedicalTrack();
}

// دالة إدارة المسار الطبي لكلية الطب البشري (طبيب عام / متخصص)
function handleMedicalTrack(isQualChange = false) {
    const facultySelect = document.getElementById('faculty');
    const qualificationSelect = document.getElementById('qualification');
    const specializationSelect = document.getElementById('specialization');
    const hiddenMajor = document.getElementById('hidden_major');
    const trackContainer = document.getElementById('medical_track_container') || document.querySelector('.medical-track-container');
    const trackGeneral = document.getElementById('medical_track_general');
    const trackSpecialist = document.getElementById('medical_track_specialist');
    const generalNotice = document.getElementById('medical_general_notice');
    const specializationWrapper = document.getElementById('specialization_wrapper') || document.querySelector('.specialization-wrapper');
    const specializationLabel = document.getElementById('specialization_label') || document.querySelector('.specialization-label');
    const oldSpecInput = document.getElementById('old_specialization');

    if (!facultySelect || !specializationSelect) return;

    const isMedicine = facultySelect.value && (facultySelect.value === 'كلية الطب البشري' || facultySelect.value.includes('الطب البشري'));

    if (!isMedicine) {
        if (trackContainer) trackContainer.style.display = 'none';
        if (specializationWrapper) specializationWrapper.style.display = '';
        specializationSelect.required = true;
        if (specializationLabel) {
            specializationLabel.innerHTML = 'التخصص <span class="text-danger">*</span>';
        }
        return;
    }

    // إظهار بطاقة المسار الطبي وحقل التخصص معاً
    if (trackContainer) {
        trackContainer.style.display = 'block';
    }
    if (specializationWrapper) {
        specializationWrapper.style.display = 'block';
    }
    specializationSelect.disabled = false;
    specializationSelect.required = true;

    if (specializationLabel) {
        specializationLabel.innerHTML = 'التخصص (طبيب عام / متخصص) <span class="text-danger">*</span>';
    }

    const currentQual = qualificationSelect ? qualificationSelect.value : 'بكالوريوس';
    const isPostgrad = ['ماجستير', 'دكتوراه', 'دبلوم عالي', 'زمالة', 'بورد'].includes(currentQual);
    const savedSpec = oldSpecInput ? oldSpecInput.value : specializationSelect.value;

    // تعيين القيمة المناسبة إذا لم يتم تحديد قيمة بعد
    if (!specializationSelect.value || specializationSelect.value === '') {
        if (savedSpec && savedSpec !== '') {
            specializationSelect.value = savedSpec;
        } else if (isPostgrad) {
            specializationSelect.value = 'متخصص';
        } else {
            specializationSelect.value = 'طبيب عام';
        }
    } else if (isQualChange) {
        if (isPostgrad && specializationSelect.value === 'طبيب عام') {
            specializationSelect.value = 'متخصص';
        }
    }

    // مزامنة أزرار الراديو والإشعار
    const currentVal = specializationSelect.value;
    if (currentVal === 'طبيب عام') {
        if (trackGeneral) trackGeneral.checked = true;
        if (generalNotice) generalNotice.style.display = 'flex';
    } else if (currentVal && currentVal !== '') {
        if (trackSpecialist) trackSpecialist.checked = true;
        if (generalNotice) generalNotice.style.display = 'none';
    }

    if (hiddenMajor && specializationSelect.value) {
        hiddenMajor.value = specializationSelect.value;
    }
}

// تهيئة القوائم عند تحميل الصفحة
function initUniversityDropdowns() {
    const universitySelect = document.getElementById('university');
    const sectorSelect = document.getElementById('sector');
    const facultySelect = document.getElementById('faculty');
    const specializationSelect = document.getElementById('specialization');
    const qualificationSelect = document.getElementById('qualification');
    const trackGeneral = document.getElementById('medical_track_general');
    const trackSpecialist = document.getElementById('medical_track_specialist');
    const generalNotice = document.getElementById('medical_general_notice');
    const hiddenMajor = document.getElementById('hidden_major');

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

    if (specializationSelect) {
        specializationSelect.removeEventListener('change', onSpecChanged);
        specializationSelect.addEventListener('change', onSpecChanged);
    }

    function onSpecChanged() {
        const isMed = facultySelect && (facultySelect.value === 'كلية الطب البشري' || facultySelect.value.includes('الطب البشري'));
        if (isMed) {
            if (this.value === 'طبيب عام') {
                if (trackGeneral) trackGeneral.checked = true;
                if (generalNotice) generalNotice.style.display = 'flex';
            } else if (this.value && this.value !== '') {
                if (trackSpecialist) trackSpecialist.checked = true;
                if (generalNotice) generalNotice.style.display = 'none';
            }
        }
        if (hiddenMajor && this.value) {
            hiddenMajor.value = this.value;
        }
    }

    if (qualificationSelect) {
        qualificationSelect.removeEventListener('change', onQualificationChanged);
        qualificationSelect.addEventListener('change', onQualificationChanged);
    }

    function onQualificationChanged() {
        const isMed = facultySelect && (facultySelect.value === 'كلية الطب البشري' || facultySelect.value.includes('الطب البشري'));
        if (isMed) {
            handleMedicalTrack(true);
        }
    }

    if (trackGeneral) {
        trackGeneral.removeEventListener('change', onGeneralRadioSelected);
        trackGeneral.addEventListener('change', onGeneralRadioSelected);
    }

    function onGeneralRadioSelected() {
        if (this.checked && specializationSelect) {
            specializationSelect.value = 'طبيب عام';
            if (hiddenMajor) hiddenMajor.value = 'طبيب عام';
            if (generalNotice) generalNotice.style.display = 'flex';
        }
    }

    if (trackSpecialist) {
        trackSpecialist.removeEventListener('change', onSpecialistRadioSelected);
        trackSpecialist.addEventListener('change', onSpecialistRadioSelected);
    }

    function onSpecialistRadioSelected() {
        if (this.checked && specializationSelect) {
            if (specializationSelect.value === 'طبيب عام' || !specializationSelect.value) {
                specializationSelect.value = 'متخصص';
            }
            if (hiddenMajor) hiddenMajor.value = specializationSelect.value;
            if (generalNotice) generalNotice.style.display = 'none';
            specializationSelect.focus();
        }
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

                const isMed = (savedFaculty === 'كلية الطب البشري' || savedFaculty.includes('الطب البشري'));
                if (isMed) {
                    if (savedSpecialization && savedSpecialization !== '') {
                        specializationSelect.value = savedSpecialization;
                    } else if (qualificationSelect && qualificationSelect.value === 'بكالوريوس') {
                        specializationSelect.value = 'طبيب عام';
                    }
                    handleMedicalTrack();
                } else {
                    if (specializationSelect && savedSpecialization) {
                        specializationSelect.value = savedSpecialization;
                    }
                }
            }
        }
    }

    // التأكد من تفعيل حقل التخصص قبل إرسال النموذج حتى يتم إرسال قيمته إلى السيرفر
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const currentFaculty = document.getElementById('faculty');
            const currentSpecSelect = document.getElementById('specialization');
            const currentHiddenMajor = document.getElementById('hidden_major');

            if (currentSpecSelect && currentSpecSelect.value) {
                currentSpecSelect.disabled = false;
                if (currentHiddenMajor) currentHiddenMajor.value = currentSpecSelect.value;
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

