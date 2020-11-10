<?php
declare(strict_types = 1);

namespace App\Presenters\Controls\Search;

use App\Controls\Form\FormFactory;
use App\Core\Model\Ares\AresFacade;
use App\Core\Model\Ares\Entities\AresResult;
use App\Core\Model\Ares\Exceptions\AresClientException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;
use Tracy\Debugger;

/**
 * @method onSuccess(?AresResult $result)
 */
class SearchFormControl extends Control
{
    private ?AresResult $aresResult;

    /**
     * @var callable[]
     */
    public array $onSuccess;

    private FormFactory $formFactory;

    private AresFacade $aresFacade;

    public function __construct(?AresResult $aresResult, FormFactory $formFactory, AresFacade $aresFacade)
    {
        $this->aresResult = $aresResult;

        $this->formFactory = $formFactory;
        $this->aresFacade = $aresFacade;
    }

    protected function createComponentAresForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText("ico", "IČO")
            ->addRule(Form::PATTERN, "Zadejte platné IČO!", "[0-9\s]+")
            ->setRequired("Zadejte IČO!")
            ->setHtmlAttribute("placeholder", "Zadejte IČO")
            ->setDefaultValue(null !== $this->aresResult ? $this->aresResult->getSearchedId() : null);

        $form->addSubmit("send", "Hledat v ARESu")->setHtmlAttribute("class", "btn-block");

        $form->onSuccess[] = function (Form $form): void {
            $searchedIco = $form->getValues(true)["ico"];
            $searchedIco = Strings::replace($searchedIco, "~\s~", "");
            try {
                $result = $this->aresFacade->search($searchedIco);
                $this->onSuccess($result);
            } catch (AresClientException $e) {
                Debugger::log($e, Debugger::EXCEPTION);
                $form->addError("Nepodařilo se zpracovat odpověď od systému ARES.");
            }
        };

        return $form;
    }

    public function render(): void
    {
        $this->template->render(__DIR__ . "/template.latte");
    }
}
