<?php


namespace core;

/**
 * Class Events — обработчик событий, хранит карту и вызывает сохраненные методы для кластеров или юнитов
 *
 * @package Structure
 */

class events {

    /**
     * @var array — карта событий, события отрабатывающие по умолчанию для кластеров и юнитов:
     * add       — При добавлении юнита в кластер
     * remove    — При удалении юнита из кластера
     * reset     — Когда кластер перезагружен
     * sort      — Когда кластер отсортирован
     * change    — Когда юнит получил новое значения
     * create    — когда в источнике создан экзмляр юнита
     * udpate    — когда в источнике обновлен экземпляр юнита
     * delete    — когда в источнике удален экземпляр юнита
     * read      — когда из источника данных юнит прочтен
     * synch     — Когда юнит или кластер удачно синхронизирован с источником данных
     * error     — Когда запрос к источнику данных был не успешным
     * invalid   — Когда данные переданные юниту не прошли валидацию
     * all       — Когда происходит одно из выше перечисленных событий
     */

    private $map = [];

    /**
     * Регистрирует метод, который в дальнейшем по наступлению события будет вызван
     * через метод commit
     *
     * @param $event
     * @param $object
     * @param $method
     */

    function attach($event, $object, $method){
        $this->map[$event][$object->sid()][$method] = $object;

    }

    /**
     * Запускает ранее зарегистрированный метод при наступлении события
     *
     * @param $event
     * @param $object
     */

    function commit($event, $object){
        if(isset($this->map[$event][$object->sid()])) {
            foreach ($this->map[$event][$object->sid()] as $m => $o) {
                $o->$m();
            }
        }
    }

    /**
     * Удаляет ранее зарегистрированное событие
     *
     * @param      $event
     * @param null $object
     * @param null $method
     */

    function detach($event, $object=null, $method=null){
        if(isset($this->map[$event][$object->sid()]) && $object) {

            if ($method) unset($this->map[$event][$object->sid()][$method]);
            if ($object && !$method) unset($this->map[$event][$object->sid()]);
        }

        if(!$object && !$method) unset($this->map[$event]);

        if($object){
            if(!$this->map[$event][$object->sid()]) unset($this->map[$event][$object->sid()]);
        }
    }

    /**
     * Удаляет все ранее зарегистрированные события
     */

    function clear(){}

}