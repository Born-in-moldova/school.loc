<?

class School_C{
    public function index(){ 
        $page = 1;
        $start = 0;
        $length = 5;

        if(isset($_GET['pp']))
            $length = intval($_GET['pp']);

        if(isset($_GET['page'])){
            $page = intval($_GET['page']);
            $start = ($page - 1) * $length;
        }
        
        $params = array(
            'limit' => array(
                'start' => $start,
                'length' => $length
            ),
            'table' => 'classes'
        );

        //get count classes
        $object_school = new School;
        $count_classes = $object_school->get_count($params);
        
        if(empty($count_classes)){
            echo "Школа еще не имеет не одного класса"; // временно
            break;
        }

        $cnt_pages = count_pages($count_classes, $length);  
        
        //get data
        $list_classes  = $object_school->get_many_classes($params);

        //get count pupils
        $count_pupils = $object_school->get_counts();

        //load views
        require_once APP_PATH . 'app/views/header_v.php';
        require_once APP_PATH . 'app/views/classes/classes_v.php';       
        require_once APP_PATH . 'app/views/footer_v.php';
    }
}
switch($action){
    case 'index' : 
        $page = 1;
        $start = 0;
        $length = 5;

        if(isset($_GET['pp']))
            $length = intval($_GET['pp']);

        if(isset($_GET['page'])){
            $page = intval($_GET['page']);
            $start = ($page - 1) * $length;
        }
        
        $params = array(
            'limit' => array(
                'start' => $start,
                'length' => $length
            ),
            'table' => 'classes'
        );

        //get count classes
        $object_school = new School;
        $count_classes = $object_school->get_count($params);
        
        if(empty($count_classes)){
            echo "Школа еще не имеет не одного класса"; // временно
            break;
        }

        $cnt_pages = count_pages($count_classes, $length);  
        
        //get data
        $list_classes  = $object_school->get_many_classes($params);

        //get count pupils
        $count_pupils = $object_school->get_counts();

        //load views
        require_once APP_PATH . 'app/views/header_v.php';
        require_once APP_PATH . 'app/views/classes/classes_v.php';       
        require_once APP_PATH . 'app/views/footer_v.php';

        break;

    case 'class' :
        if(!isset($_GET['id']))
            display_404();

        $id_class = intval($_GET['id']);

        //get data
        $object_school = new School;
        $class_info = $object_school->get_one_class($id_class);

        if(empty($class_info))
            display_404(); //скорее всего не правильный id

        //load views
        require_once APP_PATH . 'app/views/header_v.php';
        require_once APP_PATH . 'app/views/classes/class_v.php';        
        require_once APP_PATH . 'app/views/footer_v.php';
            
        break;

    case 'datatables' :

        if(!isset($_GET['id']))
            display_404();

        $id = intval($_GET['id']); 

        $params_many['where']['id_class'] = $id;
        
        //params for recordsTotal
        $params_count['where']['id_class'] = $id;

        //search
        if(isset($_POST['filter']))
            foreach($_POST['filter'] as $key => $value){
                
                $params_many['search'][$key]['column'] = clean_text($_POST['filter'][$key]['name']);

                if($_POST['filter'][$key]['name'] != 'date_birth')
                    $params_many['search'][$key]['like'] = clean_text($_POST['filter'][$key]['value']);
                else
                    $params_many['search'][$key]['like'] = intval($_POST['filter'][$key]['value']); 
            }
        
        /* if(!empty($_POST['search']['value']))
            for($i=0; $i <= 3; $i++)
                if($_POST['columns'][$i]['searchable'] == 'true')
                    $searchable_columns[] = $i;
        
        //проверяем есть ли активные поля для поиска
        if(!empty($searchable_columns)){
            //если поля есть тогда записываем их названия
            foreach($searchable_columns as $column)
                $params_many['like']['columns'][] = datatables_column($column);
            
            //обрабатываем значение поиска
            $params_many['like']['search'] = clean_text($_POST['search']['value']);
        } */

        //order
        $params_many['order']['column'] = datatables_column(intval($_POST['order']['0']['column']));
        $params_many['order']['dir'] = clean_text($_POST['order']['0']['dir']);

        //LIMIT for pagination datatables
        $params_many['limit']['start'] = intval($_POST['start']);
        $params_many['limit']['length'] = intval($_POST['length']);

        //die(print_r($params));
        //get data
        $object_school = new School;
        $list_pupils  = $object_school->get_many_pupils($params_many);

        $output['draw'] = intval($_POST['draw']);
        $output['recordsFiltered'] = empty($list_pupils) ? 0 : $object_school->get_count($params_many);
        $output['recordsTotal'] = $object_school->get_count($params_count);
        $output['data'] = array();

        if(!empty($list_pupils)){
            $i = 1;

            foreach($list_pupils as $key =>$pupil){
                $list_pupils[$key]['nr'] = intval($_POST['start']) + $i++;
                $list_pupils[$key]['date_birth'] = format_date($list_pupils[$key]['date_birth']);
                $list_pupils[$key]['name'] = '<a class="button modal" style="text-decoration : none; color: black" data-fancybox data-type="ajax" data-src="/?a=pupil&id=' . $list_pupils[$key]['id_pupil'] . '" href="javascript:;">' . $list_pupils[$key]['name'] . '</a>';
                $list_pupils[$key]['last_name'] = '<a class="button modal" style="text-decoration : none; color: black" data-fancybox data-type="ajax" data-src="/?a=pupil&id=' . $list_pupils[$key]['id_pupil'] . '" href="javascript:;">' . $list_pupils[$key]['last_name'] . '</a>';
                $list_pupils[$key]['edit'] = '<a class="button modal" style="text-decoration : none; color: black" data-fancybox data-type="ajax" data-src="/?a=edit_pupil&id=' . $list_pupils[$key]['id_pupil'] . '" href="javascript:;"> Редактирование </a>';
            }
           
            $output['data'] = $list_pupils;
        }

        echo json_encode($output);
        
        break;

    case 'teacher' :
        
        if(!isset($_GET['id']))
            die("Учитель нейзвестен...");
        
        $id_teacher = intval($_GET['id']);

        $params['where'] = array(
            'id_teacher' => $id_teacher
        );

        //get data
        $object_school = new School;
        $teacher  = $object_school->get_one_teacher($id_teacher);

        //check data
        if(empty($teacher))
            die('Учитель с указанным ID не числится в этой школе');
        
        //get teacher activities
        $activity = $object_school->get_teacher_lessons($id_teacher);
        
        //check is mentor
        $class_mentor = $object_school->get_class_mentor($id_teacher);

        //load views
        require_once APP_PATH . 'app/views/teachers/teacher_v.php';
        
        break;

    case 'pupil' :

        if(!isset($_GET['id']))
            die("Ученик нейзвестен...");
        
        $id_pupil = intval($_GET['id']);

        //get data
        $object_school = new School;
        $pupil  = $object_school->get_one_pupil($id);

        //check data
        if(empty($pupil))
            die('Ученик с указанным ID не числится в этой школе');

        $lessons = $object_school->get_pupil_lessons($pupil['id_class']);

        //load views
        require_once APP_PATH . 'app/views/pupils/pupil_v.php';

        break;
        
    case 'edit_pupil' :
        $params = array();

        $id_pupil = intval($_GET['id']);

        //get data
        $object_school = new School;
        $pupil  = $object_school->get_one_pupil($id_pupil);

        //get classes
        $classes = $object_school->get_many_classes($params, true);

        //load views
        require_once APP_PATH . 'app/views/pupils/edit_pupil_v.php';

        break;

    case 'execute' :
        
        if (!isset($_POST['name'])){//check if is ajax
            echo json_response('Так так так.... Чего-то не хватает..');
            break;
        }
  
        //validation
        require_once APP_PATH . 'app/validator.php';

        $validator = new Validator();

        $validator->set_error_delimiters("","");

        //Set validation rules

        $rules = array(
            array(
                'field' => 'name',
                'rules' => array(
                    'required' => 'Имя ученика обязательно для заполнения'
                    )
                ),
            array(
                'field' => 'surname',
                'rules' => array(
                        'required' => 'Фамилия ученика обязательна для заполнения'
                    )
                ),    
            array(
                'field' => 'birthday',
                'rules' => array(
                        'required' => 'Вы не указали дату рождения ученика'
                    )
                )  
        );

        $validator->set_rules($rules);

        //Запускаем валидацию POST данных
        if(!$validator->run()){
            //Получаем сообщения об ошибках в виде строки
            $str_errors = $validator->get_string_errors();

            //Получаем сообщения об ошибках в виде массива
            $array_errors = $validator->get_array_errors();

            echo json_response($str_errors, false);
            break;
        }else {
            $month = calculate_timestamp($_POST['birthday'], 1);
            $day = calculate_timestamp($_POST['birthday'], 2);
            $year = calculate_timestamp($_POST['birthday'], 0);

            $pupil_description = array(
                "name" => clean_text($_POST['name']),
                "last_name" => clean_text($_POST['surname']),
                "date_birth" => mktime(0, 0, 0, $month, $day, $year),
                "id_class" => intval($_POST['class'])
            );

            $object_school = new School;

            $conditions['where']['id_pupil'] = intval($_GET['id']);

            if($object_school->change($pupil_description, $conditions))
                echo json_response('Профиль ученика успешно обновлен');
            else
                echo json_response('Не получилось сохранить внесенные изменения', false);

            break;
        }

    case 'select_mentor' :
        
        if(!isset($_GET['id']))
            die('Не известен класс, которому вы пытаетесь изменить ментора');

        $id_class = intval($_GET['id']);

        //get data
        $object_school = new School;
        $teachers  = $object_school->get_candidates_for_mentor();

        //load views
        require_once APP_PATH . 'app/views/teachers/select_mentor_v.php';
        break;

    case 'change_mentor' :
        if(!isset($_GET['id_class'])){
            echo json_response('Не известен класс, которому вы пытаетесь изменить ментора', true);
            break;
        }

        if(!isset($_POST['new_mentor'])){
            echo json_response('В отправки формы произошла ошибка..');
            break;
        }

        $conditions_change['where']['id_class'] = intval($_GET['id_class']);
        $conditions_change['table'] = 'classes';
    
        $class_description['id_mentor'] = intval($_POST['new_mentor']);

        $object_school = new School;
        $teacher  = $object_school->get_one_teacher($class_description['id_mentor']);

        if(empty($teacher))
            display_404();

        if($object_school->change($class_description, $conditions_change)){
            //get one
            $additional = array(
                'teacher' => '<a class="button modal" style="text-decoration : none; color: black" data-fancybox data-type="ajax" data-src="/?a=select_mentor&id=' . $class_description['id_mentor'] . '" href="javascript:;">' . $teacher['last_name'] . ' ' .  $teacher['name'] . '</a>'
            );
            echo json_response('Классный руководитель успешно изменен',true, $additional);
        }else
            echo json_response('По неизвестным причинам возникли ошибки в процессе изменения классного руководителя');

        break;

    case 'edit_teacher' :
        
        if(!isset($_GET['id']))
            die("Не возможно определить выбранного вами учителя");

        $id = intval($_GET['id']);

        //get data
        $object_school = new School;
        $teacher  = $object_school->get_one_teacher($id);

        //check data
        if(empty($teacher))
            display_404(); //не правильный id

        //load views
        require_once APP_PATH . 'app/views/teachers/edit_teacher_v.php';
        break;
    
    case 'change_teacher' :
        
        if (!isset($_POST['name'])){//check if is ajax
            echo json_response('Так так так.... Чего-то не хватает..');
            break;
        }
            
        //validation
        require_once APP_PATH . 'app/validator.php';

        $validator = new Validator();

        $validator->set_error_delimiters("","");

        //Set validation rules

        $rules = array(
            array(
                'field' => 'name',
                'rules' => array(
                    'required' => 'Имя учителя обязательно для заполнения'
                    )
                ),
            array(
                'field' => 'last_name',
                'rules' => array(
                        'required' => 'Фамилия учителя обязательно для заполнения'
                    )
                ) 
        );

        $validator->set_rules($rules);

        //Запускаем валидацию POST данных
        if(!$validator->run()){
            //Получаем сообщения об ошибках в виде строки
            $str_errors = $validator->get_string_errors();

            //Получаем сообщения об ошибках в виде массива
            $array_errors = $validator->get_array_errors();

            echo json_response($str_errors);

            break;
        }else {

            $teacher_description = array(
                "name" => clean_text($_POST['name']),
                "last_name" => clean_text($_POST['last_name'])
            );

            $object_school = new School;

            $conditions_change['where']['id_teacher'] = intval($_GET['id']);
            $conditions_change['table'] = 'teachers';

            if($object_school->change($teacher_description, $conditions_change))
                echo json_response('Профиль учителя успешно обновлен', true);
            else
                echo json_response('Не получилось сохранить внесенные изменения');

            break;
        }

    case 'delete_teacher' :
        if(!isset($_GET['id'])){
            echo json_response('Учитель с таким ID не числится в данной школе', true);
            break;
        }

        $id_teacher = intval($_GET['id']);

        $object_school = new School;

        if ($object_school->remove_teacher($id_teacher))
            echo json_response('Учитель уволен', true); 
        else
            echo json_response('Для увольнения учителя, необходимо сначала снять с него должность ментора'); 
        
        break;
    
    case 'all_teachers' :
        $params = array();

        $object_school = new School;
        
        $teachers  = $object_school->get_many_teachers($params);

        if(empty($teachers)){
            echo "Введется набор учителей..."; // временно
            break;
        }

        //load views
        require_once APP_PATH . 'app/views/header_v.php';
        require_once APP_PATH . 'app/views/teachers/list_of_teachers_v.php';       
        require_once APP_PATH . 'app/views/footer_v.php';
    
        break;

    case 'remove_status_mentor' :
        if (!isset($_GET['id_teacher'])){//check if is ajax
            echo json_response('Доступ запрещен!');
            break;
        }

        $id_teacher = intval($_GET['id_teacher']);

        $object_school = new School;

        if($object_school->remove_mentor($id_teacher))
            echo json_response('Должность ментора снята', true);
        else
            echo json_response('Должность ментора не была снята');
        break;

    default:  
        display_404();

        break;
}