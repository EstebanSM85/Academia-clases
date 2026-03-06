<?php
namespace FacturaScripts\Plugins\EjemploHtmlView\Controller;

use FacturaScripts\Core\Lib\ExtendedController\ListController;

class ListAcademiaClase extends ListController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data["title"] = "classes";
        $data["menu"] = "Academy";
        $data["icon"] = "fas fa-calendar";
        return $data;
    }

    protected function createViews()
    {
        $this->createViewsAcademiaClase();
    }

    protected function createViewsAcademiaClase(string $viewName = "ListAcademiaClase")
    {
        $this->addView($viewName, "AcademiaClase", "classes", "fas fa-calendar");
        $this->addOrderBy($viewName, ["instructor"], "instructor");
        $this->addOrderBy($viewName, ["name"], "name", 2);
        $this->addSearchFields($viewName, ["instructor", "name"]);
    }
}
