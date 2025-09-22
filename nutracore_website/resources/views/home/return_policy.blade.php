@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;


    ?>

    <main class="main">
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="row">
                    {!! CustomHelper::getSettings('refund_policy') !!}

                </div>
            </div>
        </section>
    </main>
@endsection
