@foreach($chats as $chat)
        <?php
    if ($chat->sender_type == 'admin'){
        ?>
    <div class="message-item me">
        <div class="message-item-content">{{$chat->message??''}}
        </div>
        <span class="time small text-muted font-italic">{{date('d M Y h:i A',strtotime($chat->created_at))}}</span>
    </div>
    <?php }else{?>
    <div class="message-item">
        <div class="message-item-content">{{$chat->message??''}}</div>
        <span class="time small text-muted font-italic">{{date('d M Y h:i A',strtotime($chat->created_at))}}</span>
    </div>
    <?php } ?>
@endforeach



