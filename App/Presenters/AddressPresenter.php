<?php
declare(strict_types = 1);

namespace App\Presenters;

use App\Controls\Form\FormFactory;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Utils\Strings;
use Tracy\Debugger;

class AddressPresenter extends Presenter
{
    private const RECAPTCHA_PUBLIC_KEY = "6LeEmeEZAAAAAOQhYsrauKYfa_nXndjlH0rfAN_V";
    private const RECAPTCHA_SECRET_KEY = "6LeEmeEZAAAAAPY3FWuzoA5zfWZNISEt2q1c5R0c";
    private const RECAPTCHA_POST_FIELD = "g-recaptcha-response";
    private const RECAPTCHA_VERIFY_URL = "https://www.google.com/recaptcha/api/siteverify";

    /**
     * @persistent
     */
    public int $addressId;

    private ?array $addressRow;

    private EntityManagerInterface $em;

    private FormFactory $formFactory;

    private Mailer $mailer;

    public function __construct(EntityManagerInterface $em, FormFactory $formFactory, Mailer $mailer)
    {
        parent::__construct();
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->mailer = $mailer;
    }

    protected function startup()
    {
        parent::startup();

        $this->addressRow = $this->getAddressRow();
        if (null === $this->addressRow) {
            $this->error("Address #$this->addressId not found!");
        }
        // Session warmup
        $this->getSession()->getId();
    }

    public function actionDefault()
    {
        if ($this->isAjax()) {
            $this->redrawControl("mail");
        }
    }

    public function renderDefault(): void
    {
        $this->template->setParameters([
            "address" => $this->addressRow,
        ]);
    }

    private function getAddressRow(): ?array
    {
        $sql = <<<SQL
SELECT * FROM address WHERE address_id = ?
SQL;

        $row = $this->em->getConnection()->executeQuery(
            $sql,
            [$this->addressId]
        )->fetchAssociative();

        if (!$row) {
            return null;
        }

        return $row;
    }

    protected function createComponentMail(): Form
    {
        $form = $this->formFactory->create();
        $form->getElementPrototype()->id("addressEmail");

        $form->addEmail("email", "Sdílet")
            ->setHtmlAttribute("placeholder", "Zadejte e-mailovou adresu")
            ->setRequired("Zadejte e-mailovou adresu!");

        $form->addSubmit("send", "Sdílet")
            ->setHtmlAttribute("class", "g-recaptcha")
            ->setHtmlAttribute("data-sitekey", self::RECAPTCHA_PUBLIC_KEY)
            ->setHtmlAttribute("data-callback", "onSubmit")
            ->setHtmlAttribute("data-action", "submit")
            ->setHtmlAttribute("data-badge", "bottomleft");

        $form->onValidate[] = function (Form $form): void {
            if ("localhost" == $this->getHttpRequest()->getRemoteHost()) {
                return;
            }

            $recaptchaResponse = $this->getHttpRequest()->getPost(self::RECAPTCHA_POST_FIELD);
            $guzzle = new Client();
            try {
                $response = $guzzle->post(self::RECAPTCHA_VERIFY_URL, [
                    "form_params" => [
                        "secret" => self::RECAPTCHA_SECRET_KEY,
                        "response" => $recaptchaResponse,
                        "remoteip" => $this->getHttpRequest()->getRemoteAddress(),
                    ],
                ]);
            } catch (GuzzleException $e) {
                $form->addError("Nepodařilo se ověřit, že nejste robot!");
                Debugger::log($e, Debugger::EXCEPTION);

                return;
            }
            $postResult = $response->getBody()->getContents();

            $response = @json_decode($postResult, true);
            if (null === $response) {
                $form->addError("Nepodařilo se ověřit jestli jste robot!");

                return;
            }
            if (false === ($response["success"] ?? false)) {
                $form->addError("Vypadá to, že jste robot :P");
                $form->addError($postResult);
            }
        };

        $form->onSuccess[] = function (Form $form): void {
            if ($this->isAjax()) {
                $form->reset();
            }
            $email = $form->getValues()->email;

            $message = new Message();
            $message->addTo($email);
            $message->setFrom("info@hqm.cz");
            $message->setSubject("TEST: Informace o adrese #" . $this->addressId);
            $body = [
                "<p>Informace o adrese #" . $this->addressId . "</p>",
                "<p>Detail adresy zde: <a href='" . $this->link("//this") . "'>" . $this->link("//this") . "</a></p>",
                "<br/>",
                "<p>...ve formátu \"fuj dumpu\" z databáze :P</p>",
                "<br>",
            ];
            $body[] = "<table>";
            foreach ($this->getAddressRow() as $key => $val) {
                $body[] = "<tr>";
                $body[] = "<td>$key</td><td>$val</td>";
                $body[] = "</tr>";
            }
            $body[] = "</table>";
            $message->setHtmlBody(implode("\n", $body));

            $this->mailer->send($message);

            $this->flashMessage("Adresa byla odeslána e-mailem :)", "success");
            if ($this->isAjax()) {
                $this->redrawControl("flashes");
            } else {
                $this->redirect("this");
            }
        };

        return $form;
    }
}