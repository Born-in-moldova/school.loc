<?
require_once APP_PATH . 'app/core/mysql_driver.php';

class School extends MYSQL_Driver{

    function get_one_class($id){
        $this->table = 'classes';

        $params = array(
            'where' => array(
                'id_class' => $id
            ),
            'joins' => array(
                'teachers' => array(
                    'type' => 'LEFT JOIN',
                    'condition' => 'classes.id_mentor = teachers.id_teacher'
                )
            ),
            'columns' => 'classes.class_name, teachers.name, teachers.last_name, classes.id_class'
        );

        return $this->select_one($params);
    }

    function get_class_mentor($id_teacher){
        $this->table = 'classes';

        $params = array(
            'where' => array(
                'id_mentor' => $id_teacher
            ),
            'columns' => 'classes.class_name AS class_mentor',
            'limit' => 1
        );

        return $this->select_one($params);
    }

    function get_one_teacher($id){
        $this->table = 'teachers';

        $params['where']['id_teacher'] = $id;
        $params['columns'] = 'name, last_name, id_teacher';

        return $this->select_one($params);
    }

    function get_one_pupil($id){
        $this->table = 'pupils';

        $params = array(
            'columns' => 'pupils.id_pupil, pupils.name, pupils.last_name, pupils.date_birth, classes.id_class, classes.class_name, teachers.name AS mentor_name, teachers.last_name AS mentor_last_name',
            'where' => array(
                'id_pupil' => $id
            ),
            'joins' => array(
                'classes' => array(
                    'condition' => $this->table . '.id_class = classes.id_class',
                    'type' => 'LEFT JOIN'
                ),
                'teachers' => array(
                    'condition' => 'teachers.id_teacher = classes.id_mentor',
                    'type' => 'LEFT JOIN'
                )
            )

        );

        return $this->select_one($params);
    }

    function get_many_pupils(array $conditions = array()){
        $this->table = 'pupils';

        $params = array(
            'columns' => $this->table . '.name, ' . $this->table . '.last_name, ' . $this->table . '.date_birth, ' . $this->table . '.id_pupil'
        );

        //WHERE
        if(isset($conditions['where']))
            foreach($conditions['where'] as $column => $condition)
                $params['where'][$this->table . '.' . $column] = $condition;

        //Filters
        if(isset($conditions['search']))
            foreach($conditions['search'] as $key => $value)
                if($conditions['search'][$key]['column'] != 'date_birth'){
                    $params['where']['like'][$key]['column'] = $this->table . '.' . $conditions['search'][$key]['column'];
                    $params['where']['like'][$key]['filter'] = '%' . $conditions['search'][$key]['like'] . '%';
                }else
                    $params['where'][] = $this->table . '.' . $conditions['search'][$key]['column'] . ' > ' . mktime(0, 0, 0, 1, 1, $conditions['search'][$key]['like']) . ' AND ' . $this->table . '.' . $conditions['search'][$key]['column'] . ' < ' . mktime(0, 0, 0, 12, 31, $conditions['search'][$key]['like']);
        
        /* if(isset($conditions['like'])){
            foreach($conditions['like']['columns'] as $column)
                $params['where']['like']['columns'][] = $this->table . '.' . $column;
            
            $params['where']['like']['search'] = '%'. $conditions['like']['search'] . '%';
        } */

        //ORDER
        if(isset($conditions['order']))
            $params['order'] = $this->table . '.' . $conditions['order']['column'] . ' ' . $conditions['order']['dir'];

        //LIMIT
        if(isset($conditions['limit']))
            $params['limit'] = $conditions['limit']['start'] . ', ' . $conditions['limit']['length'];

        return $this->select_many($params);
    }

