@if ($errors->has($param))
    <p class="help-block" style="color:red;">
        {{ $errors->first($param) }}
    </p>
@endif