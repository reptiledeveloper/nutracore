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
                <div class="row no-tailwind">
                    {!! CustomHelper::getSettings('refund_policy') !!}

                </div>
            </div>
        </section>
    </main>
@endsection
