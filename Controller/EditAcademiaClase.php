<?php
namespace FacturaScripts\Plugins\EjemploHtmlView\Controller;

use FacturaScripts\Core\Lib\ExtendedController\EditController;
use FacturaScripts\Plugins\EjemploHtmlView\Model\AcademiaClaseHorario;

class EditAcademiaClase extends EditController
{
    public function getModelClassName(): string
    {
        return "AcademiaClase";
    }

    public function getSchedule(): array
    {
        $model = $this->getModel();
        $code = $this->request->get('code');
        if (empty($code) || null === $model->find($code)) {
            return [];
        }

        return $model->getSchedule();
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data["title"] = "clase";
        $data["menu"] = "academia";
        $data["icon"] = "fas fa-calendar";
        return $data;
    }

    public function renderLine(AcademiaClaseHorario $line, $idlinea): string
    {
        $html = '<div class="line line_' . $idlinea . ' card shadow mb-2">';
        $html .= '<input type="hidden" class="idhour" name="idhour_' . $idlinea . '" value="' . (empty($line->id) ? '' : $line->id) . '">';
        $html .= '<div class="card-body"><div class="form-row">';
        
        // día
        $html .= '<div class="col-md"><div class="form-group">';
        $html .= '<label class="form-label">Día</label>';
        $html .= '<select name="day_' . $idlinea . '" class="form-control" required>';
        $html .= '<option value="1" ' . (($line->day ?? 0) == 1 ? 'selected' : '') . '>Lunes</option>';
        $html .= '<option value="2" ' . (($line->day ?? 0) == 2 ? 'selected' : '') . '>Martes</option>';
        $html .= '<option value="3" ' . (($line->day ?? 0) == 3 ? 'selected' : '') . '>Miércoles</option>';
        $html .= '<option value="4" ' . (($line->day ?? 0) == 4 ? 'selected' : '') . '>Jueves</option>';
        $html .= '<option value="5" ' . (($line->day ?? 0) == 5 ? 'selected' : '') . '>Viernes</option>';
        $html .= '<option value="6" ' . (($line->day ?? 0) == 6 ? 'selected' : '') . '>Sábado</option>';
        $html .= '<option value="0" ' . (($line->day ?? 0) == 0 ? 'selected' : '') . '>Domingo</option>';
        $html .= '</select></div></div>';
        
        // hora inicio
        $html .= '<div class="col-md"><div class="form-group">';
        $html .= '<label class="form-label">Desde</label>';
        $html .= '<input type="time" name="hourstart_' . $idlinea . '" class="form-control" value="' . (empty($line->hourstart) ? '' : $line->hourstart) . '" required>';
        $html .= '</div></div>';
        
        // hora fin
        $html .= '<div class="col-md"><div class="form-group">';
        $html .= '<label class="form-label">Hasta</label>';
        $html .= '<input type="time" name="hourend_' . $idlinea . '" class="form-control" value="' . (empty($line->hourend) ? '' : $line->hourend) . '" required>';
        $html .= '</div></div>';
        
        // botón eliminar
        $html .= '<div class="col-md-1 d-flex align-items-end"><div class="form-group">';
        $html .= '<button type="button" class="btn btn-block btn-danger" onclick="return deleteHour(\'line_' . $idlinea . '\')">Eliminar</button>';
        $html .= '</div></div>';
        
        $html .= '</div></div></div>';
        return $html;
    }

    public function renderLines(array $lines): string
    {
        $html = '';
        foreach ($lines as $line) {
            $html .= $this->renderLine($line, $line->id);
        }

        return $html;
    }

