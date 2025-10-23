@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2 py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stay Hydrated</li>
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
                            <i class="fas fa-glass-whiskey fa-3x text-primary mb-3"></i>
                            <h1 class="fw-bold mb-3">Stay Hydrated</h1>
                            <p class="text-muted lead">Discover why water is your body's best friend for staying healthy and energized.</p>
                        </div>

                        <!-- Quick Tip -->
                        <div class="alert alert-success border-0 mb-5">
                            <h6 class="mb-2"><i class="fas fa-star text-warning me-2"></i>Quick Tip:</h6>
                            <p class="mb-0">Keep a water bottle by your side all day to sip regularly!</p>
                        </div>

                        <h3 class="fw-bold mb-3 text-primary">Why Water Matters</h3>
                        <p class="text-muted">Your body is like a car engine—it needs water to run smoothly. It keeps you cool, helps digestion, and flushes out toxins. Without enough water, you might feel sluggish or get sick more often.</p>
                        <hr class="my-4">

                        <h3 class="fw-bold mb-3 text-primary">How Much and What to Watch For</h3>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-primary mb-3">Daily Water Needs</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Men: Aim for 3.7 liters (about 15 cups) from drinks and food</li>
                                            <li class="mb-2">Women: Target 2.7 liters (about 11 cups) daily</li>
                                            <li class="mb-2">Drink more during workouts, hot days, or illness</li>
                                            <li>Adjust based on your body size and activity level</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-primary mb-3">Signs You're Dehydrated</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Feeling thirsty or having a dry mouth</li>
                                            <li class="mb-2">Dark yellow or amber urine</li>
                                            <li class="mb-2">Headaches or feeling dizzy</li>
                                            <li>Tiredness or trouble focusing</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-primary">Why You'll Love Staying Hydrated</h3>
                        <div class="row g-3 p-3 bg-light rounded mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Better Digestion</h6>
                                        <p class="small text-muted mb-0">Water helps your stomach break down food, keeping things moving smoothly.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Sharper Focus</h6>
                                        <p class="small text-muted mb-0">A hydrated brain stays alert, helping you tackle tasks with clarity.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Glowing Skin</h6>
                                        <p class="small text-muted mb-0">Water keeps your skin soft and may reduce signs of aging.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Weight Control</h6>
                                        <p class="small text-muted mb-0">Drinking water before meals can curb hunger and boost metabolism.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-primary">Easy Ways to Stay Hydrated</h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info border-0">
                                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Pro Tips:</h6>
                                    <ul class="mb-0">
                                        <li>Carry a reusable water bottle everywhere</li>
                                        <li>Set phone reminders to sip water hourly</li>
                                        <li>Add lemon, cucumber, or mint for flavor</li>
                                        <li>Drink a glass of water before every meal</li>
                                        <li>Check your urine color—aim for pale yellow</li>
                                        <li>Up your intake during exercise or hot weather</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-primary me-2">Back to Dashboard</a>
                    <a href="{{ route('health-tips.exercise') }}" class="btn btn-outline-primary">Next: Exercise</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
