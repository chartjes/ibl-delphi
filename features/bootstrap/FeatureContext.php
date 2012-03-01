<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I call "([^"]*)"$/
     */
    public function iCall($argument1)
    {
        $client = new Guzzle\Service\Client();
        $request = $client->get('http://local.ibl-delphi' . $argument1)->send();
        $this->response = $request->getBody(true);
    }

    /**
     * @Then /^I get a response$/
     */
    public function iGetAResponse()
    {
        if (empty($this->response)) {
            throw new Exception('Did not get a response from the API');
        }
    }

    /**
     * @Given /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->response);

        if (empty($data)) {
            throw new Exception("Response was not JSON\n" . $this->response);
        }
    }

    /**
     * @Given /^the response contains at least one transaction$/
     */
    public function theResponseContainsAtLeastOneTransaction()
    {
        $data = json_decode($this->response);

        if (count($data) < 1) {
            throw new Exception("Response did not contain at least one transaction");
        }
    }    /**
   
    /**
     * @Given /^the first transaction contains a transaction ID$/
     */
    public function theFirstTransactionContainsATransactionId()
    {
        $data = json_decode($this->response, true);
        $transaction = $data[0];

        if (!isset($transaction['id'])) {
            throw new Exception("First transaction did not contain a transaction id");
        }
    }

    /**
     * @Given /^the first transaction contains two teams$/
     */
    public function theFirstTransactionContainsTwoTeams()
    {
        $data = json_decode($this->response, true);
        $transaction = $data[0];

        if (!isset($transaction['tradePartner1']) && !isset($transaction['tradePartner2'])) {
            throw new Exception("First transaction did not contain two teams");
        }
    }

     /**
     * @Given /^the first transaction contains a description$/
     */
    public function theFirstTransactionContainsADescription()
    {
        $data = json_decode($this->response, true);
        $transaction = $data[0];

        if (!isset($transaction['description'])) {
            throw new Exception("First transaction is missing a description");
        }
    }
}
