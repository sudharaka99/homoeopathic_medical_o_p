@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2 py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daily Exercise</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('front.account.slidebar')
            </div>

            <!-- Article Content -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-4">
                        <!-- Article Header Inside Card -->
                        <div class="text-center mb-5">
                            <i class="fas fa-walking fa-3x text-success mb-3"></i>
                            <h1 class="fw-bold mb-3">Daily Exercise</h1>
                            <p class="text-muted lead">Get moving to boost your health and feel great every day!</p>
                        </div>

                        <!-- Quick Tip -->
                        <div class="alert alert-success border-0 mb-5">
                            <h6 class="mb-2"><i class="fas fa-star text-warning me-2"></i>Quick Tip:</h6>
                            <p class="mb-0">Take a 10-minute walk after dinner to kickstart your routine!</p>
                        </div>

                        <h3 class="fw-bold mb-3 text-success">Why Exercise Matters</h3>
                        <p class="text-muted">Think of exercise as your body’s daily tune-up. It strengthens your heart, lifts your mood, and keeps diseases at bay. Regular activity can also make you feel more energized and confident.</p>
                        <hr class="my-4">

                        <h3 class="fw-bold mb-3 text-success">How Much and What to Avoid</h3>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-success mb-3">Recommended Activity</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">150 minutes of moderate exercise weekly (like brisk walking)</li>
                                            <li class="mb-2">75 minutes of vigorous exercise weekly (like running)</li>
                                            <li class="mb-2">Strength training 2 or more days per week</li>
                                            <li>Include balance exercises if you’re over 65</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-success mb-3">Risks of Inactivity</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Weight gain and obesity risk</li>
                                            <li class="mb-2">Increased chance of heart disease</li>
                                            <li class="mb-2">Weaker muscles and bones</li>
                                            <li>Higher stress and anxiety levels</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-success">Why You'll Love Being Active</h3>
                        <div class="row g-3 p-3 bg-light rounded mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Heart Health</h6>
                                        <p class="small text-muted mb-0">Exercise keeps your heart strong and lowers cholesterol.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Mental Wellbeing</h6>
                                        <p class="small text-muted mb-0">Physical activity reduces stress and boosts happiness.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Stronger Muscles</h6>
                                        <p class="small text-muted mb-0">Builds strength for easier daily tasks.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Weight Control</h6>
                                        <p class="small text-muted mb-0">Burns calories to help maintain a healthy weight.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-success">Easy Ways to Get Moving</h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info border-0">
                                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Pro Tips:</h6>
                                    <ul class="mb-0">
                                        <li>Start with short 10-minute walks daily</li>
                                        <li>Pick fun activities like dancing or cycling</li>
                                        <li>Track steps with a fitness app or device</li>
                                        <li>Try bodyweight exercises like squats at home</li>
                                        <li>Check with your doctor before new routines</li>
                                        <li>Schedule workouts to build a habit</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-primary me-2">Back to Dashboard</a>
                    <a href="{{ route('health-tips.sleep') }}" class="btn btn-outline-primary">Next: Sleep</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
