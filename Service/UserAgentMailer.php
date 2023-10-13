<?php namespace Hampel\KnownBots\Service;

use XF\Service\AbstractService;

class UserAgentMailer extends AbstractService
{
	protected $toEmail = [];
	protected $agents = [];

	public function setToEmail(array $email)
	{
		$this->toEmail = $email;
	}

	public function setUserAgents(array $agents)
	{
		$this->agents = $agents;
	}

	public function mailUserAgents()
	{
        $version = $this->app->finder('XF:AddOn')->whereId('Hampel/KnownBots')->fetchOne()->version_string;

        $botList = '';
        foreach ($this->agents as $bot)
        {
            $botList .= "<li>{$bot}</li>" . PHP_EOL;
        }

        $mail = $this->app->mailer()->newMail();
        $mail->setTo($this->toEmail);
        $mail->setContent(
            \XF::phrase('hampel_knownbots_email_subject', compact('version'))->render('raw'),
            "<ul>" . PHP_EOL . $botList . PHP_EOL . "</ul>" . PHP_EOL
        );
        return $mail->queue();
	}
}
