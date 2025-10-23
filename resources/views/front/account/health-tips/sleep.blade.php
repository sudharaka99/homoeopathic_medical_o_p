@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2 py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Proper Sleep</li>
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
                            <i class="fas fa-bed fa-3x text-info mb-3"></i>
                            <h1 class="fw-bold mb-3">Proper Sleep</h1>
                            <p class="text-muted lead">Learn how good sleep recharges your body and mind for a better day.</p>
                        </div>

                        <!-- Quick Tip -->
                        <div class="alert alert-success border-0 mb-5">
                            <h6 class="mb-2"><i class="fas fa-star text-warning me-2"></i>Quick Tip:</h6>
                            <p class="mb-0">Dim your bedroom lights an hour before bed to signal sleep time!</p>
                        </div>

                        <h3 class="fw-bold mb-3 text-info">Why Sleep Matters</h3>
                        <p class="text-muted">Sleep is like hitting the reset button for your body. It repairs muscles, boosts your mood, and sharpens your mind. Without enough rest, you might feel cranky or struggle with focus.</p>
                        <hr class="my-4">

                        <h3 class="fw-bold mb-3 text-info">How Much and What to Watch For</h3>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-info mb-3">Recommended Sleep</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Adults (18-64): 7-9 hours per night</li>
                                            <li class="mb-2">Older adults (65+): 7-8 hours per night</li>
                                            <li class="mb-2">Teens (14-17): 8-10 hours per night</li>
                                            <li>Adjust based on your energy and health needs</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-info mb-3">Signs of Poor Sleep</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Feeling tired or sleepy during the day</li>
                                            <li class="mb-2">Mood swings or irritability</li>
                                            <li class="mb-2">Trouble focusing or remembering things</li>
                                            <li>Waking up often during the night</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-info">Why You'll Love Great Sleep</h3>
                        <div class="row g-3 p-3 bg-light rounded mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Sharper Mind</h6>
                                        <p class="small text-muted mb-0">Sleep boosts memory and problem-solving skills.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Healthier Heart</h6>
                                        <p class="small text-muted mb-0">Good sleep lowers heart disease risk.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Better Mood</h6>
                                        <p class="small text-muted mb-0">Rest helps you stay calm and happy.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Stronger Immunity</h6>
                                        <p class="small text-muted mb-0">Sleep helps your body fight off infections.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-info">Easy Ways to Sleep Better</h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info border-0">
                                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Pro Tips:</h6>
                                    <ul class="mb-0">
                                        <li>Stick to a regular sleep schedule, even on weekends</li>
                                        <li>Avoid screens an hour before bed to relax your brain</li>
                                        <li>Keep your bedroom dark, cool, and quiet</li>
                                        <li>Skip caffeine and alcohol after midday</li>
                                        <li>Try deep breathing or meditation before bed</li>
                                        <li>Avoid heavy meals late at night</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-primary me-2">Back to Dashboard</a>
                    <a href="{{ route('health-tips.diet') }}" class="btn btn-outline-primary">Next: Diet</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
