<div class="container" id="test" style="padding-top: 50px; text-align: Left">

    <a id="delete" href="/?a=delete_teacher&id=<?=$teacher[id_teacher]?>">
        <img src="public/images/remove_profil.png" width="50px" style="position: absolute; top: 0px; left: 0px">
    </a>

    <h2>
        <a class="button modal" data-fancybox data-type="ajax" data-src="/?a=edit_teacher&id=<?=$teacher[id_teacher]?>" href="javascript:;"><?=$teacher['last_name'] . ' ' . $teacher['name']?></a>
    </h2>

    <h3> Деятельность учителя:</h3>

    <?if(!empty($class_mentor)){?>
        <div id="mentor_info">
            <p> Классный руководитель  <?=$class_mentor['class_mentor']?> класса </p>
            <input type="button" id="remove_status_mentor" value="Снять с должности ментора">
            <hr>
        </div>

    <?}if(empty($activity)){ ?>

        <p> Данный Учитель пока не ведет никаких лекций</p>

    <? } else {?>

        <ul>

            <?foreach($activity as $lesson){?>
                <li style="width: 200px; margin:auto"> <?=$lesson['class_name'] . ' => ' . $lesson['lesson']?> </li>
            <? }?>

        </ul>

    <?}?>
    
</div>

<script>

    $(document).ready(function() {

        $('#delete').on('click', function(e){
            e.preventDefault();

            if(!confirm('Удалить?'))
                return false;

            $.ajax({
                url: $(this).attr('href'),
                dataType: 'JSON',

				success: function(response){
                    alert(response.message);
                    if(response.status)
                        $.fancybox.close( true );
                }
            })  
        });

        $('#remove_status_mentor').on('click', function(e){
            e.preventDefault();

            var id_teacher = <?=$teacher['id_teacher']?>;

            if(!confirm('Лишить должности классного руководителя?'))
                return false;

            $.ajax({
                url: '/?a=remove_status_mentor&id_teacher=' + id_teacher,
                dataType: 'JSON',

				success: function(response){
                    alert(response.message);
                    if(response.status)
                        $('#mentor_info').css("display","none");
                }
            })  
        });

           
    });
</script>