    function get_many_classes(array $conditions = array(), $special_set_columns = false){
        $this->table = 'classes';

        if($special_set_columns){
            $params['columns'] = 'class_name, id_class';
            $params['order'] = 'class_name asc';
            
            return $this->select_many($params);
        }

        $params = array(
            'columns' => $this->table . '.class_name, ' . $this->table .'.id_class, teachers.name, teachers.last_name, teachers.id_teacher',
            'joins' => array(
                'teachers' => array(
                    'condition' => $this->table . '.id_mentor = teachers.id_teacher',
                    'type' => 'LEFT JOIN'
                )
            ),
            'order' => 'classes.class_name asc'
        );

        //WHERE
        if(isset($conditions['where']))
            if(!is_array($conditions['where']))
                $params['where'][] = $this->table . '.' . $conditions['where'];
            else
                foreach($conditions['where'] as $column => $condition)
                    $params['where'][$this->table . '.' . $column] = $condition;
        //LIMIT
        if(isset($conditions['limit']))
            $params['limit'] = $conditions['limit']['start'] . ', ' . $conditions['limit']['length'];

        //die(print_r($params));
        return $this->select_many($params);
    }

    function get_pupil_lessons($id, $who = 'pupil'){
        $this->table = 'lessons';

        $params = array(
            'columns' => 'DISTINCT lesson',
            'where' => array (
                'id_class' => $id
            )
        );

        return $this->select_many($params);
    }

    function get_teacher_lessons($id){
        $this->table = 'lessons';

        $params = array(
            'columns' => 'lessons.lesson, classes.class_name',
            'joins' => array(
                'teachers' => array(
                    'condition' => 'teachers.id_teacher = lessons.id_teacher',
                    'type' => 'LEFT JOIN'
                ),
                'classes' => array(
                    'condition' => 'lessons.id_class = classes.id_class',
                    'type' => 'LEFT JOIN'
                )
            ),
            'order' => 'classes.class_name asc',
            'where' => array(
                'lessons.id_teacher' => $id
            )
        ); 
        

        return $this->select_many($params);
    }

    function get_candidates_for_mentor(){
        $this->table = 'teachers';

        $params = array(
            'columns' => 'teachers.id_teacher, teachers.name, teachers.last_name',
            'joins' => array(
                'classes' => array(
                    'type' => 'LEFT JOIN',
                    'condition' => 'classes.id_mentor = teachers.id_teacher'
                )
            ),
            'where' => array(
                'teachers.id_teacher NOT IN (SELECT classes.id_mentor FROM classes)'
            )
        );

        return $this->select_many($params);
    }

    function get_many_teachers(){
        $this->table = 'teachers';

        $params['columns'] = 'teachers.id_teacher, teachers.name, teachers.last_name';

        return $this->select_many($params);
    }

    function get_count(array $conditions = array()){
        $this->table = 'pupils';
        $params = array();
        //TABLE
        if(isset($conditions['table']))
            $this->table = $conditions['table'];

        //WHERE
        if(isset($conditions['where']))
                $params['where'] = $conditions['where'];

        //LIKE
        if(isset($conditions['like'])){
            foreach($conditions['like']['columns'] as $column)
                $params['where']['like']['columns'][] = $this->table . '.' . $column;
            
            $params['where']['like']['search'] = '%'. $conditions['like']['search'] . '%';
        }

        return $this->select_count($params);
    }

    function get_counts(array $conditions = array()){
        $this->table = 'pupils';

        $params = array(
            'columns' => 'classes.id_class, COUNT( * ) AS count_pupils',
            'joins' => array(
                'classes' => array(
                    'type' => 'LEFT JOIN',
                    'condition' => 'classes.id_class = pupils.id_class'
                )
            ),
            'group' => 'pupils.id_class'
            
        );


        return $this->select_many($params);
    }

    function remove_teacher($id){
        $this->table = 'teachers';

        $params['where']['id_teacher'] = $id;

        return $this->delete($params);
    }

    function change($entity, $conditions){
        $this->table = 'pupils';
        
        if(!isset($conditions['where']))
            return false;

        if(isset($conditions['table']))
            $this->table = $conditions['table'];

        return $this->update($entity, $conditions);
    }

    function remove_mentor($id_teacher){
        $this->table = 'classes';

        $changes['id_mentor'] = 0;

        $conditions['where']['id_mentor'] = $id_teacher;

        return $this->update($changes, $conditions);
    }
}
?>