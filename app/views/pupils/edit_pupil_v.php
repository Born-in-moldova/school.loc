<div class="container" style="width: 400px; padding: 60px">
	<form method="POST" action="/?a=execute<?=isset($pupil) ? '&id=' . $pupil['id_pupil'] : ''?>" id="edit_pupil">
        <table style="text-align: left">
            <tr>
                <td width="50%">
                    <label for="name">Имя</label>
                </td>
                <td width="50%">
                    <input type="text" id="name" name="name" value="<?=$pupil[name]?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="surname">Фамилия</label>
                </td>
                <td>
                    <input type="text" id="surname" name="surname" value="<?=$pupil[last_name]?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="birthday">Дата рождения</label>
                </td>
                <td>
                    <input type="date" id="birthday" name="birthday" style="padding: 0 15px" value="<?=format_date($pupil[date_birth], 'Y-m-d')?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="class">Класс</label>
                </td>
                <td>
                    <select name="class" style="width: 175px;">
                        <?
						if(isset($pupil))
							foreach ($classes as $class){
						?>
							<option value="<?=$class[id_class]?>" <?=if_selected($pupil['id_class'], $class['id_class'])?>><?=$class['class_name']?></option>
						<?
							}
						else
							foreach ($authors as $author){
						?>
							<option value="<?=$class[id_class]?>"><?=$class['class_name']?></option>
						<?
						}
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Подтвердить">
                </td>
            </tr>
        </table>
	</form>
</div>
<?
	require_once APP_PATH.'public/js/create.js';
?>