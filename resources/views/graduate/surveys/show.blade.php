@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white p-4 rounded-top-3">
                    <h3 class="mb-2 fw-bold">{{ $survey->title }}</h3>
                    <p class="mb-0 opacity-75">{{ $survey->description }}</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('graduate.surveys.store', $survey->id) }}" method="POST">
                        @csrf
                        
                        @foreach($survey->questions as $index => $question)
                            <div class="mb-4 p-3 border rounded bg-light">
                                <label class="form-label fw-bold mb-3">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    {{ $question['question'] }}
                                    @if(isset($question['required']) && $question['required'])
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if($question['type'] === 'text')
                                    <input type="text" name="answers[{{ $index }}]" class="form-control" 
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                
                                @elseif($question['type'] === 'textarea')
                                    <textarea name="answers[{{ $index }}]" class="form-control" rows="3"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}></textarea>
                                
                                @elseif($question['type'] === 'radio')
                                    @foreach($question['options'] as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="answers[{{ $index }}]" 
                                                id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}"
                                                {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                            <label class="form-check-label" for="q{{ $index }}_opt{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                
                                @elseif($question['type'] === 'checkbox')
                                    @foreach($question['options'] as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][]" 
                                                id="q{{ $index }}_opt{{ $loop->index }}" value="{{ $option }}">
                                            <label class="form-check-label" for="q{{ $index }}_opt{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach

                                @elseif($question['type'] === 'select')
                                    <select name="answers[{{ $index }}]" class="form-select"
                                        {{ isset($question['required']) && $question['required'] ? 'required' : '' }}>
                                        <option value="" selected disabled>اختر إجابة...</option>
                                        @foreach($question['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        @endforeach

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> إرسال الإجابات
                            </button>
                            <a href="{{ route('graduate.surveys.index') }}" class="btn btn-outline-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
