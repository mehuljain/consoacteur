<?php

namespace GD\SiteBundle\Mailer;

class Mailer 
{
    protected $mailer;
    protected $twig;

    public function __construct($mailer, $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

  /**
   *
   * @param array $renderedTemplate
   * @param string $fromEmail
   * @param string $toEmail 
   */
  public function sendEmailMessage($renderedTemplate,$context, $fromEmail, $toEmail,$cc= array(), $bcc = array()) {
    // Render the email, use the first line as the subject, and the rest as the body
    $template = $this->twig->loadTemplate($renderedTemplate);
    $subject = $template->renderBlock('subject', $context);        
    $textBody = $template->renderBlock('body_text', $context);
    $htmlBody = $template->renderBlock('body_html', $context);

    $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

    if(!empty($bcc)){
      $message->setBcc($bcc);      
    }
    
    if(!empty($cc)){
      $message->setCc($cc);
    }
      
    if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')->addPart($textBody,'text/plain');
    }else {
            $message->setBody($textBody);
    }
      


    $this->mailer->send($message);
  }
}
