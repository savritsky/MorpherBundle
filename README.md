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

	    public function indexAction() {

      		$morpher = $this->get('vsavritsky_morpher.request');
      		$result = $morpher->inflect('Тест');
		echo '<pre>';
	      	print_r($result); exit();
	      	exit();
	    }

result: 

`
Array
(
    [Р] => Стола
    [Д] => Столу
    [В] => Стол
    [Т] => Столом
    [П] => Столе
    [П-о] => о Столе
    [род] => Мужской
    [множественное] => Array
        (
            [И] => Столы
            [Р] => Столов
            [Д] => Столам
            [В] => Столы
            [Т] => Столами
            [П] => Столах
            [П-о] => о Столах
        )
    [где] => в Столе
    [куда] => в Стол
    [откуда] => из Стола
)
`
