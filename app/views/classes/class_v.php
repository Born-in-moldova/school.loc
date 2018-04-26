<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<!-- <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script> -->
<script src="/public/js/jquery.dataTables.min.js"></script>
<script src="/public/js/jquery.dtFilters.js"></script>
<div id="container">
    <div class="filters">
        <p>
            <label>Поиск по имени</label>
            <input type="text" class="dt-filter" name="name" data-title="Name">
        </p>
        <p>
            <label>Поиск по фамилии</label>
            <input type="text" class="dt-filter" name="last_name" data-title="Surname">
        </p>
        <p>
            <label>Поиск по году рождения</label>
            <select name="date_birth" class="dt-filter" data-title="year of birth">
                <option value="">Укажите год</option>
                <?
                    for($i=2018; $i>1950; $i--){
                ?>
                <option value="<?=$i?>"><?=$i?></option>
                <?
                    }
                ?>
            </select>
        </p>
    </div>
    <div class="dt-filter-container"></div>
    <div id="class_info">
        <h1>Класс : <?=$class_info['class_name']?></h1>
        <h2>Руководитель класса : <span id="teacher"><a class="button modal" style="text-decoration : none; color: black" data-fancybox data-type="ajax" data-src="/?a=select_mentor&id=<?=$class_info[id_class]?>" href="javascript:;"><?= $class_info['last_name'] == NULL ? 'Назначить классного руководителя' : $class_info['last_name'] . ' ' . $class_info['name'] ?></a></span></h2>
    </div>
    <table id="list_of_pupils" style="text-align: center">
        <thead>
            <tr class="th">
                <th style="width: 5%">Порядковый номер</th>
                <th style="width: 30%">Фамилия</th>
                <th style="width: 30%">Имя</th>
                <th style="width: 30%">Дата рождения</th>
                <th style="width: 5%">Редактирование</th>
            </tr>
        </thead>
        </tfoot>
            <tr class="th">
                <th>Порядковый номер</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Дата рождения</th>
                <th>Редактирование</th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    var dt_filters
    var pupils
$(document).ready(function() {

    pupils = $('#list_of_pupils').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        //"searching": false,
        "columns": [
            { "data": "nr" },
            { "data": "last_name" },
            { "data": "name" },
            { "data": "date_birth" },
            { "data": "edit" }
        ],
        "order": [ 1, 'asc' ],
        "columnDefs": [
            {"orderable": false, "targets": 0},
            {"orderable": false, "targets": 2},
            {"orderable": false, "targets": 4}
        ],
        "language": {
            "zeroRecords": "Упс... тут ничего нет) поищите в другом месте",
            "lengthMenu": "Показать _MENU_ учеников на странице",
            "info": "Страница _PAGE_ из _PAGES_",
            "infoEmpty": "Поиск не дал результатов",
            "infoFiltered": "(Всего _MAX_ ученика в классе)"
        },
        "lengthMenu": [50, 1, 2, 5, 10, 25],
        "ajax":  function (data, callback, settings) {
					if (!dt_filters) {
                        
						dt_filters = $('.dt-filter').dtFilters({
							'container': '.dt-filter-container',
							'autoApply': true,
							callBack: function () {
                               
								pupils.draw();
							}

						});
					}

					data.filter = dt_filters.getDTFilter();
					$.ajax({
						"dataType": 'JSON',
						"type": "POST",
						"data": data,
						"url": "/?a=datatables&id=<?=$id_class?>",
						"success": function (data, textStatus, jqXHR) {
                            
							callback(data, textStatus, jqXHR);
						}
					});
		}
        /*{
            url: "/?a=datatables&id=" + <?=$id_class?>,
            type: 'POST'
        }*/
    });    
});
</script>