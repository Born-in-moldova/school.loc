<?

class MYSQL_Driver{

    private $connection;
    private $query;
    private $error = false;
    private $debug = false;
    private $columns;
    private $values;
        
    protected $table;
    protected $result;

    public function __construct(){
        $this->debug = DEBUG;
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if($this->connection->connect_error) 
            die($this->connection->connect_error);
    }

    private function execute(){
        if($this->debug)
            echo '<!--' . $this->last_query() . '-->';

        if(!$this->result = $this->connection->query($this->query)){
            $this->error = $this->connection->error;

            if($this->debug)
                die($this->error);

            return false;
        }

        return true;
    }

    private function joins($assoc){ 
        $joins = array();
        $type = " JOIN ";

        if(empty($assoc))
            return '';

        foreach($assoc as $table => $condition){
            if(is_array($condition)){
                $joins[] = " " . $condition['type'] . " " . $table . " ON " . $condition['condition'];
                continue;
            }

            $joins[] = $type . $table . " ON " . $condition;
        }

        return implode(' ', $joins);
    }

    private function like($assoc){
        foreach($assoc as $key => $value)
            if(is_array($value))
                $result[] = $value['column'] . ' LIKE \'' . $value['filter'] . '\'';
            else
                $result[] = $value;

        return '(' . implode(' AND ', $result) . ')';
    }

    private function where(array $assoc){
        if(empty($assoc))
            return '';

        $conditions = array();
        foreach($assoc as $key => $condition){
            if($key === 'like'){
                $conditions[] = $this->like($assoc['like']);
                continue;
            }
                
            if(is_int($key))
                $conditions[] = $condition;
            else
                $conditions[] = $key . ' = ' . $condition;
            
        }

        return ' WHERE ' . implode(' AND ', $conditions);
    }

    private function set(array $assoc){
        if(empty($assoc))
            return '';

        $columns = array();

        foreach($assoc as $column => $value)
            if(is_int($column))
                $columns[] = $value;
            else
                $columns[] = "`" . $column . "` = '" . $value . "'";

        return ' SET ' . implode(' , ', $columns);
    }
        
    protected function select_one(array $assoc){
        $columns = "*";
        $limit = 1;
        $table = $this->table;
        
        //SELECT
        if(isset($assoc['columns']))
            $columns = $assoc['columns']; 
        
        $this->query =  "SELECT " . $columns;
        
        //FROM
        if(isset($assoc['table']))
            $table = $assoc['table']; 
        
        $this->query .=  " FROM " . $table; 

        //JOINS
        if(isset($assoc['joins']))
            $this->query .= $this->joins($assoc['joins']);

        //WHERE
        if(isset($assoc['where']))
            $this->query .= $this->where($assoc['where']);

        //GROUP BY for select many

        //ORDER BY for select many

        //LIMIT
        //if($assoc['limit'] !== 'none')
            $this->query .= " LIMIT " . $limit;

        //die($this->query);

        if(!$this->execute())
            return false;
            
        //echo '<pre>' . print_r($this->result->fetch_assoc(), true)   . '</pre>';
        
        return $this->result->fetch_assoc();
    }

    protected function select_many(array $assoc){
        $columns = "*";
        $limit = 100;
        $table = $this->table;

        //SELECT
        if(isset($assoc['columns']))
            $columns = $assoc['columns']; 
        
        $this->query =  "SELECT " . $columns;
        
        //FROM
        if(isset($assoc['table']))
            $table = $assoc['table']; 
        
        $this->query .=  " FROM " . $table; 

        //JOINS
        if(isset($assoc['joins']))
            $this->query .= $this->joins($assoc['joins']);

        //WHERE
        if(isset($assoc['where']))
            $this->query .= $this->where($assoc['where']);

        //GROUP BY for select many
        if(isset($assoc['group']))
            $this->query .= ' GROUP BY '.$assoc['group'];

        //ORDER BY for select many
        if(isset($assoc['order']))
            $this->query .= ' ORDER BY '.$assoc['order'];

        //LIMIT
        if(isset($assoc['limit']))
            $limit = $assoc['limit'];

        $this->query .= " LIMIT " . $limit;
        
        //die($this->query);

        if(!$this->execute())
            return false;

        while ($row = $this->result->fetch_assoc())
            $data[] = $row;

        //echo '<pre>' . print_r($data, true)   . '</pre>';

        return $data;
    }

    protected function select_count(array $assoc){
        $table = $this->table;

        //SELECT
        $this->query = 'SELECT COUNT(*) as counter ';
        
        //FROM
        if(isset($assoc['table']))
            $table = $assoc['table'];

        $this->query .= 'FROM '.$table;

        //JOINS
        if(isset($assoc['joins']))
            $this->query .= $this->joins($assoc['joins']);

        //WHERE
        if(isset($assoc['where']))
            $this->query .= $this->where($assoc['where']);

        //GROUP BY for select many
        if(isset($assoc['group']))
            $this->query .= ' GROUP BY '.$assoc['group'];
        
        //die($this->query);

        if(!$this->execute())
            return false;

        $temp = $this->result->fetch_assoc();
    
        return $temp['counter'];
    }

    protected function insert(array $entity){
        $columns = "`" . implode("`,`" ,array_keys($entity)) . "`";
        $values = "'" . implode("','" ,array_values($entity)) . "'";  

        //INTO
        $this->query = "INSERT INTO " . $this->table;

        //COLUMNS
        $this->query .= " (" . $columns . ") ";

        //VALUES
        $this->query .= "VALUES (" . $values . ")";

        //die($this->query);

        if($this->execute())
            return $this->connection->insert_id;
        
        return false;
    }

    protected function delete(array $assoc){

        //TABLE
        if(isset($assoc['table']))
            $this->table = $assoc['table'];

        $this->query = "DELETE FROM `".$this->table."`";

        //WHERE
        if(!isset($assoc['where']))
            return false;

        $this->query .= $this->where($assoc['where']);

        //die($this->query);

        return $this->execute();  
    }

    protected function update(array $entity, array $conditions){
        
        //UPDATE TABLE
        $this->query = "UPDATE " . $this->table;

        //SET
        $this->query .= $this->set($entity);

        //WHERE    
        $this->query .= $this->where($conditions['where']);

        //die($this->query);

        return $this->execute();
    }
}

?>