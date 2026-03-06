<?php
namespace FacturaScripts\Plugins\EjemploHtmlView\Model;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Model\Base\ModelClass;
use FacturaScripts\Core\Model\Base\ModelTrait;
use FacturaScripts\Core\Session;

class AcademiaClase extends ModelClass
{
    use ModelTrait;

    /** @var string */
    public $creationdate;

    /** @var int */
    public $id;

    /** @var string */
    public $instructor;

    /** @var string */
    public $lastnick;

    /** @var string */
    public $lastupdate;

    /** @var string */
    public $name;

    /** @var string */
    public $nick;

    public function clear() 
    {
        parent::clear();
        $this->creationdate = date(self::DATETIME_STYLE);
        $this->lastupdate = date(self::DATETIME_STYLE);
        $this->nick = Session::get('user')->nick ?? null;
        $this->lastnick = Session::get('user')->nick ?? null;
    }

    public function getSchedule(): array
    {
        $hours = new AcademiaClaseHorario();
        $where = [new DataBaseWhere('idclase', $this->id)];
        return $hours->all($where, ['day' => 'ASC'], 0, 0);
    }

    public static function primaryColumn(): string
    {
        return "id";
    }

    public static function tableName(): string
    {
        return "academias_clases";
    }

    protected function saveUpdate(array $values = [])
    {
        $this->lastupdate = date(self::DATETIME_STYLE);
        $this->lastnick = Session::get('user')->nick ?? null;
        return parent::saveUpdate($values);
    }
}
