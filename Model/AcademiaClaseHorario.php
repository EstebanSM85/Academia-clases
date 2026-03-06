<?php
namespace FacturaScripts\Plugins\EjemploHtmlView\Model;

use FacturaScripts\Core\Model\Base\ModelClass;
use FacturaScripts\Core\Model\Base\ModelTrait;
use FacturaScripts\Core\Session;

class AcademiaClaseHorario extends ModelClass
{
    use ModelTrait;

    /** @var string */
    public $creationdate;

    /** @var int */
    public $day;

    /** @var string */
    public $hourend;

    /** @var string */
    public $hourstart;

    /** @var int */
    public $id;

    /** @var int */
    public $idclase;

    /** @var string */
    public $lastnick;

    /** @var string */
    public $lastupdate;

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

    public static function primaryColumn(): string
    {
        return "id";
    }

    public static function tableName(): string
    {
        return "academias_clases_horarios";
    }

    protected function saveUpdate(array $values = [])
    {
        $this->lastupdate = date(self::DATETIME_STYLE);
        $this->lastnick = Session::get('user')->nick ?? null;
        return parent::saveUpdate($values);
    }
}
