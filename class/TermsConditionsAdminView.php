<?php

class TermsConditionsAdminView extends hms\View{

    private $term;

    public function __construct(Term $term)
    {
        $this->term = $term;
    }

    public function show()
    {
        $vars = array();

        $submitCmd = CommandFactory::getCommand('SaveTermSettings');
        
        $form = new PHPWS_Form('docusign');
        $submitCmd->initForm($form);


        // Over 18 template        
        $existingTemplate = '';
        
        try {
        	$existingTemplate = $this->term->getDocusignTemplate();
        } catch (InvalidConfigurationException $e) {
        	NQ::simple('hms', HMS_NOTIFICATION_WARNING, 'No DocuSign template id has been set for students over 18.');
        }
        
        $form->addText('template', $existingTemplate);
        $form->setSize('termplate', 33);

        
        // Under 18 template
        $under18Template = '';
        
        try{
        	$under18Template = $this->term->getDocusignUnder18Template();
        } catch (InvalidConfigurationException $e) {
            NQ::simple('hms', HMS_NOTIFICATION_WARNING, 'No DocuSign template id has been set for students under 18.');
        }
        
        $form->addText('under18_template', $under18Template);
        $form->setSize('under18_template', 33);
        
        
        $form->addSubmit('Save');
        $tpl = $form->getTemplate();
        
        return PHPWS_Template::process($tpl, 'hms', 'admin/TermsConditionsAdminView.tpl');
    }
}

?>
