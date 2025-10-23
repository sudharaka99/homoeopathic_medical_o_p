@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2 py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Balanced Diet</li>
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
                            <i class="fas fa-apple-alt fa-3x text-warning mb-3"></i>
                            <h1 class="fw-bold mb-3">Balanced Diet</h1>
                            <p class="text-muted lead">Fuel your body with the right foods for energy and health.</p>
                        </div>

                        <!-- Quick Tip -->
                        <div class="alert alert-success border-0 mb-5">
                            <h6 class="mb-2"><i class="fas fa-star text-warning me-2"></i>Quick Tip:</h6>
                            <p class="mb-0">Fill half your plate with colorful veggies at every meal!</p>
                        </div>

                        <h3 class="fw-bold mb-3 text-warning">Why Diet Matters</h3>
                        <p class="text-muted">A balanced diet is like premium fuel for your body. It gives you energy, strengthens your defenses, and helps prevent diseases. Eating well keeps you feeling great inside and out.</p>
                        <hr class="my-4">

                        <h3 class="fw-bold mb-3 text-warning">What to Eat and What to Skip</h3>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-warning mb-3">Daily Food Groups</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Vegetables: 2½ cups daily (think broccoli, spinach)</li>
                                            <li class="mb-2">Fruits: 2 cups daily (like berries, apples)</li>
                                            <li class="mb-2">Grains: 6 oz daily (half whole grains like quinoa)</li>
                                            <li>Protein: 5½ oz daily (lean meats, beans, nuts)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h4 class="fw-semibold text-warning mb-3">Unhealthy Choices</h4>
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">Sugary snacks and sodas</li>
                                            <li class="mb-2">Processed foods with trans fats</li>
                                            <li class="mb-2">High-sodium packaged meals</li>
                                            <li>Excessive fatty meats</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-warning">Why You'll Love Eating Well</h3>
                        <div class="row g-3 p-3 bg-light rounded mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Healthier Heart</h6>
                                        <p class="small text-muted mb-0">Good food choices lower heart disease risk.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Weight Control</h6>
                                        <p class="small text-muted mb-0">Balanced meals help maintain a healthy weight.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Stronger Immunity</h6>
                                        <p class="small text-muted mb-0">Nutrients like vitamin C boost your defenses.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Mental Clarity</h6>
                                        <p class="small text-muted mb-0">Healthy foods improve focus and mood.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3 text-warning">Easy Ways to Eat Better</h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info border-0">
                                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Pro Tips:</h6>
                                    <ul class="mb-0">
                                        <li>Fill half your plate with colorful vegetables</li>
                                        <li>Choose whole grains like brown rice or oats</li>
                                        <li>Swap sugary drinks for water or herbal tea</li>
                                        <li>Check nutrition labels for hidden sugars</li>
                                        <li>Plan weekly meals for balanced nutrition</li>
                                        <li>Add lean proteins like fish or lentils</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-primary me-2">Back to Dashboard</a>
                    <a href="{{ route('health-tips.hydration') }}" class="btn btn-outline-primary">Previous: Hydration</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
