<?
function display_404(){
    require_once APP_PATH . 'app/views/404.php';
    die();
}

function format_date($timestamp, $format = 'l j F Y'/*, $lang = 'eng'*/){
    /* if($lang == 'rus'){
        $monthes = array(
            1 => 'Января',
            2 => 'Февраля',
            3 => 'Марта',
            4 => 'Апреля',
            5 => 'Мая',
            6 => 'Июня',
            7 => 'Июля',
            8 => 'Августа',
            9 => 'Сентября',
            10 => 'Октября',
            11 => 'Ноября',
            12 => 'Декабря'
        );
    
        $days = array(
            'Воскресенье',
            'Понедельник',
            'Вторник',
            'Среда',
            'Четверг',
            'Пятница',
            'Суббота'
        );

        $date = $days[date('w', $timestamp)] . " "
                . date('j', $timestamp) . " "
                . $monthes[date('n', $timestamp)] . " "
                . date('Y', $timestamp);

        return $date;
    } */
    
    return date($format, $timestamp);
}

function datatables_column($index){
    $columns = array(
        'nr',
        'last_name',
        'name',
        'date_birth'
    );

    return $columns[$index];
}

function clean_text($text){
    return trim(mysql_real_escape_string(htmlspecialchars($text)));
}

function if_selected($option, $val){
    if($option == $val)
        return 'selected="selected"';
}

function count_pages($count, $per_page){
    if($count % $per_page == 0)
            $nr_pages = $count / $per_page;
        else 
            $nr_pages = intval($count / $per_page + 1);
    return $nr_pages;
}

function calculate_timestamp($timestamp, $ord = 0){

    $res = explode('-', $timestamp);

    return $res[$ord];
}

function json_response($mess, $status = false, $additional = array()){
    $response = array(
            'status' => $status,
            'message' => $mess,
        );
    
    //echo "<pre>".print_r($response, 1)."</pre";
    
    exit(json_encode(array_merge($response, $additional)));
}

function show_message($message){
    exit($message);
}