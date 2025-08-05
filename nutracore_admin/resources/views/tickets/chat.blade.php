@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    ?>

    <div class="content ">

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            Name: {{\App\Helpers\CustomHelper::getUserDetails($tickets->user_id)->name??''   }}<br>
                            Type: {{$tickets->type??''}}<br>
                            Email: {{$tickets->email??''}}<br>
                            Subject: {{$tickets->subject??''}}<br>
                        </div>
                        <div class="col-md-4">
                            <label>Change Status</label>
                            <select class="form-control" name="" onchange="update_status(this.value)">
                                <option value="">Change Ticket Status</option>
                                <option value="2" {{$tickets->status == 2?"selected":""}}>OPEN</option>
                                <option value="3" {{$tickets->status == 3?"selected":""}}>RESOLVE</option>
                                <option value="4" {{$tickets->status == 4?"selected":""}}>CLOSE</option>
                                <option value="5" {{$tickets->status == 5?"selected":""}}>REOPEN</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="chat-block">
                            <!-- begin::chat content -->
                            <div class="chat-content chat-content chat-mobile-open">
                                <!-- begin::messages -->
                                <div class="messages" id="messages">

                                </div>
                                <!-- end::messages -->
                                <!-- begin::chat footer -->
                                <div class="chat-footer">
                                    <form class="d-flex">
                                        {{--                                        <div class="dropdown flex-shrink-0 me-3">--}}
                                        {{--                                            <button class="btn btn-primary btn-rounded" type="button"--}}
                                        {{--                                                    data-bs-toggle="dropdown">--}}
                                        {{--                                                <i class="bi bi-three-dots"></i>--}}
                                        {{--                                            </button>--}}
                                        {{--                                            <div class="dropdown-menu">--}}
                                        {{--                                                <a href="#" class="dropdown-item">Add Emoji</a>--}}
                                        {{--                                                <a href="#" class="dropdown-item">Attach files</a>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        <input type="text" class="form-control" autofocus=""
                                               placeholder="Write message..." id="message">
                                        <button class="btn btn-primary btn-rounded flex-shrink-0 ms-3"
                                                onclick="submit_chat()">Send
                                        </button>
                                    </form>
                                </div>
                                <!-- end::chat footer -->

                            </div>
                            <!-- begin::chat content -->

                        </div>
                        <!-- end::chat sidebar -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    <script>

        function update_status(value) {
            if (confirm("Are You Want to Change The Status")) {
                var ticket_id = "{{$tickets->id??''}}";
                var _token = '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('support_tickets.update_status') }}",
                    type: "POST",
                    data: {ticket_id: ticket_id, status: value},
                    dataType: "HTML",
                    headers: {'X-CSRF-TOKEN': _token},
                    cache: false,
                    success: function (resp) {
                        alert('Updated');
                    }
                });
            }
        }

        function submit_chat() {
            var message = $('#message').val();
            var ticket_id = "{{$tickets->id??''}}";
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('support_tickets.submit_chat') }}",
                type: "POST",
                data: {ticket_id: ticket_id, message: message},
                dataType: "HTML",
                headers: {'X-CSRF-TOKEN': _token},
                cache: false,
                success: function (resp) {
                    $('#message').val('');
                    getChats();
                }
            });
        }

        $(document).ready(function () {
            getChats();
            setInterval(getChats, 5000);
            //
        });

        function getChats() {
            var _token = '{{ csrf_token() }}';
            var ticket_id = "{{$tickets->id??''}}";
            $.ajax({
                url: "{{ route('support_tickets.get_chats') }}",
                type: "POST",
                data: {ticket_id: ticket_id},
                dataType: "HTML",
                headers: {'X-CSRF-TOKEN': _token},
                cache: false,
                success: function (resp) {
                    $('#messages').html(resp);
                }
            });
        }


    </script>

@endsection
