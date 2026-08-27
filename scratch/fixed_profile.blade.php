?@extends('layouts.app')

@section('page-title', '?????? ?§???´?±???© ?§?????¹?±??????')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        
        <!-- ?§?????³?? ?§???¬?§???¨??: ?­?§???© ?§???´?±?§???© ???§???´?¹?§?± -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #0F172A 0%, #045db0 100%);">
                    <div class="position-relative d-inline-block mb-3 mt-2">
                        @if($company->logo_path)
                            <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}" class="rounded-circle border border-4 border-white shadow" style="width: 120px; height: 120px; object-fit: cover; background: white;">
                        @else
                            <div class="rounded-circle border border-4 border-white shadow d-flex align-items-center justify-content-center bg-white text-primary" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                {{ mb_substr($company->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white border-2 rounded-circle shadow" title="?­?³?§?¨ ?????¹??">
                            <span class="visually-hidden">?????¹??</span>
                        </span>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $company->name }}</h5>
                    <p class="mb-0 text-white-50 small mt-1"><i class="fas fa-building me-1"></i> {{ $company->industry ?? '???·?§?¹ ?????± ???­?¯?¯' }}</p>
                </div>
                
                <div class="card-body p-4 bg-light">
                    <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">?????§?µ???? ?§???´?±?§???© ???¹ ?§???¬?§???¹?©</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="fas fa-handshake me-2"></i> ?????¹ ?§???´?±?§???©:</span>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            @php
                                $types = [
                                    'employment' => '?????¸????',
                                    'training' => '???¯?±???¨',
                                    'logistic_support' => '?¯?¹?? ?????¬?³????',
                                    'academic' => '?£???§?¯??????',
                                    'training_employment' => '???¯?±???¨ ???????¸????'
                                ];
                            @endphp
                            {{ $types[$company->partnership_type] ?? '?????± ???­?¯?¯' }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="fas fa-shield-alt me-2"></i> ?­?§???© ?§???´?±?§???©:</span>
                        @if($company->partnership_status == 'active')
                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> ???´?·?©</span>
                        @elseif($company->partnership_status == 'expired')
                            <span class="badge bg-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> ???????????©</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i> ?????¯ ?§?????±?§?¬?¹?©</span>
                        @endif
                    </div>
                    
                    @if($company->partnership_start_date)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> ???§?±???® ?§???¨?¯??:</span>
                        <span class="fw-bold small">{{ $company->partnership_start_date->format('Y-m-d') }}</span>
                    </div>
                    @endif
                    
                    @if($company->partnership_end_date)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="fas fa-calendar-times me-2"></i> ???§?±???® ?§???¥???????§??:</span>
                        <span class="fw-bold small">{{ $company->partnership_end_date->format('Y-m-d') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2"><i class="fas fa-chart-line me-2"></i> ?¥?­?µ?§?¦???§?? ?§?????´?§?·</h6>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h3 fw-bold text-primary mb-0">{{ $company->jobOpportunitiesCount ?? 0 }}</div>
                            <div class="text-muted small">???±?µ ?§???¹????</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h3 fw-bold text-success mb-0">{{ $company->partnershipDocumentsCount ?? 0 }}</div>
                            <div class="text-muted small">?§?????«?§?¦?? ?§?????±?????©</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ?§?????³?? ?§???±?¦???³??: ???¹?¯???? ?§???¨???§???§?? -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-edit me-2 text-primary"></i> ???­?¯???« ?¨???§???§?? ?§???´?±???©</h5>
                    <p class="text-muted small mt-1">???? ?¨???­?¯???« ???¹???????§?? ?´?±?????? ?????¸???± ?¨?´???? ?§?­???±?§???? ?????®?±???¬????.</p>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm border-0">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <h6 class="fw-bold text-primary mt-4 mb-2">?§?????¹???????§?? ?§???£?³?§?³???©</h6>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?§?³?? ?§???´?±???© <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" value="{{ old('name', $company->name) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">???·?§?¹ ?§???¹???? (?§???µ???§?¹?©) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-industry text-muted"></i></span>
                                    <input type="text" name="industry" class="form-control border-start-0 ps-0" value="{{ old('industry', $company->industry) }}" required placeholder="???«?§??: ?????????© ?§?????¹???????§???? ?§???µ?§???§???? ?¥???®">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">?´?¹?§?± ?§???´?±???© (Logo)</label>
                                <input type="file" name="logo" class="form-control" accept="image/png, image/jpeg, image/jpg, image/svg">
                                <div class="form-text">?????¶?? ?£?? ???????? ?¨?®???????© ?´???§???© (PNG) ???¨?£?¨?¹?§?¯ ?????³?§?????© (???±?¨?¹). ?§???­?¯ ?§???£???µ?? ?????­?¬?? 2MB.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">???¨?°?© ???¹?±???????© ?¹?? ?§???´?±???©</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="?§?????¨ ???¨?°?© ???®???µ?±?© ?¹?? ???´?§?· ?§???´?±???© ???±?¤???????§...">{{ old('description', $company->description) }}</textarea>
                            </div>
                            
                            <h6 class="fw-bold text-primary mt-4 mb-2">???¹???????§?? ?§???????§?µ?? ???§?????????¹</h6>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?±???? ?§?????§???? <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone', $company->phone) }}" required dir="ltr">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?§?????????¹ ?§???¥???????±??????</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                    <input type="url" name="website" class="form-control border-start-0 ps-0" value="{{ old('website', $company->website) }}" placeholder="https://www.example.com" dir="ltr">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">?¹?????§?? ?§???????± ?§???±?¦???³?? <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" name="address" class="form-control border-start-0 ps-0" value="{{ old('address', $company->address) }}" required>
                                </div>
                            </div>
                            
                            <h6 class="fw-bold text-primary mt-4 mb-2">?§???´?®?µ ?§?????±?¬?¹?? ?????????§?µ?? (HR / ?§???????«??)</h6>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?§?³?? ?§???????«??</label>
                                <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $company->contact_person) }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?§???????µ?¨</label>
                                <input type="text" name="contact_position" class="form-control" value="{{ old('contact_position', $company->contact_position) }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?±???? ???§???? ?§???????«??</label>
                                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $company->contact_phone) }}" dir="ltr">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">?§???¨?±???¯ ?§???¥???????±?????? ?????????«??</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $company->contact_email) }}" dir="ltr">
                            </div>
                            
                        </div>
                        
                        <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm" style="background-color: #8CC63F; border-color: #8CC63F;">
                                <i class="fas fa-save me-2"></i> ?­???¸ ?§?????­?¯???«?§??
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
    .form-control:focus, .input-group-text {
        border-color: #8CC63F;
        box-shadow: none;
    }
    .form-control:focus + .input-group-text {
        border-color: #8CC63F;
    }
</style>
@endsection

