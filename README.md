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
    
or 

composer require vsavritsky/morpherbundle

Add the AnchovyCURLBundle and VsavritskyMorpherBundle to your application's kernel:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Anchovy\CURLBundle\AnchovyCURLBundle(),
            new Vsavritsky\MorpherBundle\VsavritskyMorpherBundle(),
            ...
        );

## Usage ##

	// Simple call:

	    public function indexAction() {

      $morpher = $this->get('vsavritsky_morpher.request');
      $result = $morpher->inflect('Тест');
      echo '<pre>';
      print_r($result); exit();
      exit();
		}
