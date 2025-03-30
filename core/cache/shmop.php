<?php


namespace core\cache;

class shmop extends \core\root {

    /**
     * 'private' => [],        // идентификаторы объектов
     * 'public' => [],         // идентификаторы произвольных данных
     * 'size' => [
     *     'private' => [],    // участок пямяти для объектов
     *     'public' => []      // участок памяти для произвольных данных
     * ],
     * 'increment' => 0         // следующий идентификатор
     * 
     * @var array
     */

    private $map = [];          // Карта данных в памяти
    private $shmid;             // Последний открытый участок памяти
    private $mapid;             // ключ к памяти в которой хранится карта
    private $increment;         // Счетчик идентификаторов
    private $block = 16;        // первоначальный размер ячейки памяти в байтах

    protected $strctr = [];

    function __construct($strctr=[], $config=[]){
        parent::__construct($strctr, $config);
        if(extension_loaded('shmop')) {
            $this->map();
        }
    }

    function map() {
        $this->increment = ftok($_SERVER['SCRIPT_FILENAME'], '1');
        if(!$this->mapid) $this->mapid = $this->increment;
        $this->map = $this->read($this->mapid);
        if(isset($this->map['increment'])) $this->increment = $this->map['increment'];
    }

    function open($id=null, $size=0){
        return $this->shmid = shmop_open($id, 'c', 0600, $size);
    }

    function write($data=0, $id=null){

        $data = serialize($data);

        if($id) {
            if($this->exists($id)){
                $this->delete();
                $this->close();
            }
            $this->open($id, $this->size($data));
            shmop_write($this->shmid, $data, 0);
        }

    }

    function read($id = null){
        if($this->exists($id)) {
            return unserialize(shmop_read($this->shmid, 0, $this->size()));
        } else
            return NULL;
    }

    function size($data=null){
        if(is_string($data) || is_integer($data))
            return ceil(strlen($data) / $this->block) * $this->block;
        else
            return shmop_size($this->shmid);
    }

    function delete(){
        return shmop_delete($this->shmid);
    }

    function exists($id){
        return $this->shmid = @shmop_open($id, "a", 0, 0);
    }

    function close(){
        return shmop_close($this->shmid);
    }

    public function offsetSet($offset, $value) {
        if($value instanceof \structure) {
            if(!isset($this->map['private'][$offset]))
                $id = $this->map['private'][$offset] = ++$this->increment;
            else
                $id = $this->map['private'][$offset];
        } else {
            if (!isset($this->map['public'][$offset]))
                $id = $this->map['public'][$offset] = ++$this->increment;
            else
                $id = $this->map['public'][$offset];
        }

        $this->write($value, $id);
    }

    public function offsetGet($offset) {
        if (isset($this->map['public'][$offset])) {
            $id = $this->map['public'][$offset];
            return $this->read($id);
        } else return NULL;
    }

    public function remember($name){
        if(isset($this->map['private'][$name])) {

            $id = $this->map['private'][$name];

            if (!isset($this->current[$name]))
                return $this->current[$name] = $this->read($id);
            else
                return $this->current[$name];

        } else
            return NULL;
    }

    function __destruct(){
        $this->map['increment'] = $this->increment;
        $this->write($this->map, $this->mapid);
    }

}
