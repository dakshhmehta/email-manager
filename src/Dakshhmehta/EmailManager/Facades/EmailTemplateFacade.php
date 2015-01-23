<?php namespace Dakshhmehta\EmailManager\Facades;

use Illuminate\Support\Facades\Facade;

class EmailTemplateFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'email.template'; }

}