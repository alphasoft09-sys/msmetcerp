<div class="row">
    <div class="col-md-4 text-center">
        @if($student->Photo)
            <img src="{{ route('images.students', ['filename' => basename($student->Photo)]) }}" 
                 alt="Student Photo" 
                 class="img-fluid rounded mb-3" 
                 style="max-width: 200px;">
        @else
            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 200px; height: 200px;">
                <i class="bi bi-person text-white" style="font-size: 4rem;"></i>
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <h4 class="mb-3">{{ $student->Name }}</h4>
        
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-2">Basic Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Roll No:</strong></td>
                        <td><span class="badge bg-primary">{{ $student->RollNo }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Ref No:</strong></td>
                        <td><span class="badge bg-secondary">{{ $student->RefNo }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Father's Name:</strong></td>
                        <td>{{ $student->FatherName }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date of Birth:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($student->DOB)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gender:</strong></td>
                        <td>{{ $student->Gender }}</td>
                    </tr>
                    <tr>
                        <td><strong>Category:</strong></td>
                        <td>{{ $student->Category }}</td>
                    </tr>
                    @if($student->Minority)
                        <tr>
                            <td><strong>Minority Type:</strong></td>
                            <td>{{ $student->MinorityType ?? 'Not specified' }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-2">Academic Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Program:</strong></td>
                        <td>{{ $student->ProgName }}</td>
                    </tr>
                    <tr>
                        <td><strong>Education:</strong></td>
                        <td>{{ $student->EducationName }}</td>
                    </tr>
                    @if($student->TraineeFee)
                        <tr>
                            <td><strong>Fee:</strong></td>
                            <td>₹{{ number_format($student->TraineeFee, 2) }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-muted mb-2">Contact Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Mobile:</strong></td>
                        <td><i class="bi bi-telephone me-1"></i>{{ $student->MobileNo }}</td>
                    </tr>
                    @if($student->PhoneNo)
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td><i class="bi bi-telephone-fill me-1"></i>{{ $student->PhoneNo }}</td>
                        </tr>
                    @endif
                    @if($student->Email)
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><i class="bi bi-envelope me-1"></i>{{ $student->Email }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-muted mb-2">Address</h6>
                <p class="mb-1">{{ $student->Address }}</p>
                <p class="mb-1">{{ $student->City }}, {{ $student->District }}, {{ $student->State }}</p>
                <p class="mb-1">{{ $student->Country }} - {{ $student->Pincode }}</p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-muted mb-2">Status</h6>
                @if($student->Email)
                    <span class="badge bg-success">Has Login Access</span>
                @else
                    <span class="badge bg-warning">No Login Access</span>
                @endif
            </div>
        </div>
    </div>
</div> 