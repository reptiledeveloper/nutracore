@extends('home.layout')
@section('content')
    <style>
        .center-wrapper {
            display: flex;
            padding: 20px;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
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
            align-items: center;    /* Center horizontally */
            justify-content: center; /* Center vertically if height is set */
            text-align: center;      /* Center text */
            cursor: pointer;         /* Keep the pointer on hover */
            padding: 10px;           /* Optional padding */
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
    </style>
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
                    <input type="text" id="full-name" name="full-name" placeholder="Your Name" required>

                    <label for="full-name"><strong>You were born on*</strong></label>
                    <input type="text" id="full-name" name="full-name" placeholder="Your Name" required>

                    <label for="full-name"><strong>Gender*</strong></label>
                    <div class="gender-selection">
                        <input type="radio" id="male" name="gender" value="male" checked>
                        <label for="male">Male</label>

                        <input type="radio" id="female" name="gender" value="female">
                        <label for="female">Female</label>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="form-step" id="form-step2">
                    <h6>Personal Details</h6>
                    <p>About your body</p>
                    <label for="full-name"><strong>Height</strong></label>
                    <input type="text" id="full-name" name="full-name" placeholder="Your Name" required>

                    <label for="full-name"><strong>Weight</strong></label>
                    <input type="text" id="full-name" name="full-name" placeholder="Your Name" required>


                </div>

                <!-- Step 3 -->
                <div class="form-step" id="form-step3">
                    <h6>Health Profile</h6>
                    <p>What is your Goal?</p>
                    <div class="row">
                        @for($i=0;$i<=10;$i++)
                         <div class="col-md-6">
                             <div class="card_activity1" onclick="selectCard1(this,'{{$i}}')">
                                 <img src="http://localhost/nutracore/nutracore_website/public/assets/logo.png">
                                 Walking
                             </div>
                         </div>
                        @endfor

                    </div>

                </div>

                <!-- Step 4 -->
                <div class="form-step" id="form-step4">
                    <h6>Daily activity & Lifestyle</h6>
                    <p>How active are you?</p>
                   @for($i=0;$i<=10;$i++)
                        <div class="card_activity" onclick="selectCard(this,'{{$i}}')">
                            Walking
                        </div>
                   @endfor


                    <label for="full-name"><strong>Food Choice*</strong></label>
                    <div class="gender-selection">
                        <input type="radio" id="Veg" name="food" value="Veg" checked>
                        <label for="Veg">Veg</label>

                        <input type="radio" id="Non-Veg" name="food" value="Non-Veg">
                        <label for="Non-Veg">Non-Veg</label>
                        <input type="radio" id="Eggetarian" name="food" value="Eggetarian">
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
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            } else {
                document.getElementById('multi-step-form').submit(); // Replace this with actual form submission if needed
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });

        showStep(currentStep);
    </script>

    <script>
        function selectCard(element, value) {
            // Remove 'selected' class from all cards
            document.querySelectorAll('.card_activity').forEach(card => {
                card.classList.remove('selected');
            });

            // Add 'selected' class to the clicked card
            element.classList.add('selected');
            // Show selected value
        }
        function selectCard1(element, value) {
            // Remove 'selected' class from all cards
            document.querySelectorAll('.card_activity1').forEach(card => {
                card.classList.remove('selected');
            });

            // Add 'selected' class to the clicked card
            element.classList.add('selected');
            // Show selected value
        }
    </script>
@endsection
