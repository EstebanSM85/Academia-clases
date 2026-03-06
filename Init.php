<?php
namespace FacturaScripts\Plugins\EjemploHtmlView;

use FacturaScripts\Core\Template\InitClass;

class Init extends InitClass
{
    public function init() : void
    {
        // se ejecuta cada vez que carga FacturaScripts (si este plugin está activado).
    }

    public function update() :void
    {
        // se ejecuta cada vez que se instala o actualiza el plugin.
    }

    public function uninstall() : void
    {
        // se ejecuta cuando se desinstala el plugin.
    }
}