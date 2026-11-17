@php
    $rawAnswers = isset($existingResponse) ? $existingResponse->answers : [];
    if (is_string($rawAnswers)) {
        $rawAnswers = json_decode($rawAnswers, true);
    }
    $previousAnswers = is_array($rawAnswers) ? $rawAnswers : [];

    $surveyQuestions = $survey->questions;
    if (is_string($surveyQuestions)) {
        $surveyQuestions = json_decode($surveyQuestions, true);
    }
    $surveyQuestions = is_array($surveyQuestions) ? array_values($surveyQuestions) : [];
@endphp

<div class="survey-questions-list">
    @foreach($surveyQuestions as $index => $question)
        @php
            $question = is_array($question) ? $question : [];
            $qType = $question['type'] ?? 'text';
            $qText = $question['question'] ?? ($question['text'] ?? 'سؤال');
            $isRequired = !empty($question['required']);

            $qOptions = $question['options'] ?? [];
            if (is_string($qOptions)) {
                $qOptions = array_values(array_filter(array_map('trim', explode("\n", $qOptions))));
            }
            $qOptions = is_array($qOptions) ? $qOptions : [];

            $prevRaw = $previousAnswers[$index] ?? '';
            $prevStr = is_scalar($prevRaw) ? (string) $prevRaw : '';
            $prevArr = is_array($prevRaw) ? $prevRaw : ($prevStr !== '' ? [$prevStr] : []);
        @endphp

        <div class="mb-4 p-3.5 p-md-4 rounded-4 bg-light border">
            <label class="fw-bold mb-3 d-flex align-items-start gap-2">
                <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background: #0d3882; color: #fff; font-size: 0.82rem;">
                    {{ $index + 1 }}
                </span>
                <span class="fs-6 text-dark lh-base pt-0.5">
                    {{ $qText }}
                    @if($isRequired)
                        <span class="text-danger ms-1">*</span>
                    @endif
                </span>
            </label>

            @if($qType === 'text')
                <input type="text" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3"
                    value="{{ $prevStr }}"
                    placeholder="اكتب إجابتك هنا..."
                    {{ $isRequired ? 'required' : '' }}>

            @elseif($qType === 'textarea')
                <textarea name="answers[{{ $index }}]" class="form-control bg-white rounded-3 p-3" rows="3"
                    placeholder="اكتب ملاحظاتك وتفاصيل إجابتك هنا..."
                    {{ $isRequired ? 'required' : '' }}>{{ $prevStr }}</textarea>

            @elseif($qType === 'radio')
                <div class="ps-2">
                    @foreach($qOptions as $option)
                        @php $optVal = is_array($option) ? ($option['label'] ?? $option['text'] ?? $option['value'] ?? '') : (string) $option; @endphp
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[{{ $index }}]"
                                id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $optVal }}"
                                {{ $prevStr !== '' && $prevStr == $optVal ? 'checked' : '' }}
                                {{ $isRequired ? 'required' : '' }}>
                            <label class="form-check-label text-dark fw-semibold" for="q{{ $index }}_opt{{ $loop->index }}" style="font-size: 0.92rem;">
                                {{ $optVal }}
                            </label>
                        </div>
                    @endforeach
                </div>

            @elseif($qType === 'checkbox')
                <div class="ps-2">
                    @foreach($qOptions as $option)
                        @php $optVal = is_array($option) ? ($option['label'] ?? $option['text'] ?? $option['value'] ?? '') : (string) $option; @endphp
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][]"
                                id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $optVal }}"
                                {{ in_array($optVal, $prevArr, true) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark fw-semibold" for="q{{ $index }}_opt{{ $loop->index }}" style="font-size: 0.92rem;">
                                {{ $optVal }}
                            </label>
                        </div>
                    @endforeach
                </div>

            @elseif($qType === 'select')
                <select name="answers[{{ $index }}]" class="form-select bg-white rounded-3 py-2 px-3"
                    {{ $isRequired ? 'required' : '' }}>
                    <option value="" {{ $prevStr === '' ? 'selected' : '' }} disabled>اختر إجابة من القائمة...</option>
                    @foreach($qOptions as $option)
                        @php $optVal = is_array($option) ? ($option['label'] ?? $option['text'] ?? $option['value'] ?? '') : (string) $option; @endphp
                        <option value="{{ $optVal }}" {{ $prevStr === $optVal ? 'selected' : '' }}>{{ $optVal }}</option>
                    @endforeach
                </select>

            @elseif($qType === 'rating')
                @php
                    $maxRating = max(1, (int) ($question['max_rating'] ?? 5));
                    $currentRating = is_numeric($prevStr) ? (int) $prevStr : '';
                @endphp
                <div class="rating-stars bg-white p-3 rounded-3 border" data-question-index="{{ $index }}">
                    <input type="hidden" name="answers[{{ $index }}]" id="rating_{{ $index }}" value="{{ $currentRating }}"
                        {{ $isRequired ? 'required' : '' }}>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        @for($i = 1; $i <= $maxRating; $i++)
                            <span class="star" data-rating="{{ $i }}" style="font-size: 2.2rem; cursor: pointer; color: {{ $currentRating && $i <= $currentRating ? '#ffc107' : '#ddd' }}; user-select: none;">
                                ★
                            </span>
                        @endfor
                        <span class="rating-display ms-3 fw-bold {{ $currentRating ? 'text-warning' : 'text-muted' }}">
                            {{ $currentRating ? $currentRating . ($currentRating == 1 ? ' نجمة' : ' نجوم') : 'لم يتم التقييم بعد' }}
                        </span>
                    </div>
                </div>

            @elseif($qType === 'date')
                <input type="date" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3"
                    value="{{ $prevStr }}"
                    {{ $isRequired ? 'required' : '' }}>

            @elseif($qType === 'email')
                <input type="email" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3"
                    value="{{ $prevStr }}"
                    placeholder="example@domain.com"
                    {{ $isRequired ? 'required' : '' }}>

            @elseif($qType === 'number')
                <input type="number" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3"
                    value="{{ $prevStr }}"
                    placeholder="أدخل رقماً..."
                    {{ $isRequired ? 'required' : '' }}>

            @else
                <input type="text" name="answers[{{ $index }}]" class="form-control bg-white rounded-3 py-2 px-3"
                    value="{{ $prevStr }}"
                    placeholder="اكتب إجابتك هنا..."
                    {{ $isRequired ? 'required' : '' }}>
            @endif
        </div>
    @endforeach
</div>
