<?php
namespace FacturaScripts\Plugins\EjemploHtmlView\Controller;

use FacturaScripts\Core\Lib\ExtendedController\EditController;
use FacturaScripts\Plugins\EjemploHtmlView\Model\AcademiaClaseHorario;

class EditAcademiaClase extends EditController
{
    private $logLevels = ['critical', 'error', 'info', 'notice', 'warning'];

    public function getModelClassName(): string
    {
        return "AcademiaClase";
    }

    public function getSchedule(): array
    {
        $model = $this->getModel();
        if (false === $model->loadFromCode($this->request->get('code'))) {
            return [];
        }

        return $model->getSchedule();
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data["title"] = "class";
        $data["icon"] = "fas fa-calendar";
        return $data;
    }

    public function renderLine(AcademiaClaseHorario $line, $idlinea): string
    {
        $i18n = $this->toolBox()->i18n();
        return '<div class="line line_' . $idlinea . ' card shadow mb-2">'
            . '<input type="hidden" class="idhour" name="idhour_' . $idlinea . '" value="' . $line->id . '">'
            . '<div class="card-body">'
            . '<div class="form-row">'
            . '<div class="col-md">'
            . '<div class="form-group">'
            . '<label class="form-label">' . $i18n->trans('day') . '</label>'
            . '<select name="day_' . $idlinea . '" class="form-control" required>'
            . '<option value="1" ' . ($line->day === 1 ? 'selected' : '') . '>' . $i18n->trans('monday') . '</option>'
            . '<option value="2" ' . ($line->day === 2 ? 'selected' : '') . '>' . $i18n->trans('tuesday') . '</option>'
            . '<option value="3" ' . ($line->day === 3 ? 'selected' : '') . '>' . $i18n->trans('wednesday') . '</option>'
            . '<option value="4" ' . ($line->day === 4 ? 'selected' : '') . '>' . $i18n->trans('thursday') . '</option>'
            . '<option value="5" ' . ($line->day === 5 ? 'selected' : '') . '>' . $i18n->trans('friday') . '</option>'
            . '<option value="6" ' . ($line->day === 6 ? 'selected' : '') . '>' . $i18n->trans('saturday') . '</option>'
            . '<option value="0" ' . ($line->day === 0 ? 'selected' : '') . '>' . $i18n->trans('sunday') . '</option>'
            . '</select>'
            . '</div>'
            . '</div>'
            . '<div class="col-md">'
            . '<div class="form-group">'
            . '<label class="form-label">' . $i18n->trans('from-hour') . '</label>'
            . '<input type="time" name="hourstart_' . $idlinea . '" class="form-control" value="' . $line->hourstart . '" required>'
            . '</div>'
            . '</div>'
            . '<div class="col-md">'
            . '<div class="form-group">'
            . '<label class="form-label">' . $i18n->trans('until-end') . '</label>'
            . '<input type="time" name="hourend_' . $idlinea . '" class="form-control" value="' . $line->hourend . '" required>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-1 d-flex align-items-end">'
            . '<div class="form-group">'
            . '<button type="button" class="btn btn-block btn-danger" onclick="return deleteHour(\'line_' . $idlinea . '\')">' . $i18n->trans('delete') . '</button>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
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
        $this->setTemplate(false);

        if (false === $this->validate()) {
            $this->response->setContent(json_encode([
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'messages' => self::toolBox()::log()::read('', $this->logLevels)
            ]));
            return false;
        }

        $number = str_replace('idhour_n', '', $this->request->request->get('last_line'));
        $idlinea = 'n' . ((int)$number + 1);
        $content = [
            'multireqtoken' => $this->multiRequestProtection->newToken(),
            'newline' => $this->renderLine(new AcademiaClaseHorario(), $idlinea),
            'messages' => self::toolBox()::log()::read('', $this->logLevels)
        ];
        $this->response->setContent(json_encode($content));
        return false;
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
        $this->setTemplate(false);

        if (false === $this->validate()) {
            $this->response->setContent(json_encode([
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'messages' => self::toolBox()::log()::read('', $this->logLevels)
            ]));
            return false;
        }

        // comprobamos que existe la clase
        $formData = json_decode($this->request->request->get('data'), true);
        $model = $this->getModel();
        if (false === $model->loadFromCode($formData['idclase'])) {
            $this->response->setContent(json_encode([
                'multireqtoken' => $this->multiRequestProtection->newToken(),
                'messages' => self::toolBox()::log()::read('', $this->logLevels)
            ]));
            return false;
        }

        $this->dataBase->beginTransaction();

        // eliminamos o actualizamos los registros existentes
        foreach ($model->getSchedule() as $schedule) {
            // si no existe el id lo eliminamos
            if (false === isset($formData['idhour_' . $schedule->id])) {
                if (false === $schedule->delete()) {
                    $this->toolBox()->i18nLog()->warning('record-deleted-error');
                    $this->dataBase->rollback();
                    return false;
                }
            }

            // si existe lo actualizamos
            if (isset($formData['idhour_' . $schedule->id])) {
                $schedule->day = $formData['day_' . $schedule->id];
                $schedule->hourstart = $formData['hourstart_' . $schedule->id];
                $schedule->hourend = $formData['hourend_' . $schedule->id];
                if (false === $schedule->save()) {
                    $this->toolBox()->i18nLog()->warning('record-updated-error');
                    $this->dataBase->rollback();
                    return false;
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
                    $this->toolBox()->i18nLog()->warning('record-updated-error');
                    $this->dataBase->rollback();
                    return false;
                }
            }
        }

        $this->dataBase->commit();
        $content = [
            'success' => true,
            'multireqtoken' => $this->multiRequestProtection->newToken(),
            'url' => $model->url('edit') . '?code=' . $formData['idclase'] . '&action=save-ok',
            'messages' => self::toolBox()::log()::read('', $this->logLevels)
        ];
        $this->response->setContent(json_encode($content));
        return false;
    }

    protected function validate(): bool
    {
        if (false === $this->permissions->allowUpdate) {
            $this->toolBox()->i18nLog()->warning('not-allowed-modify');
            return false;
        } elseif (false === $this->validateFormToken()) {
            return false;
        }

        return true;
    }
}