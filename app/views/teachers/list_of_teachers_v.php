<div class="container">
    <h2 style="text-align: center; padding: 30px 0">
        Список учителей нашей школы
    </h2>
    <?
        foreach($teachers as $teacher){
    ?>

        <div style="text-align: center; width: 300px; margin: auto">
            <a class="button modal" data-fancybox data-type="ajax" data-src="/?a=teacher&id=<?=$teacher[id_teacher]?>" href="javascript:;">
                <?=$teacher['last_name'] . ' ' . $teacher['name']?>
            </a>
            <hr>
        </div>
        
    <?}?>
</div>