# MorpherBundle #

## Installation ##

Require the `vsavritsky/morpherbundle` in your composer.json and update your dependencies.
composer require vsavritsky/morpherbundle

    {
        "require": {
           ...
           "vsavritsky/morpherbundle": "dev-master"
           ...
        }
    }

Add the AnchovyCURLBundle and VsavritskyMorpherBundle to your application's kernel:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Anchovy\CURLBundle\AnchovyCURLBundle(),
            new Vsavritsky\MorpherBundle\VsavritskyMorpherBundle(),
            ...
        );

add parameters access to parameters.yml:

    vsavritsky_morpher.access.username: username
    vsavritsky_morpher.access.pass: pass

update db:
app/console doctrine:schema:update --force

## Usage ##

	// Simple call:
	    use Vsavritsky\MorpherBundle\Entity\RequestFacade;
	    ...
	    public function indexAction() {

      		$morpher = $this->get('vsavritsky_morpher.request');
      		$result = $morpher->inflect('Тест', RequestFacade::CASE_ROD, 'Дефолтное значение');
		echo '<pre>';
	      	print_r($result); exit();
	      	exit();
	    }

consts:

    `const CASE_ROD = 'Р';`
    `const CASE_DAT = 'Д';`
    `const CASE_VIN = 'В';`
    `const CASE_TVOR = 'Т';`
    `const CASE_PREDL = 'П';`
    `const CASE_GDE = 'М';`

`result: 'Тесту'`
`
