<?php
//use DB;
// use Str;
//$chatMsgLimit = 5;
use Illuminate\Support\Str;


function chatMsgLimit()
{
    return 2;
}

function pr($data = array())
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';

}

function prd($data = array())
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die;

}

function echo_die($string = array())
{
    echo $string;
    die;

}

function generateToken($num)
{
    // return str_random($num);
    return Str::random($num);

}

/* Common function */
function logo(): string
{
    return url('public/assets/images/logo.png');
}
function logo_login(): string
{
    return url('public/assets/images/logo_login.svg');
}
function favicon(): string
{
    return url('public/assets/images/default.png');
}
function editsvg(): string
{
    return '<img src=' . url('public/assets/images/edit_new.svg') . ' style="height:25px;width:25px;">';
}
function bellsvg(): string
{
    return '<img src=' . url('public/assets/images/bell.svg') . ' style="height:25px;width:25px;">';
}

function deletesvg(): string
{
    return '<img src=' . url('public/assets/images/trash_new.svg') . ' style="height:25px;width:25px;">';
}

function viewsvg(): string
{
    return '<img src=' . url('public/assets/images/view_new.svg') . ' style="height:25px;width:25px;">';
}
function invoicesvg(): string
{
    return '<img src=' . url('public/assets/images/invoice.svg') . ' style="height:25px;width:25px;">';
}

function getStatus($status = 0): string
{
    $checked1 = $status == 1 ? 'checked' : '';
    $checked0 = $status == 0 ? 'checked' : '';
    $html = '<div class="col-md-4 mt-3"><label for="validationCustom01" class="form-label">Status</label><br>
<div class="form-check form-check-primary form-check-inline">
    <input class="form-check-input" type="radio" name="status" value="1" ' . $checked1 . ' id="form-check-radio-primary">
    <label class="form-check-label" for="form-check-radio-primary">
        Active
    </label>
</div>

<div class="form-check form-check-info form-check-inline">
    <input class="form-check-input" type="radio" value="0" ' . $checked0 . ' name="status" id="form-check-radio-info">
    <label class="form-check-label" for="form-check-radio-info">
        InActive
    </label>
</div>
        </div>';


    return $html;
}

function getImageHtml($image = ''): string
{
    $html = '';
    if (!empty($image)) {
        $html = '<a href=' . $image . ' target="_blank"><img src=' . $image . ' style="height:50px;width:50px;"></a>';
    } else {
        $html = '<a href=' . url('public/assets/images/no_image.png') . ' target="_blank"><img src=' . url('public/assets/images/no_image.png') . ' style="height:50px;width:50px;"><a>';
    }
    return $html;
}

function getStatusHtml($status = 0): string
{
    $html = '';
    if ($status == 0) {
        $html = '<span class="shadow-none badge badge-danger">InActive</span>';
    }
    if ($status == 1) {
        $html = '<span class="shadow-none badge badge-primary">Active</span>';
    }
    return $html;
}

function randomNumber($qtd)
{
    $Caracteres = '0123456789';
    $QuantidadeCaracteres = strlen($Caracteres);
    $QuantidadeCaracteres--;

    $ransom_num = NULL;
    for ($x = 1; $x <= $qtd; $x++) {
        $Posicao = rand(0, $QuantidadeCaracteres);
        $ransom_num .= substr($Caracteres, $Posicao, 1);
    }

    return $ransom_num;
}


function dateTimeInterval($data)
{
    $result = '';
    $datetime1 = new DateTime($data);
    $datetime2 = new DateTime();
    $interval = $datetime1->diff($datetime2);

    //prd($interval);

    $year = $interval->format('%y');
    $month = $interval->format('%m');
    $days = $interval->format('%d');
    $hours = $interval->format('%h');
    $minutes = $interval->format('%i');
    $seconds = $interval->format('%s');


    if ($year > 0) {
        $result = $year . ' Year';
        if ($year > 1) {
            $result = $result . 's';
        }
    } elseif ($month > 0) {
        $result = $month . ' Month';
        if ($month > 1) {
            $result = $result . 's';
        }
    } elseif ($days > 0) {
        $result = $days . ' Day';
        if ($days > 1) {
            $result = $result . 's';
        }
    } elseif ($hours > 0) {
        $result = $hours . ' Hour';
        if ($hours > 1) {
            $result = $result . 's';
        }
    } elseif ($minutes > 0) {
        $result = $minutes . ' Minute';
        if ($result > 1) {
            $result = $result . 's';
        }
    } elseif ($seconds > 0) {
        $result = $seconds . ' Second';
        if ($seconds > 1) {
            $result = $result . 's';
        }
    }

    return $result;
}

function slugify($text)
{
    // echo $text; die;
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);
    // echo $text; die;
    if (empty ($text)) {
        // return 'n-a';
    }
    // echo $text; die;
    return $text;
}


/* End - Common function */

?>
