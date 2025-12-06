@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;


    ?>
    <style>
        .no-tailwind * {
            all: revert;
        }
    </style>
    <main class="main">
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Privacy Policy</h3>
                    </div>
                </div>
                <div class="row no-tailwind">
                   {!! CustomHelper::getSettings('privacy_policy') !!}

                </div>
            </div>
        </section>
    </main>
@endsection
