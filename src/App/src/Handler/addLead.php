<?php

declare(strict_types=1);

namespace App\Handler;

use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;

class addLead implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $clientId = "40dae47c-c10b-4089-8169-abf705b2a8c8";
        $clientSecret = "IapnWJhgG5B39cRFXYg5lfWTXi2Cvm3BQBvOCaj9nZzDJSF2ZY5FSMyEBVny5Kfc";
        $redirectUri = "https://6911-178-215-72-130.ngrok-free.app/addLead";
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        if (isset($_GET['referer'])) {
            $apiClient->setAccountBaseDomain($_GET['referer']);
        }
        
        if (!isset($_GET['code'])) {
            $_SESSION['name'] = $_GET['name'];
            $_SESSION['mail'] = $_GET['mail'];
            $_SESSION['number'] = $_GET['number'];
            $_SESSION['price'] = $_GET['price'];

            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;
                $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
                die;
            
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            
            unset($_SESSION['oauth2state']);
            exit($_GET['state']. " ".$_SESSION['oauth2state'].'  nvalid state1');
        }
        try {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);
        } catch (\Exception $e) {
            die((string)$e);
        }
        $apiClient->setAccessToken($accessToken);

        if (isset($_GET['referer'])) {
            $apiClient->setAccountBaseDomain($_GET['referer']);
        }
        $leadsService = $apiClient->leads();
        $contactCollection = new ContactsCollection();
        $contact = new ContactModel();
        $customFields = new CustomFieldsValuesCollection();
        $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
        $phoneField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORKDD')
                        ->setValue($_SESSION['number'])
                )
        );
        $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
        $emailField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORK')
                        ->setValue($_SESSION['mail'])
                )
        );
        $customFields->add($phoneField);
        $customFields->add($emailField);
        $contact->setCustomFieldsValues($customFields);
        $contact->setName($_SESSION['name']);
        $contactCollection->add($contact);
        $lead = new LeadModel();
        $lead
            ->setPrice((int) $_SESSION['price'])
            ->setContacts(
                $contactCollection
            );
        try {
            $leadsCollection = $leadsService->addOneComplex($lead);
        } catch (AmoCRMApiException $e) {
            exit($e);
        }
        unset($_SESSION['name']);
        unset($_SESSION['price']);
        unset($_SESSION['mail']);
        unset($_SESSION['number']);
        
        return new RedirectResponse("/amo");
    }
}
