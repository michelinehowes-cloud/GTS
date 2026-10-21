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

    // مزامنة مسار كلية الطب البشري (طبيب عام / طبيب متخصص)
    handleMedicalTrack();
}

// دالة إدارة المسار الطبي لكلية الطب البشري (طبيب عام / طبيب متخصص)
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

    const isMedicine = (facultySelect.value === 'كلية الطب البشري');

    if (!isMedicine) {
        if (trackContainer) trackContainer.style.display = 'none';
        if (specializationWrapper) specializationWrapper.style.display = '';
        specializationSelect.required = true;
        if (specializationLabel) {
            specializationLabel.innerHTML = 'التخصص <span class="text-danger">*</span>';
        }
        return;
    }

    // الكلية المختارة هي كلية الطب البشري
    if (trackContainer) {
        trackContainer.style.display = 'block';
    }

    const currentQual = qualificationSelect ? qualificationSelect.value : 'بكالوريوس';
    const isPostgrad = ['ماجستير', 'دكتوراه', 'دبلوم عالي', 'زمالة', 'بورد'].includes(currentQual);
    const savedSpec = oldSpecInput ? oldSpecInput.value : specializationSelect.value;

    // تحديد الخيار الأنسب إذا لم يكن محددًا مسبقاً
    if (trackGeneral && trackSpecialist) {
        if (!trackGeneral.checked && !trackSpecialist.checked) {
            if (savedSpec && savedSpec !== 'طبيب عام' && savedSpec !== '') {
                // الخريج لديه تخصص سريري محفوظ مسبقاً
                trackSpecialist.checked = true;
            } else if (isPostgrad) {
                // خريج دراسات عليا في الطب يميل للتخصص الدقيق
                trackSpecialist.checked = true;
            } else {
                // خريج بكالوريوس يبدأ طبيعياً كطبيب عام
                trackGeneral.checked = true;
            }
        } else if (isQualChange) {
            if (isPostgrad && trackGeneral.checked) {
                trackSpecialist.checked = true;
            } else if (currentQual === 'بكالوريوس' && (!specializationSelect.value || specializationSelect.value === 'طبيب عام')) {
                trackGeneral.checked = true;
            }
        }

        // تطبيق حالة الواجهة بناءً على الخيار
        if (trackGeneral.checked) {
            if (generalNotice) generalNotice.style.display = 'flex';
            if (specializationWrapper) specializationWrapper.style.display = 'none';
            specializationSelect.required = false;

            // التأكد من وجود قيمة طبيب عام وتعيينها
            let hasGeneralOption = false;
            for (let i = 0; i < specializationSelect.options.length; i++) {
                if (specializationSelect.options[i].value === 'طبيب عام') {
                    hasGeneralOption = true;
                    break;
                }
            }
            if (!hasGeneralOption) {
                const opt = document.createElement('option');
                opt.value = 'طبيب عام';
                opt.textContent = 'طبيب عام';
                specializationSelect.appendChild(opt);
            }
            specializationSelect.value = 'طبيب عام';
            specializationSelect.disabled = false;
            if (hiddenMajor) hiddenMajor.value = 'طبيب عام';
        } else if (trackSpecialist.checked) {
            if (generalNotice) generalNotice.style.display = 'none';
            if (specializationWrapper) specializationWrapper.style.display = '';
            specializationSelect.required = true;
            specializationSelect.disabled = false;
            if (specializationLabel) {
                specializationLabel.innerHTML = 'التخصص السريري / الدقيق <span class="text-danger">*</span>';
            }

            // استدعاء تعبئة التخصصات السريرية (باستثناء طبيب عام)
            populateMedicalSpecialties(savedSpec);
        }
    }
}

// دالة لتعبئة التخصصات السريرية لكلية الطب البشري (باستثناء "طبيب عام")
function populateMedicalSpecialties(selectedVal) {
    const specializationSelect = document.getElementById('specialization');
    const universitySelect = document.getElementById('university');
    const sectorSelect = document.getElementById('sector');
    const facultySelect = document.getElementById('faculty');
    const hiddenMajor = document.getElementById('hidden_major');

    if (!specializationSelect) return;

    const u = universitySelect ? universitySelect.value : 'جامعة طرابلس';
    const s = sectorSelect ? sectorSelect.value : 'قاطع (أ)';
    const f = facultySelect ? facultySelect.value : 'كلية الطب البشري';

    const rawSpecs = (universitiesData[u] && universitiesData[u][s] && universitiesData[u][s][f]) ? universitiesData[u][s][f] : [];
    const clinicalSpecs = rawSpecs.filter(spec => spec !== 'طبيب عام');

    specializationSelect.innerHTML = '<option value="">اختر التخصص السريري / الدقيق</option>';
    clinicalSpecs.forEach(spec => {
        const opt = document.createElement('option');
        opt.value = spec;
        opt.textContent = spec;
        if (selectedVal && selectedVal === spec) {
            opt.selected = true;
        }
        specializationSelect.appendChild(opt);
    });

    if (selectedVal && selectedVal !== 'طبيب عام') {
        specializationSelect.value = selectedVal;
        if (hiddenMajor) hiddenMajor.value = selectedVal;
    } else if (specializationSelect.value === 'طبيب عام') {
        specializationSelect.value = '';
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

    if (qualificationSelect) {
        qualificationSelect.removeEventListener('change', onQualificationChanged);
        qualificationSelect.addEventListener('change', onQualificationChanged);
    }

    function onQualificationChanged() {
        if (facultySelect && facultySelect.value === 'كلية الطب البشري') {
            handleMedicalTrack(true);
        }
    }

    if (trackGeneral) {
        trackGeneral.removeEventListener('change', onTrackRadioChanged);
        trackGeneral.addEventListener('change', onTrackRadioChanged);
    }

    if (trackSpecialist) {
        trackSpecialist.removeEventListener('change', onTrackRadioChanged);
        trackSpecialist.addEventListener('change', onTrackRadioChanged);
    }

    function onTrackRadioChanged() {
        handleMedicalTrack();
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

                if (savedFaculty === 'كلية الطب البشري') {
                    if (savedSpecialization === 'طبيب عام' || (!savedSpecialization && (!qualificationSelect || qualificationSelect.value === 'بكالوريوس'))) {
                        if (trackGeneral) trackGeneral.checked = true;
                    } else if (savedSpecialization && savedSpecialization !== 'طبيب عام') {
                        if (trackSpecialist) trackSpecialist.checked = true;
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
            const currentGeneralRadio = document.getElementById('medical_track_general');
            const currentSpecSelect = document.getElementById('specialization');
            const hiddenMajor = document.getElementById('hidden_major');

            if (currentFaculty && currentFaculty.value === 'كلية الطب البشري' && currentGeneralRadio && currentGeneralRadio.checked) {
                if (currentSpecSelect) {
                    currentSpecSelect.disabled = false;
                    currentSpecSelect.value = 'طبيب عام';
                }
                if (hiddenMajor) {
                    hiddenMajor.value = 'طبيب عام';
                }
            } else {
                if (currentSpecSelect && currentSpecSelect.value) {
                    currentSpecSelect.disabled = false;
                    if (hiddenMajor) hiddenMajor.value = currentSpecSelect.value;
                }
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

