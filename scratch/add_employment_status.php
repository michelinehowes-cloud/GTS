<?php
$files = [
    'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/create.blade.php' => 'create',
    'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/edit.blade.php' => 'edit'
];

foreach ($files as $file => $type) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check if employment_status already exists
        if (strpos($content, 'name="employment_status"') === false) {
            
            $oldVal = $type == 'edit' ? "old('employment_status', \$graduate->employment_status)" : "old('employment_status')";
            
            $employmentStatusHtml = '
                            <div class="col-12">
                                <label for="employment_status" class="form-label">
                                    حالة التوظيف <span class="required">*</span>
                                </label>
                                <select class="form-select @error(\'employment_status\') is-invalid @enderror"
                                    id="employment_status" name="employment_status" required>
                                    <option value="">اختر حالة التوظيف</option>
                                    <option value="employed" {{ ' . $oldVal . ' == \'employed\' ? \'selected\' : \'\' }}>موظف</option>
                                    <option value="unemployed" {{ ' . $oldVal . ' == \'unemployed\' ? \'selected\' : \'\' }}>غير موظف</option>
                                    <option value="seeking_opportunities" {{ ' . $oldVal . ' == \'seeking_opportunities\' ? \'selected\' : \'\' }}>باحث عن عمل</option>
                                    <option value="further_study" {{ ' . $oldVal . ' == \'further_study\' ? \'selected\' : \'\' }}>مستكمل للدراسة</option>
                                </select>
                                @error(\'employment_status\')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>';
                            
            $targetStr = '<div class="col-12">
                                <label for="experiences"';
                                
            $replacementStr = $employmentStatusHtml . '
                            
                            <div class="col-12">
                                <label for="experiences"';
                                
            $content = str_replace($targetStr, $replacementStr, $content);
            file_put_contents($file, $content);
            echo "Added employment_status to $type.\n";
        } else {
            echo "employment_status already in $type.\n";
        }
    }
}
