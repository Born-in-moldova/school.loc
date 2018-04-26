<!-- connect FancyBox -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
<div>
    <p>
        Количество классов на странице
    </p>
    <select onchange="window.location.href=this.options[this.selectedIndex].value" style="width: 100px">
        <option VALUE="/" <?=if_selected($length, 5)?>>5</option>
        <option VALUE="/?pp=10" <?=if_selected($length, 10)?>>10</option>
        <option VALUE="/?pp=25" <?=if_selected($length, 25)?>>25</option>
    </select>
</div>
<div class="container" style="width: 500px; margin: 50px auto; text-align: center;">
    <?
        foreach($list_classes as $class){
            $cnt = 0;
    ?>
        <div>
            <a href="/?a=class&id=<?=$class['id_class']?>">
                Класс:  <?=$class['class_name']?>
            </a>
        </div>

        <div style="margin: 5px 0 10px 30px">
            Классный руководитель: <?= empty($class['last_name']) ? "Не назначен" : '<a href="/?a=all_teachers">' . $class['last_name'] . ' ' . $class['name'] . '</a>'?>
        </div>

        <div>
            Количество учеников в классе: 
            <?
            foreach($count_pupils as $count_class){
                if($count_class['id_class'] == $class['id_class'])
                    $cnt = $count_class['count_pupils'];
            }
                echo $cnt;
            ?>
        </div>
        <hr>
    <?
        }
    ?>
</div>
<?
    require_once APP_PATH . 'app/views/pagination_v.php';
?>