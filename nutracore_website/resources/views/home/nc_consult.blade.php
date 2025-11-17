@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    ?>


    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> NC Consult
                </div>
            </div>
        </div>
        <div class="page-content pt-50">
            <img src="{{url('public/assets/Free_Expert_Consultation.webp')}}" style="width: 100%;height: 400px">
            <div class="container my-5">

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-center mb-2">NC Consult – Nutrition Consultation</h3>
                                <p class="text-center text-muted mb-4">Personalized diet & supplement plan (one-time
                                    consultation).</p>

                                <form action="{{ route('consultation_save') }}" method="POST">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="fullName" class="form-label">Full Name*</label>
                                            <input type="text" class="form-control" id="fullName" name="fullName"
                                                   placeholder="Enter full name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="age" class="form-label">Age*</label>
                                            <input type="number" class="form-control" id="age" name="age"
                                                   placeholder="Enter age" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="gender" class="form-label">Gender*</label>
                                            <select class="form-select" id="gender" name="gender" required>
                                                <option value="" selected>Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="mobile" class="form-label">Mobile Number*</label>
                                            <input type="tel" class="form-control" id="mobile" name="mobile"
                                                   placeholder="10 digits" pattern="[0-9]{10}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               placeholder="Enter email">
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="primaryGoal" class="form-label">Primary Goal*</label>
                                            <select class="form-select" id="primaryGoal" name="primaryGoal" required>
                                                <option value="" selected>Select</option>
                                                <option value="weight_loss">Weight Loss</option>
                                                <option value="muscle_gain">Muscle Gain</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="currentWeight" class="form-label">Current Weight (kg)*</label>
                                            <input type="number" class="form-control" id="currentWeight" name="currentWeight"
                                                   placeholder="Enter current weight" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="targetWeight" class="form-label">Target Weight (kg)*</label>
                                        <input type="number" class="form-control" id="targetWeight" name="targetWeight"
                                               placeholder="Enter target weight" required>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="dietPreference" class="form-label">Diet Preference*</label>
                                            <select class="form-select" id="dietPreference" name="dietPreference" required>
                                                <option value="" selected>Select</option>
                                                <option value="vegetarian">Vegetarian</option>
                                                <option value="non_vegetarian">Non-Vegetarian</option>
                                                <option value="vegan">Vegan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="activityLevel" class="form-label">Typical Daily Activity Level*</label>
                                            <select class="form-select" id="activityLevel" name="activityLevel" required>
                                                <option value="" selected>Select</option>
                                                <option value="low">Low</option>
                                                <option value="moderate">Moderate</option>
                                                <option value="high">High</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="healthConditions" class="form-label">Any existing health conditions or allergies? (optional)</label>
                                        <textarea class="form-control" id="healthConditions" name="healthConditions" rows="3"
                                                  placeholder="Thyroid, Diabetes, PCOS, Lactose intolerance, etc."></textarea>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label d-block">Preferred Consultation Mode*</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="consultMode" id="videoCall" value="video" required>
                                                <label class="form-check-label" for="videoCall">Video Call</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="consultMode" id="phoneCall" value="phone" required>
                                                <label class="form-check-label" for="phoneCall">Phone Call</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="preferredDate" class="form-label">Preferred Date*</label>
                                            <input type="date" class="form-control" id="preferredDate" name="preferredDate" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="timeSlot" class="form-label">Preferred Time Slot*</label>
                                            <select class="form-select" id="timeSlot" name="timeSlot" required>
                                                <option value="" selected>Select</option>
                                                <option value="10-11">10:00 AM - 11:00 AM</option>
                                                <option value="11-12">11:00 AM - 12:00 PM</option>
                                                <option value="12-1">12:00 PM - 1:00 PM</option>
                                                <option value="1-2">1:00 PM - 2:00 PM</option>
                                                <option value="2-3">2:00 PM - 3:00 PM</option>
                                                <option value="3-4">3:00 PM - 4:00 PM</option>
                                                <option value="4-5">4:00 PM - 5:00 PM</option>
                                                <option value="5-6">5:00 PM - 6:00 PM</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-check mt-5">
                                        <input class="form-check-input" type="checkbox" id="termsCheck" name="termsCheck" required>
                                        <label class="form-check-label" for="termsCheck">
                                            I understand the terms & conditions and I agree to be contacted by the NutraCore® nutrition team.
                                        </label>
                                    </div>

                                    <div class="text-center mt-5">
                                        <button type="submit" class="btn btn-primary px-5">Book Consultation</button>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

@endsection