    protected function addSchedule(): bool
    {
        try {
            $lastLine = $this->request->request->get('last_line', '0');
            $number = (int) str_replace('idhour_n', '', $lastLine);
            $idlinea = 'n' . ($number + 1);
            
            $content = [
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'newline' => $this->renderLine(new AcademiaClaseHorario(), $idlinea),
                'messages' => []
            ];
            header('Content-Type: application/json');
            echo json_encode($content);
            die();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'messages' => [],
                'error' => $e->getMessage()
            ]);
            die();
        }
    }

    protected function createViews()
    {
        parent::createViews();
        $this->addHTMLView('EditAcademiaClaseHorario', 'EditAcademiaClaseHorario', 'AcademiaClaseHorario', 'schedule', 'fas fa-clock');
        $this->setTabsPosition('bottom');
    }

    protected function execPreviousAction($action): bool
    {
        switch ($action) {
            case 'add-schedule':
                return $this->addSchedule();
            case 'update-schedule':
                return $this->updateSchedule();
        }

        return parent::execPreviousAction($action);
    }

    protected function updateSchedule(): bool
    {
        try {
            // comprobamos que existe la clase
            $data = $this->request->request->get('data');
            $formData = json_decode($data, true);
            $modelClass = $this->getModelClassName();
            $modelClass = 'FacturaScripts\\Plugins\\EjemploHtmlView\\Model\\' . $modelClass;
            $model = $modelClass::find($formData['idclase']);
            if (null === $model) {
                header('Content-Type: application/json');
                echo json_encode([
                    'multireqtoken' => $this->multiRequestProtection->newToken(),
                    'messages' => [],
                    'error' => 'Model not found'
                ]);
                die();
            }

            $this->dataBase->beginTransaction();

            // eliminamos o actualizamos los registros existentes
            foreach ($model->getSchedule() as $schedule) {
                // si no existe el id lo eliminamos
                if (false === isset($formData['idhour_' . $schedule->id])) {
                    if (false === $schedule->delete()) {
                        $this->dataBase->rollback();
                        header('Content-Type: application/json');
                        echo json_encode([
                            'multireqtoken' => $this->multiRequestProtection->newToken(),
                            'messages' => [],
                            'error' => 'Error deleting schedule'
                        ]);
                        die();
                    }
                }

                // si existe lo actualizamos
                if (isset($formData['idhour_' . $schedule->id])) {
                    $schedule->day = $formData['day_' . $schedule->id];
                    $schedule->hourstart = $formData['hourstart_' . $schedule->id];
                    $schedule->hourend = $formData['hourend_' . $schedule->id];
                    if (false === $schedule->save()) {
                        $this->dataBase->rollback();
                        header('Content-Type: application/json');
                        echo json_encode([
                            'multireqtoken' => $this->multiRequestProtection->newToken(),
                            'messages' => [],
                            'error' => 'Error saving schedule'
                        ]);
                        die();
                    }
                }
            }

            // añadimos los nuevos registros
            foreach ($formData as $key => $value) {
                if (false !== strpos($key, 'idhour_n')) {
                    $id = str_replace('idhour', '', $key);
                    $schedule = new AcademiaClaseHorario();
                    $schedule->idclase = $formData['idclase'];
                    $schedule->day = $formData['day' . $id];
                    $schedule->hourstart = $formData['hourstart' . $id];
                    $schedule->hourend = $formData['hourend' . $id];
                    if (false === $schedule->save()) {
                        $this->dataBase->rollback();
                        header('Content-Type: application/json');
                        echo json_encode([
                            'multireqtoken' => $this->multiRequestProtection->newToken(),
                            'messages' => [],
                            'error' => 'Error adding schedule'
                        ]);
                        die();
                    }
                }
            }

            $this->dataBase->commit();
            $content = [
                'success' => true,
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'url' => $model->url('edit') . '?code=' . $formData['idclase'] . '&action=save-ok',
                'messages' => []
            ];
            header('Content-Type: application/json');
            echo json_encode($content);
            die();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'messages' => [],
                'error' => $e->getMessage()
            ]);
            die();
        }
    }

    protected function validate(): bool
    {
        return true;
    }
}