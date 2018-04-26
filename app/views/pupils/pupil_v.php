<div class="container">
    <h1 style="text-align: center"><?=$pupil['last_name'] . ' ' . $pupil['name']?> <span style="font: italic small-caps 100 16px Tahoma"><?=format_date($pupil['date_birth'])?></span></h1>
    <hr>
    <div>
        <p>
            Класс: <?=$pupil['class_name']?>
        </p>
        <p>
            Классый руководитель: <?=$pupil['mentor_last_name'] . ' ' . $pupil['mentor_name']?>
        </p>
        <p>
            Изучает: <?
                        if(empty($lessons))
                            echo "Класс пока не посещает никакие курсы";
                        else{
                            foreach($lessons as $key => $lesson)
                                $storage[] = $lesson['lesson'];

                            $list_of_lessons = implode(', ', $storage);
                            echo $list_of_lessons;
                        }
                    ?>
        </p>
    </div>
</div>