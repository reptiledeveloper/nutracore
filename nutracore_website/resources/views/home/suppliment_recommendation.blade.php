@extends('home.layout')
@section('content')

    @php
        $user = Auth::user();
$categories = $supplimentsArray['goal_categories'] ??[];
 $activity_array = [
        "Walking / Running",
        "Sports",
        "Gym (Beginner)",
        "Gym (Intermediate/Advance)",
        "Yoga",
        "No Activity"
    ];
    @endphp


    <style>
        .center-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;

        }

        .form-container {
            width: 100%;
            max-width: 550px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }

        .progress-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center; /* ← This centers both the circle and the text */
            text-align: center;
        }

        .circle {
            width: 50px;
            height: 50px;
            border: 2px solid #ccc;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center; /* Center number vertically */
            justify-content: center; /* Center number horizontally */
            font-size: 16px;
            font-weight: bold;
            color: #ccc;
            margin-bottom: 8px;
        }

        .progress-step p {
            margin: 0;
            font-size: 13px;
            color: #555;
        }

        .progress-step.completed .circle {
            border-color: #00A8A8;
            background-color: #00A8A8;
            color: white;
        }

        .progress-step.active .circle {
            border-color: #00A8A8;
            color: #00A8A8;
        }


        .progress-step p {
            margin-top: 8px;
            font-size: 14px;
            color: #555;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .d-flex1 {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        button {
            background-color: #00A8A8;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            width: 100%;
            margin-top: 20px;
        }

        button:disabled {
            background-color: #ccc;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
    <style>
        .gender-selection {
            display: flex;
            border: 1px solid #00A8A8;
            border-radius: 5px;
            overflow: hidden;
        }

        .gender-selection input[type="radio"] {
            display: none;
        }

        .gender-selection label {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            font-weight: bold;
            color: gray;
        }

        .gender-selection input:checked + label {
            background-color: #00A8A8;
            color: white;
        }

        .gender-selection label:first-child {
            border-right: 1px solid #00A8A8;
        }

        label {
            margin-bottom: 0px;
        }

        .card_activity {
            height: 50px;
            border: 1px solid #ccc;
            margin: 10px;
            border-radius: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card_activity1 {
            display: flex;
            flex-direction: column; /* Stack image and text vertically */
            align-items: center; /* Center horizontally */
            justify-content: center; /* Center vertically if height is set */
            text-align: center; /* Center text */
            cursor: pointer; /* Keep the pointer on hover */
            padding: 10px; /* Optional padding */
            height: 200px;
            border: 1px solid #ccc;
            margin: 10px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card_activity img {
            max-width: 100px; /* Adjust image size as needed */
            margin-bottom: 10px; /* Space between image and text */
        }

        #selected-value {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .card_activity:hover {
            background-color: #e0e0e0;
            box-shadow: 0px 8px 12px rgba(0, 0, 0, 0.2);
        }

        .card_activity.selected {
            background-color: #00b3b3;
            color: white;
            border-color: #00b3b3;
        }

        .card_activity1.selected {
            background-color: #00b3b3;
            color: white;
            border-color: #00b3b3;
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin: 40px 0;
        }

        /* Horizontal Bar (center line) */
        .progress-container::before {
            content: "";
            position: absolute;
            top: 19px;
            left: 12%; /* increased gap */
            right: 12%; /* increased gap */
            height: 4px;
            background: #ccc;
            z-index: 1;
        }


        /* Steps */
        .progress-step {
            text-align: center;
            position: relative;
            z-index: 2; /* Circles above the line */
        }


        /* Active example */
        .progress-step.active .circle {
            background: #f9b201;
            color: #000;
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin: 20px auto;
            width: 100%;
            max-width: 600px;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        .progress-step .circle {
            width: 40px;
            height: 40px;
            background: #0f5759;
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 16px;
        }

        /* --------------------------
           MOBILE FIX (375px & below)
        --------------------------- */
        @media (max-width: 480px) {
            .progress-container {
                flex-direction: row;
                justify-content: space-between;
                gap: 10px;
            }

            .progress-step {
                flex: unset;
                width: 23%;
            }

            .progress-step p {
                font-size: 10px;
                line-height: 12px;
                margin: 0;
                word-break: break-word;
            }

            .progress-step .circle {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .progress-container::before {
                top: 15px;
            }
        }

    </style>


    <div class="container">
        <img src="{{url('public/assets/SuppRecommBanner.webp')}}"
             class="img-fluid"
             style="width: 100%; height: 400px; border-radius: 20px; object-fit: cover; margin-top: 20px">
        <div class="center-wrapper">
            <div class="form-container">
                <div class="progress-container">
                    <div class="progress-step" id="step1">
                        <div class="circle">1</div>
                        <p>Personal Details</p>
                    </div>
                    <div class="progress-step" id="step2">
                        <div class="circle">2</div>
                        <p>Body Type</p>
                    </div>
                    <div class="progress-step" id="step3">
                        <div class="circle">3</div>
                        <p>Goal</p>
                    </div>
                    <div class="progress-step" id="step4">
                        <div class="circle">4</div>
                        <p>Lifestyle</p>
                    </div>
                </div>

                <form id="multi-step-form">
                    <!-- Step 1 -->
                    <div class="form-step active" id="form-step1">
                        <h6>Personal Details</h6>
                        <p>Hi! What’s your name ?</p>
                        <label for="full-name"><strong>Full Name</strong></label>
                        <input type="text" name="name" placeholder="Your Name" value="{{$user->name??""}}" required>

                        <label for="full-name"><strong>You were born on*</strong></label>
                        <input type="date" name="dob" placeholder="" value="{{$user->dob??""}}" required>

                        <label for="full-name"><strong>Gender*</strong></label>
                        <div class="gender-selection">
                            <input type="radio" id="male" name="gender"
                                   value="male" {{!empty($user->gender) && $user->gender == 'male' ?"checked":""}}>
                            <label for="male">Male</label>

                            <input type="radio" id="female" name="gender"
                                   value="female" {{!empty($user->gender) && $user->gender == 'female' ?"checked":""}}>
                            <label for="female">Female</label>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="form-step" id="form-step2">
                        <h6>Personal Details</h6>
                        <p>About your body</p>
                        <label for="full-name"><strong>Height</strong></label>
                        <input type="text" name="height" placeholder="Height" value="{{$user->height??""}}" required>

                        <label for="full-name"><strong>Weight</strong></label>
                        <input type="text" name="weight" placeholder="Weight" value="{{$user->weight??""}}" required>


                    </div>
                    <input type="hidden" name="activity" value="{{$user->activity??''}}" id="activity">
                    <input type="hidden" name="health_profile" value="{{$user->health_profile??''}}" id="health_profile">
                    <input type="hidden" name="submit_where" value="suppliment" >
                    <!-- Step 3 -->
                    <div class="form-step" id="form-step3">
                        <h6>Health Profile</h6>
                        <p>What is your Goal?</p>
                        <div class="row">
                            @foreach($categories as $key => $cat)
                                <div class="col-md-6">
                                    <div class="card_activity1 {{$user->health_profile == $cat->id?"selected":""}}" onclick="selectCard1(this,'{{$key}}','{{$cat->id}}')">
                                        <img
                                            src="{{\App\Helpers\CustomHelper::getImageUrl('categories',$cat->image??'')}}"
                                            style="height: 100px">
                                        {{$cat->name??''}}
                                    </div>
                                </div>
                            @endforeach

                        </div>

                    </div>

                    <!-- Step 4 -->
                    <div class="form-step" id="form-step4">
                        <h6>Daily activity & Lifestyle</h6>
                        <p>How active are you?</p>
                        @foreach($activity_array as $key =>$value )
                            <div class="card_activity {{$user->activity == $value?"selected":""}}" onclick="selectCard(this,'{{$key}}','{{$value}}')">
                                {{$value}}
                            </div>
                        @endforeach


                        <label for="full-name"><strong>Food Choice*</strong></label>
                        <div class="gender-selection">
                            <input type="radio" id="Veg" name="food_choice" value="Veg" {{$user->food_choice == "Veg" ?"checked":""}}>
                            <label for="Veg">Veg</label>

                            <input type="radio" id="Non-Veg" name="food_choice" value="Non-Veg" {{$user->food_choice == "Non-Veg" ?"checked":""}}>
                            <label for="Non-Veg">Non-Veg</label>
                            <input type="radio" id="Eggetarian" name="food_choice" value="Eggetarian" {{$user->food_choice == "Eggetarian" ?"checked":""}}>
                            <label for="Eggetarian">Eggetarian</label>
                        </div>
                    </div>

                    <!-- Navigation buttons -->
                    <div class="d-flex1">
                        <button type="button" id="prev-btn" disabled>Back</button>
                        <button type="button" id="next-btn">Next</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        const steps = document.querySelectorAll('.form-step');
        const progressSteps = document.querySelectorAll('.progress-step');
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        let currentStep = 0;

        function showStep(stepIndex) {
            steps.forEach((step, index) => {
                step.classList.remove('active');
                if (index === stepIndex) {
                    step.classList.add('active');
                }
            });

            progressSteps.forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index === stepIndex) {
                    step.classList.add('active');
                } else if (index < stepIndex) {
                    step.classList.add('completed');
                }
            });

            prevBtn.disabled = stepIndex === 0;
            nextBtn.textContent = stepIndex === steps.length - 1 ? 'Submit' : 'Next';
        }

        nextBtn.addEventListener('click', () => {
            submitFormAjax();
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            } else {
                window.location.href =  "{{route('suppliment_product')}}";
            }

        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });

        showStep(currentStep);


        function submitFormAjax() {
            let form = document.getElementById("multi-step-form");
            let formData = new FormData(form);

            let url = '{{route('profile')}}';

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === true) {
                        // alert("Profile saved successfully!");
                        // window.location.href = data.redirect ?? window.location.href;
                    } else {
                        alert(data.message || "Something went wrong");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Server error!");
                });
        }


    </script>

    <script>
        function selectCard(element, value,val) {
            // Remove 'selected' class from all cards
            document.querySelectorAll('.card_activity').forEach(card => {
                card.classList.remove('selected');
            });

            // Add 'selected' class to the clicked card
            element.classList.add('selected');
            // Show selected value
            $('#activity').val(val);
        }

        function selectCard1(element, value,val) {
            // Remove 'selected' class from all cards
            document.querySelectorAll('.card_activity1').forEach(card => {
                card.classList.remove('selected');
            });

            // Add 'selected' class to the clicked card
            element.classList.add('selected');
            // Show selected value
            $('#health_profile').val(val);
        }


    </script>
@endsection
