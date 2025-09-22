@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;


    ?>

    <main class="main">
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Privacy Policy</h3>
                    </div>
                </div>
                <div class="row">
                   {!! CustomHelper::getSettings('privacy_policy') !!}

                </div>
            </div>
        </section>
    </main>
@endsection
